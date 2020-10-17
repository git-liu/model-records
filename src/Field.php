<?php


namespace ModifyRecord;


class Field
{
    public $name;
    
    public $type;
    
    public $useMappingName;
    
    /**
     * Field constructor.
     * @param string $name
     * @param string $type
     * @param bool $useMappingName
     */
    public function __construct(string $name, string $type, bool $useMappingName = true)
    {
         $this->name = $name;
         
         $this->type = $type;
         
         $this->useMappingName = $useMappingName;
    }
}
