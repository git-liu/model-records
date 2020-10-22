<?php


namespace ModifyRecord\Drivers;

use Illuminate\Support\Facades\DB;
use Throwable;

class MysqlDriver extends Driver
{
    
    /**
     * @param $key
     * @param $zhKey
     * @param $currentValue
     * @param $originalValue
     * @param $type
     */
    public function setColumnChanges($key, $zhKey, $currentValue, $originalValue, $type)
    {
        $logContent = $this->record->getHandle()->getConfig('logContent');
        
        $this->columnChanges->push(new $logContent([
            'tb_key' => $key,
            'tb_zh_key' => $zhKey,
            'current_tb_value' => $currentValue,
            'tb_value' => $originalValue,
            'field_type' => $type,
            'model' => get_class($this->record->model)
        ]));
    }
    
    /**
     * @param string $type
     */
    public function setOperateChanges($type = 'Operate')
    {
        $logClass = $this->record->getHandle()->getConfig('log');
        
        $this->operateChanges->push(new $logClass([
            'user_id' => $this->record->getOperator(),
            'table_name' => $this->record->getMapping()->table(),
            'table_id' => $this->record->getMapping()->tableId(),
            'title' => $this->record->getTitle(),
            'comment' => $this->record->getComment(),
            'modify_type' => $type,
            'origin_table_name' => $this->record->model->getTable(),
            'origin_table_id' => $this->record->model->getKey(),
        ]));
    }
    
    /**
     * @return mixed|void
     * @throws Throwable
     */
    public function store()
    {
        if ($logs = $this->getOperateChanges()) {
            DB::beginTransaction();
            try {
                foreach ($logs as $log) {
                    $log->save();
        
                    if ($logContents = $this->getColumnChanges()) {
                        $log->contents()->saveMany($logContents);
                    }
                }
                
                DB::commit();
            } catch (Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }
        }
    }
}
