<?php

namespace FuelioImporter;

use FuelioImporter\IBackupEntry;

/**
 * Fuelio Vehicle data
 * @author Kamil Kamiński
 * @version 20150727
 */
class Vehicle implements IBackupEntry {

    /** Distance unit */
    const KILOMETERS = 0;
    /** Distance unit */
    const MILES = 1;
    
    /** Fuel unit */
    const LITRES = 0;
    /** Fuel unit */
    const GALLONS_US = 1;
    /** Fuel unit */
    const GALLONS_UK = 2;
    
    /** Consumption unit: l/100km */
    const L_PER_100KM = 0;
    /** Consumption unit: mpg (us) */
    const MPG_US = 1;
    /** Consumption unit: mpg (imp) */
    const MPG_UK = 2;
    /** Consumption unit: km/l */
    const KM_PER_L = 3;
    /** Consumption unit: km/gal (imp) */
    const KM_PER_GAL_US = 4;
    /** Consumption unit: km/gal (us) */
    const KM_PER_GAL_UK = 5;

    /** @var string Car name */
    protected $name;
    
    /** @var string Car description */
    protected $description;
    
    /** @var integer Distance unit */
    protected $distance_unit;
    
    /** @var integer Fuel unit */
    protected $fuel_unit;
    
    /** @var integer Consuption unit */
    protected $consumption_unit;
    
    /** @var string CSV date format, constant */
    protected $csv_date_format = 'dd.MM.yyyy';
    
    /** @var string Vehicle Identification Number */
    protected $vin;
    
    /** @var string Insurance policy number */
    protected $insurance;
    
    /** @var string Plate number */
    protected $plate;
    
    /** @var string Vehicle make */
    protected $make;
    
    /** @var string Vehicle model */
    protected $model;
    
    /** @var string Vehicle production year */
    protected $year;

    /**
     * Default constructor
     * @param string $sName Car name
     * @param string $sDescription Car description
     * @param integer $iDistance_unit Distance unit constant
     * @param integer $iFuel_unit Fuel unit constant
     * @param integer $iConsumption_unit Consuption unit constant
     */
    public function __construct($sName, $sDescription, $iDistance_unit = Vehicle::KILOMETERS, $iFuel_unit = Vehicle::LITRES, $iConsumption_unit = Vehicle::L_PER_100KM) {
        $this->setName($sName);
        $this->setDescription($sDescription);
        $this->setDistanceUnit($iDistance_unit);
        $this->setFuelUnit($iFuel_unit);
        $this->setConsumptionUnit($iConsumption_unit);
    }

    public function setName($sName) {
        $this->name = $sName;
    }

    public function setDescription($sDescription) {
        $this->description = $sDescription;
    }

    public function setDistanceUnit($iDistance_unit) {
        $this->distance_unit = $iDistance_unit;
    }

    public function setFuelUnit($iFuel_unit) {
        $this->fuel_unit = $iFuel_unit;
    }

    public function setConsumptionUnit($iConsumption_unit) {
        $this->consumption_unit = $iConsumption_unit;
    }

    public function setVIN($sVin) {
        $this->vin = $sVin;
    }

    public function setInsurance($sInsurance) {
        $this->insurance = $sInsurance;
    }

    public function setPlate($sPlate) {
        $this->plate = $sPlate;
    }

    public function setMake($sMake) {
        $this->make = $sMake;
    }

    public function setModel($sModel) {
        $this->model = $sModel;
    }

    public function setYear($iYear) {
        $this->year = intval($iYear);
    }

    public function getData() {
        $vars = get_object_vars($this);
        if (empty($vars['name'])) {
            $vars['name'] = 'No Name';
        }
        return array_values($vars);
    }

}
