<?php


namespace ModifyRecord\Drivers;

use Closure;
use Exception;
use ModifyRecord\ModifyRecord;

class DriverConnect
{
    protected static $extend;
    
    protected $record;
    
    public function __construct(ModifyRecord $record)
    {
        $this->record = $record;
    }
    
    /**
     * @return mixed|MysqlDriver
     */
    public function getDriver()
    {
        if (self::$extend) {
            return call_user_func(self::$extend, $this->record);
        }
        
        return $this->getDefaultDriver();
    }
    
    /**
     * @return MysqlDriver
     */
    public function getDefaultDriver()
    {
        return new MysqlDriver($this->record);
    }
    
    /**
     * 设置自定义Driver
     * @param $cluster
     * @return callable|Closure|String
     * @throws Exception
     */
    public static function extend($cluster)
    {
        if (is_string($cluster)) {
            if (class_exists($cluster)) {
                $driver = new $cluster();
                return function ($record) use ($driver) {
                    return $driver->handle($record);
                };
            }
            
            throw new Exception('自定义 Driver 类不存在');
        }
        
        if (is_callable($cluster)) {
            return $cluster;
        }
        
        throw new Exception('自定义 Driver 参数类型错误');
    }
}
