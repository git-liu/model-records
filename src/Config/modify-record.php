<?php

return [

    /**
     * 变更记录表名称
     */
    'log' => \ModifyRecord\Models\TableLog::class,
    'logContent' => \ModifyRecord\Models\TableLogContent::class,

    /**
     * 操作用户，对应 auth.guards.api
     */
    'auth' => 'api',

    'fieldTypes' => [
        'integer' => \ModifyRecord\FieldTypes\IntegerType::class,
        'string' => \ModifyRecord\FieldTypes\StringType::class,
        'array' => \ModifyRecord\FieldTypes\ArrayType::class,
    ],

    /**
     * 设置Model映射
     *
     * [
     *      User::class => UserMapping::class
     * ]
     */
    'modelMappings' => [
    
    ],
    
    /**
     * 设置显示时需要用到的映射（数据挂载后可以通过此参数设置映射）
     * [
     *      User::class => [
     *          UserMapping::class,
     *          //
     *      ]
     * ]
     */
    'modelRouting' => [
    
    ]
];
