<?php

namespace FuelioImporter;

use FuelioImporter\IBackupEntry;

class Vehicle implements IBackupEntry {

    // Distance units
    const KILOMETERS = 0;
    const MILES = 1;
    
    // Fuel units
    const LITRES = 0;
    const GALLONS_US = 1;
    const GALLONS_UK = 2;
    
    // Consumption units
    const L_PER_100KM = 0;
    const MPG_US = 1;
    const MPG_UK = 2;
    const KM_PER_L = 3;
    const KM_PER_GAL_US = 4;
    const KM_PER_GAL_UK = 5;

    protected $name;
    protected $description;
    protected $distance_unit;
    protected $fuel_unit;
    protected $consumption_unit;
    protected $csv_date_format = 'dd.MM.yyyy';
    protected $vin;
    protected $insurance;
    protected $plate;
    protected $make;
    protected $model;
    protected $year;

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
