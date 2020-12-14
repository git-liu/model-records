<?php

namespace ModifyRecord\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class ModelChanged
{
    use Dispatchable, InteractsWithSockets;
    
    public $tbLog;
    
    public function __construct(Model $tbLog)
    {
        $this->tbLog = $tbLog;
    }
}
