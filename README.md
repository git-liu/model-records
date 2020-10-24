###### 用途
针对表数据越来越多，表数据变更也越发麻烦。
写下了这个基于laravel的表数据变更记录模块。用于记录表字段变更过程（包含自定义数据，数据挂载，操作记录等功能）。
###### composer安装
composer require git-liu/model-records
###### 创建变更记录表
~~~json
php artisan migrate
执行完成后会创建 tb_logs，tb_log_contents 两张表用于保存数据
~~~
###### 发布配置文件
~~~json
php artisan vendor:publish --provider=ModifyRecord\ModifyRecordServiceProvider
~~~
###### 创建模型映射
~~~json
php artisan make:mapping UserMapping
映射类文件默认会创建到 app\\Mappings 目录
~~~
###### 模型映射配置
~~~php
<?php


namespace App\RecordMappings;


use ModifyRecord\Contract\WithEvent;      
use ModifyRecord\Contract\WithOriginData; 
use ModifyRecord\Contract\WithDataShow;   
use ModifyRecord\Contract\WithExtraData;  
use ModifyRecord\Events\RecordStoreEnd;
use ModifyRecord\Events\RecordStoreStart;
use ModifyRecord\Mapping;

class UserMapping extends Mapping implements 
    WithOriginData,  // 用于事件监听后的逻辑处理
    WithExtraData, // 用户设置原始字段值（针对多对多关联关系的变更需要手动设置原始值）
    WithDataShow, // 用于设置字段显示样式
    WithEvent // 用于设置额外的字段变更（针对request中传过来的非表字段）
{
    // 设置字段映射
    public function mapping(): array
    {
        return [
            'name' => $this->set('姓名', 'string'),
            'testColumn' => $this->set('测试字段一', 'string'),
            'testField' => $this->set('测试字段二', 'string')
        ];
    }
    
    // 设置备注
    public function comment(): string
    {
        return 'mark';
    }
    
    // 设置操作名称
    public function title(): string
    {
        return 'title';
    }

    // 设置数据需要挂载的表（默认是当前模型对应的表
    // 主要用于关联关系表变更后挂载到主表上显示）
    public function table(): string
    {
        return $this->model->getTable();
    }
    
    // 设置数据需要挂载的表id
    public function tableId(): int
    {
        return $this->model->getKey();
    }
    
    // 设置字段原始值
    public function originData(): array
    {
        return [
            'testColumn' => '123',
            'testField' => '456'
        ];
    }
    
    // 设置额外字段
    public function extraData(): array
    {
        return $this->request->only(['testColumn', 'testField']);
    }
    
    // 设置字段显示样式
    public function dataShow(): array
    {
        return [
            'name' => function ($value) {
                return $value . '- test';
            }
        ];
    }
    
    // 注册事件监听
    public function events(): array
    {
        return [
            RecordStoreStart::class => function ($event) {
                info($event->getModel());
            },
            RecordStoreEnd::class => function ($event) {
                info($event->getRecords());
            },
        ];
    }
}

~~~
###### 使用示例
~~~php
<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use ModifyRecord\Traits\ModifyRecord;
class User extends Model
{
    use ModifyRecord;  //  在 model 中使用变更记录 trait
    
    protected $table = "users";
    
}
~~~
###### 获取变更记录信息
~~~php
<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function userList()
    {
        $user = User::where('id',1)->first();
        $user->mobile = 66666666;
        $user->save();    //  保存用户信息
        
        //  或者使用 records 获取变更记录信息
        $user = User::where('id',1)->modifyRecords()->first()->toArray();
        
        return $user;
    }
}

// 返回用户信息
$user = [
    "id" => 1,
    "name" => "admin",
    "mobile" => "66666666",
    "modifyRecords" => [
        [
            "id" => 1,
            "table_name" => "users",                       //  表名
            "table_id" => 1,                               //  表id
            "operator" => [                                //  操作人
                "id" => 1,
                "name" => 'admin'
            ], 
            "title" => "Column Modified",                 // 操作名称
            "comment" => "test",                          // 备注  
            "modify_type" => "Column",                    // 变更类型
            "created_at" => "2018-11-19 03:11:47",
            "updated_at" => "2018-11-19 03:11:47",         
            "contents" => [                         //  变更记录具体信息
                [
                    "id" => 1,
                    "log_id" => 1,                          
                    "tb_key" => "mobile",                   //  变更字段
                    "tb_zh_key" => "手机号",                 //  变更字段中文名
                    "tb_value" => "88888888",               //  旧值
                    "current_tb_value" => "66666666",       //  新值
                    "field_type" => "string",               //  字段类型
                ] 
            ]
        ]
    ]
];
~~~
###### 变更记录路由
~~~php
返回指定{table_name}表中{table_id}的变更记录
/records?table_name=&table_id=  
~~~

