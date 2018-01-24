<?php

namespace FuelioImporter;
use FuelioImporter\IBackupEntry;
use FuelioImporter\FuelioBackupBuilder;

/**
 * Additional costs model
 * @author Kamil Kamiński
 */
class Cost implements IBackupEntry
{
    const EMPTY_DATE = '2011-01-01';

    /** @var string Cost title */
    protected $title;
    /** @var \DateTime Timestamp */
    protected $date;
    /** @var integer Odometer reading */
    protected $odo;
    /** @var integer Cost category */
    protected $cost_category_id;
    /** @var string Optional notes */
    protected $notes;
    /** @var double Cost value*/
    protected $cost;
    /** @var integer 0|1 Determines if this is a recurring cost */
    protected $flag = 0;
    /** @var integer Internal id of recurring cost parent */
    protected $idR = 0;
    /** @var integer 0|1 Flags entry as read */
    protected $read = 1;
    /** @var integer Reminder odo, internal */
    protected $remindOdo = 0;
    /** @var string Reminder date, internal */
    protected $remindDate = '2011-01-01';
    /** @var integer 0|1 Flags cost as template */
    protected $tpl = 0;
    /** @var integer Repeat cost at specific ODO threshold */
    protected $repeat_odo = 0;
    /** @var integer Repeat cost after specific months */
    protected $repeat_months = 0;
    /** @var integer Flags as "is income" */
    protected $is_income = 0;
    /** @var integer Unique cost id */
    protected $unique_id;
    
    public function setTitle($sTitle)
    {
        if (empty($sTitle))
            $sTitle = 'No title';
        $this->title = $sTitle;
    }
    
    public function setDate($sDate)
    {
        $dt = new \DateTime($sDate);
        $this->date = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }
    
    public function setOdo($iOdo)
    {
        $this->odo = intval($iOdo);
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
        $this->setIsIncome($dCost<0);
    }
    
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }
    
    public function setIdR($iId)
    {
        $this->idR = $iId;
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

    public function setRepeatOdo($iOdo)
    {
        $this->repeat_odo = (int)$iOdo;
    }

    public function setRepeatMonths($sMonths)
    {
        $this->repeat_months = (int)$sMonths;
    }

    public function setIsIncome($bIsIncome)
    {
        $this->is_income = (int)(bool)$bIsIncome;
    }

    public function setUniqueId($iUniqueId)
    {
        $this->unique_id = (int)$iUniqueId;
    }

    public function getCostDate()
    {
        return $this->date;
    }
    
    public function getData() {
        $vars = get_object_vars($this); 
        return array_values($vars);
    }
}