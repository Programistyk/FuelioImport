<?php

namespace FuelioImporter;

/**
 * Cost category model
 * @author Kamil Kamiński
 */
class CostCategory implements IBackupEntry
{
    /** @var integer Internal cost category id */
    private $type_id;
    /** @var string Cost category name **/
    private $name;
    /** @var integer Cost category priority **/
    private $priority;
    /** @var string Category color, #rrggbb format **/
    private $color;
    
    public function __construct($type_id, $name, $priority = 0, $color = '')
    {
        $this->type_id = $type_id;
        $this->name = $name;
        $this->priority = $priority;
        $this->color = $color;
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