<?php


namespace ModifyRecord\Drivers;


use Illuminate\Support\Collection;
use ModifyRecord\ModifyRecord;

abstract class Driver
{
    protected $record;
    
    /**
     * @var Collection
     */
    protected $columnChanges;
    
    /**
     * @var Collection
     */
    protected $operateChanges;
    
    public function __construct(ModifyRecord $record)
    {
        $this->record = $record;
        
        $this->columnChanges = collect();
        
        $this->operateChanges = collect();
    }
    
    /**
     * @return mixed
     */
    abstract public function store();
    
    abstract public function setColumnChanges($key, $zhKey, $currentValue, $originalValue, $type);
    
    abstract public function setOperateChanges($type);
    
    /**
     * @return Collection
     */
    public function getColumnChanges()
    {
        return $this->columnChanges;
    }
    
    /**
     * @return Collection
     */
    public function getOperateChanges()
    {
        return $this->operateChanges;
    }
}
