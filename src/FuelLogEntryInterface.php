<?php

declare(strict_types=1);

namespace FuelioImporter;

use FuelioImporter\BackupEntryInterface;
use FuelioImporter\FuelioBackupBuilder;

/**
 * Fuel log model
 * @author Kamil Kamiński
 * @version 20180124
 */
class FuelLogEntryInterface implements BackupEntryInterface {
    /** @var string Fueling timestamp, valid for \DateTime constructor */
    protected string $data;
    /** @var integer Odometer reading (total) */
    protected int $odo;
    /** @var double Amount of fuel */
    protected float $fuel;
    /** @var integer 0|1 Determines if thats a full or partial fillup */
    protected int $full_fillup;
    /** @var double Total cost of fueling */
    protected float $price;
    /** @var double Fuel consuption, calculated by Fuelio */
    protected float $consumption;
    /** @var double Geo Latitude of fueling */
    protected float $latitude;
    /** @var double Geo Longitude of fueling */
    protected float $longitude;
    /** @var string Geo city name, required to display on map */
    protected string $city;
    /** @var string Optional notes */
    protected string $notes;
    /** @var integer 0|1 Determines if there are missing fueling before this entry in database */
    protected int $missed_entries = 0;
    /** @var int Tank number */
    protected int $tank_number = 1;
    /** @var int Tank number as id from FuelType */
    protected int $fuel_type;
    /** @var double Volume price*/
    protected float $volume_price;

    public function setDate($sDatetime): void
    {
        $dt = new \DateTime($sDatetime);
        $this->data = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }

    public function setOdo($iOdo): void
    {
        $this->odo = (int)$iOdo;
    }

    public function setFuel($dFuel): void
    {
        $this->fuel = $dFuel;
    }

    public function setFullFillup($bFull): void
    {
        // force integer form of forced boolean :)
        $this->full_fillup = (int) (bool) $bFull;
    }

    public function setPrice($dPrice): void
    {
        $this->price = $dPrice;
    }

    public function setConsumption($dConsumption): void
    {
        $this->consumption = $dConsumption;
    }

    public function setGeoCoords($dLatitude, $dLongitude): void
    {
        $this->latitude = $dLatitude;
        $this->longitude = $dLongitude;
        
        // Fuelio requires city name to display geo data on map
        if (!empty($dLatitude) && empty($this->city))
        {
            $this->setCity('GPS');
        }
    }

    public function setCity($sCity): void
    {
        $this->city = $sCity;
    }
    
    public function setNotes($sNotes): void
    {
        $this->notes = $sNotes;
    }
    
    public function setMissedEntries($iMissed): void
    {
        $this->missed_entries = $iMissed;
    }

    public function setTankNumber($nTankNumber): void
    {
        $this->tank_number = (int)$nTankNumber;
    }

    public function setFuelType($nFuelType): void
    {
        $this->fuel_type = (int)$nFuelType;
    }

    public function setVolumePrice($dVolumePrice): void
    {
        $this->volume_price = (double)$dVolumePrice;
    }

    public function getData(): array
    {
        $vars = get_object_vars($this);
        return array_values($vars);
    }
}
