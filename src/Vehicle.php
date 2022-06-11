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
    
    /** @var int Vehicle production year */
    protected int $year;

    /** @var int Number of fuel tanks */
    protected int $tank_count = 1;

    /** @var int Type of fuel */
    protected int $tank_1_type;

    /** @var int Type of fuel */
    protected int $tank_2_type;

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
        int $iConsumption_unit = Vehicle::L_PER_100KM
    ) {
        $this->setName($sName);
        $this->setDescription($sDescription);
        $this->setDistanceUnit($iDistance_unit);
        $this->setFuelUnit($iFuel_unit);
        $this->setConsumptionUnit($iConsumption_unit);
    }

    public function setName($sName): void
    {
        $this->name = $sName;
    }

    public function setDescription($sDescription): void
    {
        $this->description = $sDescription;
    }

    public function setDistanceUnit($iDistance_unit): void
    {
        $this->distance_unit = $iDistance_unit;
    }

    public function setFuelUnit($iFuel_unit): void
    {
        $this->fuel_unit = $iFuel_unit;
    }

    public function setConsumptionUnit($iConsumption_unit): void
    {
        $this->consumption_unit = $iConsumption_unit;
    }

    public function setVIN($sVin): void
    {
        $this->vin = $sVin;
    }

    public function setInsurance($sInsurance): void
    {
        $this->insurance = $sInsurance;
    }

    public function setPlate($sPlate): void
    {
        $this->plate = $sPlate;
    }

    public function setMake($sMake): void
    {
        $this->make = $sMake;
    }

    public function setModel($sModel): void
    {
        $this->model = $sModel;
    }

    public function setYear($iYear): void
    {
        $this->year = (int) $iYear;
    }

    public function setTankCount($nTankCount): void
    {
        $this->tank_count = (int)$nTankCount;
    }

    public function setTankType($nIdx, $nType): void
    {
        $idx = (int)$nIdx;
        if (($idx !== 1 && $idx !== 2) || $nType === null) {
            return; //no-op, only two tanks storable
        }

        $this->{'tank_'.$idx.'_type'} = (int)$nType;
    }

    public function setActive($bActive): void
    {
        $this->active = (int)(bool)$bActive;
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
