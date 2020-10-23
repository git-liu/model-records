<?php


namespace ModifyRecord;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use ModifyRecord\Contract\WithEvent;
use ModifyRecord\Exceptions\ModelMappingNotFoundException;

class RecordHandle
{
    /**
     * 配置
     * @var
     */
    protected $config;
    
    private static $operator;
    
    /**
     * RecordHandle constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = collect((array) $config);
    }
    
    /**
     * @param Model $model
     * @return ModifyRecord
     * @throws ModelMappingNotFoundException
     */
    public function setModel(Model $model)
    {
        $mapping = $this->getMappingByModel($model);
        
        return new ModifyRecord($model, $mapping, $this);
    }
    
    /**
     * 获取Mapping设置
     * @param Model $model
     * @return mixed
     * @throws ModelMappingNotFoundException|Exception
     */
    public function getMappingByModel($model)
    {
        $class = get_class($model);
        
        $modelMappings = $this->config->get('modelMappings');
        
        if (array_key_exists($class, $modelMappings)) {
            $mapping = $modelMappings[$class];
            if (! class_exists($mapping)) {
                throw new ModelMappingNotFoundException("{$class} Mapping Not Found");
            }
            
            $instance = new $mapping($model);
            if ($instance instanceof Mapping) {
                // 注册事件
                if ($instance instanceof WithEvent) {
                    $events = $instance->events();
                    foreach ($events as $event => $listener) {
                        Event::listen($event, $listener);
                    }
                }
                
                return $instance;
            }
            
            throw new Exception("{$mapping} Must Be Instance Of Mapping");
        }
        
        // 没有设置Mapping，返回一个默认的Mapping
        return new class($model) extends Mapping {
            public function mapping(): array
            {
                $mappings = [];
                
                foreach ($this->model->getAttributes() as $key => $value) {
                    $type = is_array($value) ? 'array' : 'string';
                    $mappings[$key] = $this->set($key, $type);
                }
                
                return $mappings;
            }
        };
    }
    
    /**
     * 获取配置信息
     * @param null $key
     * @return Collection|string|array
     */
    public function getConfig($key = null)
    {
        return $key ? $this->config->get($key) : $this->config;
    }
    
    /**
     * 设置变更记录操作人
     * @param $operator
     */
    public static function setOperator($operator)
    {
        if (is_callable($operator)) {
            self::$operator = $operator;
        } else {
            self::$operator = function () use ($operator) {
                return $operator;
            };
        }
    }
    
    /**
     * 获取操作人
     * @return mixed
     */
    public static function getOperator()
    {
        return call_user_func(self::$operator);
    }
}
