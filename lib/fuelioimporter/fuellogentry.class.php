<?php

namespace FuelioImporter;

use FuelioImporter\IBackupEntry;
use FuelioImporter\FuelioBackupBuilder;

class FuelLogEntry implements IBackupEntry {

    private $data;
    private $odo;
    private $fuel;
    private $full_fillup;
    private $price;
    private $consumption;
    private $latitude;
    private $longitude;
    private $city;
    private $notes;
    private $missed_entries = 0;

    public function setDate($sDatetime) {
        $dt = new \DateTime($sDatetime);
        $this->data = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }

    public function setOdo($iOdo) {
        $this->odo = intval($iOdo);
    }

    public function setFuel($dFuel) {
        $this->fuel = $dFuel;
    }

    public function setFullFillup($bFull) {
        // force integer form of forced boolean :)
        $this->full_fillup = intval($bFull == true);
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
        if (empty($this->city))
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

    public function getData() {
        $vars = get_object_vars($this);
        return array_values($vars);
    }

}
