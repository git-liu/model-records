<?php


namespace ModifyRecord\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class RecordStoreStart
{
    use Dispatchable, InteractsWithSockets;
    
    private $model;
    
    private $records;
    
    public function __construct(Model $model, $records)
    {
        $this->model = $model;
        
        $this->records = $records;
    }
    
    public function getModel()
    {
        return $this->model;
    }
    
    public function getRecords()
    {
        return $this->records;
    }
}
