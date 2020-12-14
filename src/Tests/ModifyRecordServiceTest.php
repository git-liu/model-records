<?php


namespace ModifyRecord\Tests;


use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ModifyRecord\ModifyRecord;
use ModifyRecord\RecordHandle;
use Tests\TestCase;

class ModifyRecordServiceTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testRecordHandle()
    {
        $service = app('record');
        
        $this->assertTrue($service instanceof RecordHandle);
    }
    
    public function testRecord()
    {
        $service = app('record');
        
        $record = $service->setModel(User::first());
        
        $this->assertTrue($record instanceof ModifyRecord);
    }
    
    public function testOperateRecord()
    {
        $user = User::first();
    
        $this->actingAs($user, 'api');
        
        $service = app('record');
        
        $service->setModel($user)->storeOperate('操作记录', '测试', 'operate');
        
        $this->assertDatabaseHas('tb_logs', [
            'table_name' => 'users',
            'table_id' => 1,
            'title' => '操作记录',
            'comment' => '测试',
            'modify_type' => 'Operate',
            'origin_table_name' => 'users',
            'origin_table_id' => $user->id,
            'model' => User::class
        ])->assertDatabaseHas('tb_log_contents', [
            'tb_key' => 'operate',
            'tb_zh_key' => '操作',
            'current_tb_value' => '操作记录'
        ]);
    }
    
    public function testRecordStore()
    {
        $user = User::first();
    
        $this->actingAs($user, 'api');
        
        $user->name = 'llgg';
        $user->save();
        
        $this->assertDatabaseHas('tb_logs', [
            'table_name' => 'users',
            'table_id' => 1,
            'modify_type' => 'Column',
            'account_id' => $user->id
        ]);
        
        $this->assertDatabaseHas('tb_log_contents', [
            'tb_key' => 'name',
            'current_tb_value' => 'llgg'
        ]);
    }
    
    public function testRecordStoreWithMapping()
    {
        $user = User::first();
    
        $this->actingAs($user, 'api');
        
        $response = $this->post('api/records/test', [
            'testColumn' => '111',
            'testField' => '222'
        ]);
        
        $response->assertJson([
            'code' => 1,
            'msg' => '成功',
            'result' => ''
        ]);
        
        $this->assertDatabaseHas('tb_log_contents', [
            'tb_key' => 'testColumn',
            'current_tb_value' => '111',
            'tb_value' => '123'
        ]);
        
        $this->assertDatabaseHas('tb_log_contents', [
            'tb_key' => 'testField',
            'current_tb_value' => '222',
            'tb_value' => '456'
        ]);
    }
    
    public function testRecordList()
    {
        $user = User::first();
    
        $this->actingAs($user, 'api');
        
        $response = $this->get('api/records?table_name=users&table_id=1&page_size=1');
        
        echo json_encode($response->decodeResponseJson(), true);
        
        $response->assertStatus(200);
    }
}
