<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Fuel log model
 * @author Kamil Kamiński
 * @version 20180124
 */
class FuelLogEntry implements BackupEntryInterface
{
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

    public function setDate(string $sDatetime): void
    {
        $dt = new \DateTime($sDatetime);
        $this->data = $dt->format(FuelioBackupBuilder::DATE_FORMAT);
    }

    public function setOdo(int $iOdo): void
    {
        $this->odo = $iOdo;
    }

    public function setFuel(float $dFuel): void
    {
        $this->fuel = $dFuel;
    }

    public function setFullFillup(bool $bFull): void
    {
        // force integer form of forced boolean :)
        $this->full_fillup = (int) $bFull;
    }

    public function setPrice(float $dPrice): void
    {
        $this->price = $dPrice;
    }

    public function setConsumption(float $dConsumption): void
    {
        $this->consumption = $dConsumption;
    }

    public function setGeoCoords(float $dLatitude, float $dLongitude): void
    {
        $this->latitude = $dLatitude;
        $this->longitude = $dLongitude;

        // Fuelio requires city name to display geo data on map
        if (!empty($dLatitude) && empty($this->city)) {
            $this->setCity('GPS');
        }
    }

    public function setCity(string $sCity): void
    {
        $this->city = $sCity;
    }

    public function setNotes(string $sNotes): void
    {
        $this->notes = $sNotes;
    }

    public function setMissedEntries(int $iMissed): void
    {
        $this->missed_entries = $iMissed;
    }

    public function setTankNumber(int $nTankNumber): void
    {
        $this->tank_number = $nTankNumber;
    }

    public function setFuelType(int $nFuelType): void
    {
        $this->fuel_type = $nFuelType;
    }

    public function setVolumePrice(float $dVolumePrice): void
    {
        $this->volume_price = $dVolumePrice;
    }

    public function getData(): array
    {
        $vars = get_object_vars($this);
        return array_values($vars);
    }
}
