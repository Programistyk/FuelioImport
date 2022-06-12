<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Fuelio Vehicle data
 * @author Kamil Kamiński
 * @version 20180124
 */
class Vehicle implements BackupEntryInterface {

    /** Distance unit */
    public const KILOMETERS = 0;
    /** Distance unit */
    public const MILES = 1;
    
    /** Fuel unit */
    public const LITRES = 0;
    /** Fuel unit */
    public const GALLONS_US = 1;
    /** Fuel unit */
    public const GALLONS_UK = 2;
    
    /** Consumption unit: l/100km */
    public const L_PER_100KM = 0;
    /** Consumption unit: mpg (us) */
    public const MPG_US = 1;
    /** Consumption unit: mpg (imp) */
    public const MPG_UK = 2;
    /** Consumption unit: km/l */
    public const KM_PER_L = 3;
    /** Consumption unit: km/gal (imp) */
    public const KM_PER_GAL_US = 4;
    /** Consumption unit: km/gal (us) */
    public const KM_PER_GAL_UK = 5;

    /** @var string Car name */
    protected string $name;
    
    /** @var string Car description */
    protected string $description;
    
    /** @var integer Distance unit */
    protected int $distance_unit;
    
    /** @var integer Fuel unit */
    protected int $fuel_unit;
    
    /** @var integer Consuption unit */
    protected int $consumption_unit;
    
    /** @var string CSV date format, constant */
    protected string $csv_date_format = 'dd.MM.yyyy';
    
    /** @var string Vehicle Identification Number */
    protected string $vin;
    
    /** @var string Insurance policy number */
    protected string $insurance;
    
    /** @var string Plate number */
    protected string $plate;
    
    /** @var string Vehicle make */
    protected string $make;
    
    /** @var string Vehicle model */
    protected string $model;
    
    /** @var string Vehicle production year */
    protected string $year;

    /** @var int Number of fuel tanks */
    protected int $tank_count = 1;

    /** @var ?int Type of fuel */
    protected ?int $tank_1_type = null;

    /** @var ?int Type of fuel */
    protected ?int $tank_2_type = null;

    /** @var int Flag vehicle is active in app */
    protected int $active = 1;

    /**
     * Default constructor
     * @param string $sName Car name
     * @param string $sDescription Car description
     * @param integer $iDistance_unit Distance unit constant
     * @param integer $iFuel_unit Fuel unit constant
     * @param integer $iConsumption_unit Consumption unit constant
     */
    public function __construct(
        string $sName,
        string $sDescription,
        int $iDistance_unit = Vehicle::KILOMETERS,
        int $iFuel_unit = Vehicle::LITRES,
        int $iConsumption_unit = Vehicle::L_PER_100KM,
        bool $bActive = true
    ) {
        $this->setName($sName);
        $this->setDescription($sDescription);
        $this->setDistanceUnit($iDistance_unit);
        $this->setFuelUnit($iFuel_unit);
        $this->setConsumptionUnit($iConsumption_unit);
        $this->setActive($bActive);
        $this->setVIN('');
        $this->setInsurance('');
        $this->setPlate('');
        $this->setMake('');
        $this->setModel('');
        $this->setYear('');
        $this->setTankCount(0);
    }

    public function setName(string $sName): void
    {
        $this->name = $sName;
    }

    public function setDescription(string $sDescription): void
    {
        $this->description = $sDescription;
    }

    public function setDistanceUnit(int $iDistance_unit): void
    {
        $this->distance_unit = $iDistance_unit;
    }

    public function setFuelUnit(int $iFuel_unit): void
    {
        $this->fuel_unit = $iFuel_unit;
    }

    public function setConsumptionUnit(int $iConsumption_unit): void
    {
        $this->consumption_unit = $iConsumption_unit;
    }

    public function setVIN(string $sVin): void
    {
        $this->vin = $sVin;
    }

    public function setInsurance(string $sInsurance): void
    {
        $this->insurance = $sInsurance;
    }

    public function setPlate(string $sPlate): void
    {
        $this->plate = $sPlate;
    }

    public function setMake(string $sMake): void
    {
        $this->make = $sMake;
    }

    public function setModel(string $sModel): void
    {
        $this->model = $sModel;
    }

    public function setYear(string $iYear): void
    {
        $this->year = $iYear;
    }

    public function setTankCount(int $nTankCount): void
    {
        $this->tank_count = $nTankCount;
    }

    public function setTankType(int $nIdx, ?int $nType): void
    {
        if (($nIdx !== 1 && $nIdx !== 2) || $nType === null) {
            return; //no-op, only two tanks storable
        }

        $this->{'tank_'.$nIdx.'_type'} = $nType;
    }

    public function setActive(bool $bActive): void
    {
        $this->active = (int) $bActive;
    }

    public function getData(): array
    {
        $vars = get_object_vars($this);
        if (empty($vars['name'])) {
            $vars['name'] = 'No Name';
        }
        return array_values($vars);
    }
}
