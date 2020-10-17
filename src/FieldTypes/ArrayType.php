<?php


namespace ModifyRecord\FieldTypes;


class ArrayType extends BaseType
{
    public function toString($value)
    {
        if (is_callable($value)) {
            $value = call_user_func($value);
        }
        
        return is_array($value) ? json_encode((array) $value) : $value;
    }
}