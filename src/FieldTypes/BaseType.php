<?php

namespace ModifyRecord\FieldTypes;

class BaseType
{
    /**
     * @param $value
     * @return mixed
     */
    public function toString($value)
    {
        if (is_callable($value)) {
            return call_user_func($value);
        }
        
        return $value;
    }
}