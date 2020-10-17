<?php


namespace ModifyRecord\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ModifyRecord\Contract\WithDataShow;

class TableLogContent extends Model
{
    public $table = 'tb_log_content';
    
    public $guarded = ['id'];
    
    public function getCurrentTbValueAttribute($field, $value)
    {
        return $this->getValue($field, $value);
    }
    
    public function getTbValueAttribute($field, $value)
    {
        return $this->getValue($field, $value);
    }
    
    public function getTbZhKeyAttribute($field, $value)
    {
        if (count($mappingClass = ($this->mappings ?? []))) {
            foreach ($mappingClass as $map) {
                if (array_key_exists($field, $mappings = $map->mapping())) {
                    $mappingField = $mappings[$field];
                    if ($mappingField->useMappingName) {
                        return $mappings[$field]->name;
                    }
        
                    return $value;
                }
                
                return $value;
            }
        }
    
        return $value;
    }
    
    protected function mutateAttribute($key, $value)
    {
        return $this->{'get' . Str::studly($key) . 'Attribute'}($this->getOriginal('tb_key'), $value);
    }
    
    protected function getValue($field, $value)
    {
        if (count($mappingClass = ($this->mappings ?? []))) {
            foreach ($mappingClass as $map) {
                if ($map instanceof WithDataShow) {
                    $dataShows = collect($map->dataShow());
                    if ($dataShows->has($field)) {
                        $data = $dataShows->get($field);
                        return is_callable($data) ? call_user_func($data, $value) : $data;
                    }
                }
            }
        }
    
        return $value;
    }
}
