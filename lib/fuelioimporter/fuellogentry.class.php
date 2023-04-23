<?php

namespace FuelioImporter;

use FuelioImporter\IBackupEntry;
use FuelioImporter\FuelioBackupBuilder;

/**
 * Fuel log model
 * @author Kamil Kamiński
 * @version 20180124
 */
class FuelLogEntry implements IBackupEntry {
    /** @var string Fueling timestamp, valid for \DateTime constructor */
    protected $data;
    /** @var integer Odometer reading (total) */
    protected $odo;
    /** @var double Amount of fuel */
    protected $fuel;
    /** @var integer 0|1 Determines if thats a full or partial fillup */
    protected $full_fillup;
    /** @var double Total cost of fueling */
    protected $price;
    /** @var double Fuel consuption, calculated by Fuelio */
    protected $consumption;
    /** @var double Geo Latitude of fueling */
    protected $latitude;
    /** @var double Geo Longitude of fueling */
    protected $longitude;
    /** @var string Geo city name, required to display on map */
    protected $city;
    /** @var string Optional notes */
    protected $notes;
    /** @var integer 0|1 Determines if there are missing fueling before this entry in database */
    protected $missed_entries = 0;
    /** @var int Tank number */
    protected $tank_number = 1;
    /** @var int Tank number as id from FuelType */
    protected $fuel_type = null;
    /** @var double Volume price*/
    protected $volume_price;

    public function setDate($sDatetime) {
        $dt = new \DateTime($sDatetime);
        $this->data = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }

    public function setOdo($iOdo) {
        $this->odo = (int)$iOdo;
    }

    public function setFuel($dFuel) {
        $this->fuel = $dFuel;
    }

    public function setFullFillup($bFull) {
        // force integer form of forced boolean :)
        $this->full_fillup = (int)(bool)$bFull;
    }

    public function setPrice($dPrice) {
        $this->price = $dPrice;
    }

    public function setConsumption($dConsumption) {
        $this->consumption = $dConsumption;
    }

    public function setGeoCoords($dLatitude, $dLongitude) {
        $this->latitude = $dLatitude;
        $this->longitude = $dLongitude;
        
        // Fuelio requires city name to display geo data on map
        if (!empty($dLatitude) && empty($this->city))
        {
            $this->setCity('GPS');
        }
    }

    public function setCity($sCity) {
        $this->city = $sCity;
    }
    
    public function setNotes($sNotes) {
        $this->notes = $sNotes;
    }
    
    public function setMissedEntries($iMissed) {
        $this->missed_entries = $iMissed;
    }

    public function setTankNumber($nTankNumber) {
        $this->tank_number = (int)$nTankNumber;
    }

    public function setFuelType($nFuelType) {
        $this->fuel_type = is_null($nFuelType) ? null : (int)$nFuelType;
    }

    public function setVolumePrice($dVolumePrice) {
        $this->volume_price = (double)$dVolumePrice;
    }

    public function getData() {
        $vars = get_object_vars($this);
        return array_values($vars);
    }

}
