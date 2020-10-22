<?php


namespace ModifyRecord;


class ModelObserver
{
    public function saved($model)
    {
        if ($model->exists) {
            $user = RecordHandle::getOperator() ?: auth(config('modify_record.auth'))->id();
            if ($user) {
                app('record')->setModel($model)->storeColumn();
            }
        }
    }
    
    public function deleted($model)
    {
        $user = RecordHandle::getOperator() ?: auth(config('modify_record.auth'))->id();
        if ($user) {
            app('record')->setModel($model)->storeOperate('删除数据');
        }
    }
}
