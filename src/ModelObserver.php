<?php


namespace ModifyRecord;


class ModelObserver
{
    public function saving($model)
    {
        if ($model->exists) {
            $user = RecordHandle::getOperator();
            if ($user) {
                app('record')->setModel($model)->storeColumn();
            }
        }
    }
    
    public function deleted($model)
    {
        $user = RecordHandle::getOperator();
        if ($user) {
            app('record')->setModel($model)->storeOperate('删除数据');
        }
    }
}
