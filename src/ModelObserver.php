<?php


namespace ModifyRecord;


class ModelObserver
{
    public function saved($model)
    {
        if ($model->exists) {
            $user = auth(config('modify_record.auth'))->user();
            if ($user) {
                app('record')->setModel($model)->storeColumn();
            }
        }
    }
    
    public function deleted($model)
    {
        $user = auth(config('modify_record.auth'))->user();
        if ($user) {
            app('record')->setModel($model)->storeOperate('删除数据');
        }
    }
}