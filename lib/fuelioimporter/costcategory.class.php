<?php

namespace FuelioImporter;

class CostCategory implements IBackupEntry
{
    private $type_id;
    private $name;
    private $priority;
    
    public function __construct($type_id, $name, $priority = 0)
    {
        $this->type_id = $type_id;
        $this->name = $name;
        $this->priority = $priority;
    }
    
    public function getTypeId()
    {
        return $this->type_id;
    }
    
    public function setTypeId($iId)
    {
        $this->type_id = $iId;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getData()
    {
        $vars = get_object_vars($this); 
        return array_values($vars);
    }
}