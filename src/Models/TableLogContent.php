<?php


namespace ModifyRecord\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ModifyRecord\Contract\WithDataShow;

class TableLogContent extends Model
{
    public $table = 'tb_log_content';
    
    public $guarded = ['id'];
    
    public function getCurrentTbValueAttribute($field, $value, $model)
    {
        return $this->getValue($field, $value, $model);
    }
    
    public function getTbValueAttribute($field, $value, $model)
    {
        return $this->getValue($field, $value, $model);
    }
    
    public function getTbZhKeyAttribute($field, $value, $model)
    {
        foreach ($this->getMappings($model) as $map) {
            if (array_key_exists($field, $mapping = $map->mapping())) {
                $mappingField = $mapping[$field];
                if ($mappingField->useMappingName) {
                    return $mapping[$field]->name;
                }
                
                return $value;
            }
        
            return $value;
        }
    
        return $value;
    }
    
    protected function mutateAttribute($key, $value)
    {
        return $this->{'get' . Str::studly($key) . 'Attribute'}($this->getOriginal('tb_key'), $value, $this->getOriginal('model'));
    }
    
    protected function getValue($field, $value, $model)
    {
        foreach ($this->getMappings($model) as $map) {
            if ($map instanceof WithDataShow) {
                $dataShows = collect($map->dataShow());
                if ($dataShows->has($field)) {
                    $data = $dataShows->get($field);
                    return is_callable($data) ? call_user_func($data, $value) : $data;
                }
            }
        }
    
        return $value;
    }
    
    protected function getMappings($model)
    {
        $config = app('record.config');
        if (! empty($config['modelRouting'][$model])) {
            $mappingClass = $config['modelRouting'][$model] ?? [];
        } else {
            $mappingClass = [$config['modelMappings'][$model]] ?? [];
        }
        $mappings = [];
        foreach ($mappingClass as $class) {
            array_push($mappings, new $class(new $model));
        }
        
        return $mappings;
    }
}
