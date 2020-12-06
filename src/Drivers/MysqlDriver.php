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
        $this->columnChanges->push([
            'tb_key' => $key,
            'tb_zh_key' => $zhKey,
            'current_tb_value' => $currentValue,
            'tb_value' => $originalValue,
            'field_type' => $type,
            'model' => get_class($this->record->model),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * @param string $type
     */
    public function setOperateChanges($type = 'Operate')
    {
        $logClass = $this->record->getHandle()->getConfig('log');
        
        $this->operateChanges->push(new $logClass([
            'account_id' => $this->record->getOperator(),
            'table_name' => $this->record->getMapping()->table(),
            'table_id' => $this->record->getMapping()->tableId(),
            'title' => $this->record->getTitle(),
            'comment' => $this->record->getComment(),
            'modify_type' => $type,
            'model' => get_class($this->record->model),
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
        
                    if ($logContents = $this->getColumnChanges()->toArray()) {
                        foreach ($logContents as &$content) {
                            $content['log_id'] = $log->id;
                        }
                        $contentClass = $this->record->getHandle()->getConfig('logContent');
                        $contentModel = new $contentClass;
                        $contentModel->insert($logContents);
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
