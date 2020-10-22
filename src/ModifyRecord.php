<?php


namespace ModifyRecord;


use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use ModifyRecord\Drivers\Driver;
use ModifyRecord\Drivers\DriverConnect;
use ModifyRecord\Drivers\MysqlDriver;
use ModifyRecord\Events\RecordStoreEnd;
use ModifyRecord\Events\RecordStoreStart;
use ModifyRecord\Exceptions\FieldTypeNotFoundException;
use ModifyRecord\FieldTypes\BaseType;
use ModifyRecord\Contract\WithOriginData;
use ModifyRecord\Contract\WithExtraData;
use Throwable;

class ModifyRecord
{
    /**
     * @var Model
     */
    public $model;
    
    /**
     * 表映射
     * @var Mapping
     */
    protected $mapping;
    
    /**
     * @var RecordHandle
     */
    protected $handle;
    
    /**
     * @var Driver
     */
    protected $driver;
    
    /**
     * 当前值
     * @var Collection
     */
    protected $attributes;
    
    /**
     * 原始值
     * @var Collection
     */
    protected $originals;
    
    /**
     * @var bool
     */
    protected $hasChange = false;
    
    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var string
     */
    protected $comment;
    
    public function __construct(Model $model, Mapping $mapping, RecordHandle $handle)
    {
        $this->model = $model;
        $this->mapping = $mapping;
        $this->handle = $handle;
        $this->title = $this->mapping->title();
        $this->comment = $this->mapping->comment();
        $this->driver = $this->getDriver();
        
        $this->setAttributes();
    
        $this->setOriginals();
    }
    
    /**
     * 字段变更
     * @param string[] $keys
     * @throws FieldTypeNotFoundException
     * @throws Throwable
     */
    public function storeColumn($keys = ['*'])
    {
        $this->compareFields($keys);
        
        if ($this->hasChange) {
            $this->driver->setOperateChanges('Column');
            
            Event::dispatch(new RecordStoreStart($this->model, $this->driver->getColumnChanges()));
            
            $this->driver->store();
    
            Event::dispatch(new RecordStoreEnd($this->model, $this->driver->getColumnChanges()));
        }
    }
    
    /**
     * 操作记录
     * @param string $title
     * @param string|null $comment
     * @throws Throwable
     */
    public function storeOperate(string $title, string $comment = null)
    {
        $this->title = $title;
        $this->comment = $comment;
        
        $this->driver->setOperateChanges();
        
        $this->driver->store();
    }
    
    /**
     * 获取映射
     * @param array $keys
     * @return array|Collection
     */
    public function getMappings($keys = ['*'])
    {
        $keys = Arr::wrap($keys);
        
        if ($keys[0] == '*') {
            return $this->mapping->mapping();
        } else {
            return collect($this->mapping->mapping())->only($keys)->toArray();
        }
    }
    
    /**
     * 获取当前属性值
     * @param array $keys
     * @return array|Collection
     */
    public function getAttributes($keys = ['*'])
    {
        $keys = Arr::wrap($keys);
        
        if ($keys[0] == '*') {
            return $this->attributes;
        } else {
            return $this->attributes->only($keys)->toArray();
        }
    }
    
    /**
     * 获取原始属性值
     * @param string[] $keys
     * @return array|Collection
     */
    public function getOriginals($keys = ['*'])
    {
        $keys = Arr::wrap($keys);
    
        if ($keys[0] == '*') {
            return $this->originals;
        } else {
            return $this->originals->only($keys)->toArray();
        }
    }
    
    /**
     * 字段比较
     * @param $keys
     * @throws FieldTypeNotFoundException
     */
    protected function compareFields($keys)
    {
        foreach ($this->getMappings($keys) as $key => $value) {
            if ($this->attributes->has($key) && $this->originals->has($key)) {
                
                list($current, $original) = $this->getFieldData($key, $value->type);
                
                if ($this->isModified($current, $original)) {
                    
                    $this->hasChange = true;
                    
                    $this->driver->setColumnChanges(
                        $key,
                        $value->name,
                        $current,
                        $original,
                        $value->type
                    );
                }
            }
        }
    }
    
    /**
     * 判断字段是否修改过
     * @param $current
     * @param $original
     * @return bool
     */
    protected function isModified($current, $original): bool
    {
        return $current != $original;
    }
    
    /**
     * 获取字段值 当前值|原始值
     * @param $key
     * @param $type
     * @return array
     * @throws FieldTypeNotFoundException
     */
    protected function getFieldData($key, $type): array
    {
        if ($class = $this->handle->getConfig('fieldTypes')[$type] ?? null) {
            $fieldType = new $class();
            if ($fieldType instanceof BaseType) {
                $attribute = $fieldType->toString($this->attributes->get($key));
                $original = $fieldType->toString($this->originals->get($key));
                
                return [$attribute, $original];
            }
        }
        
        throw new FieldTypeNotFoundException("Type {$type} Not Found");
    }
    
    /**
     * 获取当前值
     */
    protected function setAttributes()
    {
        $this->attributes = collect($this->model->getAttributes());
        if ($this->mapping instanceof WithExtraData) {
            foreach ($this->mapping->extraData() as $key => $value) {
                $this->attributes->offsetSet($key, $value);
            }
        }
    }
    
    /**
     * 获取原始值
     */
    protected function setOriginals()
    {
        $this->originals = collect($this->model->getOriginal());
        if ($this->mapping instanceof WithOriginData) {
            foreach ($this->mapping->originData() as $key => $value) {
                $this->originals->offsetSet($key, $value);
            }
        }
    }
    
    /**
     * @return mixed|MysqlDriver
     */
    public function getDriver()
    {
        $connect = new DriverConnect($this);
        
        return $connect->getDriver();
    }
    
    /**
     * @return RecordHandle
     */
    public function getHandle()
    {
        return $this->handle;
    }
    
    /**
     * @return Mapping
     */
    public function getMapping()
    {
        return $this->mapping;
    }
    
    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }
    
    /**
     * 获取操作人
     * @return int
     */
    public function getOperator()
    {
        if ($operator = $this->handle->getOperator()) {
            if (is_integer($operator)) {
                return $operator;
            }
            return $operator->id;
        }
        
        return auth($this->handle->getConfig('auth'))->id();
    }
}
