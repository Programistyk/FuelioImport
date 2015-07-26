<?php

namespace FuelioImporter;
use FuelioImporter\IBackupEntry;
use FuelioImporter\FuelioBackupBuilder;

class Cost implements IBackupEntry
{
    protected $title;
    protected $date;
    protected $odo;
    protected $cost_category_id;
    protected $notes;
    protected $cost;
    protected $flag = 0;
    protected $idR = 0;
    protected $read = 1;
    protected $remindOdo = 0;
    protected $remindDate = '2011-01-01';
    
    public function setTitle($sTitle)
    {
        $this->title = $sTitle;
    }
    
    public function setDate($sDate)
    {
        $dt = new \DateTime($sDate);
        $this->date = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }
    
    public function setOdo($iOdo)
    {
        $this->odo = $iOdo;
    }
    
    public function setCostCategoryId($iId)
    {
        $this->cost_category_id = $iId;
    }
    
    public function setNotes($sNotes)
    {
        $this->notes = $sNotes;
    }
    
    public function setCost($dCost)
    {
        $this->cost = $dCost;
    }
    
    public function setFlag($flag)
    {
        $this->flag = $flag; // TODO: What is "flag"? 
    }
    
    public function setIdR($iId)
    {
        $this->idR = $iId; // TODO: What is Id R? Isn't it "internal"
    }
    
    public function setRead($bRead)
    {
        $this->read = intval(boolval($bRead));
    }
    
    public function setReminderOdo($iOdo)
    {
        $this->remindOdo = $iOdo;
    }
    
    public function setReminderDate($sDate)
    {
        $dt = new \DateTime($sDate);
        $this->remindDate = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }
    
    public function getData() {
        $vars = get_object_vars($this); 
        return array_values($vars);
    }
}