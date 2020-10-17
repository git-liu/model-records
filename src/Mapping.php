<?php


namespace ModifyRecord;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;

abstract class Mapping
{
    protected $model;
    
    /**
     * @var Request
     */
    protected $request;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
        
        $this->request = app('request');
    }
    
    /**
     * @return array
     */
    abstract public function mapping(): array ;
    
    /**
     * 设置字段
     * @param string $field
     * @param string $type
     * @param  bool $useMappingName
     * @return Field
     */
    public function set(string $field, string $type, bool $useMappingName = true): Field
    {
        return new Field($field, $type, $useMappingName);
    }
    
    /**
     * @return string
     */
    public function table(): string
    {
        return $this->model->getTable();
    }
    
    /**
     * @return int
     */
    public function tableId(): int
    {
        return $this->model->getKey();
    }
    
    /**
     * @return null
     */
    public function comment()
    {
        return null;
    }
    
    /**
     * @return null
     */
    public function title()
    {
        return null;
    }
}
