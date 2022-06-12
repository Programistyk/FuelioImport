<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Additional costs model
 * @author Kamil Kamiński
 */
class Cost implements BackupEntryInterface
{
    public const EMPTY_DATE = '2011-01-01';

    /** @var string Cost title */
    protected string $title;
    /** @var string Timestamp */
    protected string $date;
    /** @var integer Odometer reading */
    protected int $odo;
    /** @var integer Cost category */
    protected int $cost_category_id;
    /** @var string Optional notes */
    protected string $notes;
    /** @var double Cost value*/
    protected float $cost;
    /** @var integer 0|1 Determines if this is a recurring cost */
    protected int $flag = 0;
    /** @var integer Internal id of recurring cost parent */
    protected int $idR = 0;
    /** @var integer 0|1 Flags entry as read */
    protected int $read = 1;
    /** @var integer Reminder odo, internal */
    protected int $remindOdo = 0;
    /** @var string Reminder date, internal */
    protected string $remindDate = '2011-01-01';
    /** @var integer 0|1 Flags cost as template */
    protected int $tpl = 0;
    /** @var integer Repeat cost at specific ODO threshold */
    protected int $repeat_odo = 0;
    /** @var integer Repeat cost after specific months */
    protected int $repeat_months = 0;
    /** @var integer Flags as "is income" */
    protected int $is_income = 0;
    /** @var integer Unique cost id */
    protected int $unique_id;
    
    public function setTitle(string $sTitle): void
    {
        if (empty($sTitle)) {
            $sTitle = 'No title';
        }
        $this->title = $sTitle;
    }
    
    public function setDate(string $sDate): void
    {
        $dt = new \DateTime($sDate);
        $this->date = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }
    
    public function setOdo(int $iOdo): void
    {
        $this->odo = $iOdo;
    }
    
    public function setCostCategoryId(int $iId): void
    {
        $this->cost_category_id = $iId;
    }
    
    public function setNotes(string $sNotes): void
    {
        $this->notes = $sNotes;
    }
    
    public function setCost(float $dCost): void
    {
        $this->cost = $dCost;
        $this->setIsIncome($dCost < 0);
    }
    
    public function setFlag(int $flag): void
    {
        $this->flag = $flag;
    }
    
    public function setIdR(int $iId): void
    {
        $this->idR = $iId;
    }
    
    public function setRead(bool $bRead): void
    {
        $this->read = (int) $bRead;
    }
    
    public function setReminderOdo(int $iOdo): void
    {
        $this->remindOdo = $iOdo;
    }
    
    public function setReminderDate(string $sDate): void
    {
        $dt = new \DateTime($sDate);
        $this->remindDate = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }

    public function setRepeatOdo(int $iOdo): void
    {
        $this->repeat_odo = $iOdo;
    }

    public function setRepeatMonths(int $sMonths):void
    {
        $this->repeat_months = $sMonths;
    }

    public function setIsIncome(bool $bIsIncome): void
    {
        $this->is_income = (int) $bIsIncome;
    }

    public function setUniqueId(int $iUniqueId): void
    {
        $this->unique_id = $iUniqueId;
    }

    public function getCostDate(): string
    {
        return $this->date;
    }
    
    public function getData(): array
    {
        $vars = get_object_vars($this); 
        return array_values($vars);
    }
}
