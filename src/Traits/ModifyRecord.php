<?php


namespace ModifyRecord\Traits;

use ModifyRecord\ModelObserver;
use ModifyRecord\Models\TableLog;

trait ModifyRecord
{
    public static function bootModifyRecord()
    {
        static::observe(new ModelObserver());
    }
    
    // 变更记录
    public function modifyRecords()
    {
        return $this->hasMany(TableLog::class, 'table_id', 'id')
            ->where('table_name', $this->getTable());
    }
}
