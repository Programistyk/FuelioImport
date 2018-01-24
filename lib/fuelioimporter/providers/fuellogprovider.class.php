<?php

namespace FuelioImporter\Providers;

use FuelioImporter\FuelioBackupBuilder;
use FuelioImporter\FuelLogEntry;
use FuelioImporter\IConverter;
use FuelioImporter\InvalidFileFormatException;
use FuelioImporter\Vehicle;

class FuellogProvider implements IConverter
{
    protected $vehicles = array();
    /** @var string Vehicle key used to import data */
    protected $vehicle_key = null;

    public function getName()
    {
        return 'fuellog';
    }

    public function getTitle()
    {
        return 'Fuel Log';
    }

    public function getOutputFileName()
    {
        return $this->getTitle();
    }

    public function getStylesheetLocation()
    {
        return null;
    }

    public function setCarName($name)
    {
        if (!empty($name)) {
            $this->car_name = $name;
        }
    }

    public function getCard()
    {
        return new FuellogCard();
    }

    public function getErrors()
    {
        return array();
    }

    public function getWarnings()
    {
        return array();
    }

    public function processFile(\SplFileObject $in, $form_data)
    {
        if ($in->isDir() || ($in->isFile() && !$in->isReadable())) {
            throw new InvalidFileFormatException();
        }

        // Configure reader
        $in->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        // Prepare output generator
        $out = new FuelioBackupBuilder();

        $line = $in->fgetcsv();
        if ($line[0] !== '## vehicles') {
            throw new InvalidFileFormatException();
        }

        // Import vehicles
        $this->processVehicles($in, $out);

        // Import fillups
        $this->processFillups($in, $out);

        return $out;
    }

    /**
     * Reads vehicles from Fuel Log's export
     * @param \SplFileObject $in
     * @param FuelioBackupBuilder $out
     * @throws InvalidFileFormatException
     */
    protected function processVehicles(\SplFileObject $in, FuelioBackupBuilder $out)
    {
        // "make","model","note","distance","volume","consumption"
        $header = $in->fgetcsv();
        if ($header[0] !== 'make' || count($header) < 6) {
            throw new InvalidFileFormatException();
        }
        do {
            if (!($line = $in->fgetcsv()) || strpos($line[0], '#', 0) === 0) {
                break;
            }
            $key = $line[0] . '.' . $line[1];

            // Select imported vehicle if its data is in litres/kilometers
            if (!$this->vehicle_key && $line[3] === '1' && $line[4] === '1' && $line[5] === '1') {
                $this->vehicle_key = $key;
            }

            $this->vehicles[$key] = $line;

        } while (!$in->eof() && strpos($line[0], '#', 0) !== 0);

        if (!reset($this->vehicles)) {
            throw new InvalidFileFormatException('No vehicles in file.');
        }

        // Select vehicle key to import even if we don't have liters/kilometers
        if (!$this->vehicle_key) {
            $this->vehicle_key = key($this->vehicles);
        }

        // Write out selected vehicle
        $out->writeVehicleHeader();

        // Prepare Vehicle
        $data = $this->vehicles[$this->vehicle_key];
        $vehicle = new Vehicle(
            trim($data[0] . ' ' . $data[1]), // Build proper name: Make + Model
            $data[2], // Use Notes as description
            Vehicle::KILOMETERS,
            Vehicle::LITRES,
            Vehicle::L_PER_100KM);
        $out->writeVehicle($vehicle);
    }

    protected function processFillups(\SplFileObject $in, FuelioBackupBuilder $out)
    {
        // "make","model","date","mileage","fuel","price","partial","note"
        $header = $in->fgetcsv();
        if ($header[0] !== 'make' || count($header) !== 8) {
            throw new InvalidFileFormatException();
        }

        $out->writeFuelLogHeader();
        
        while (!$in->eof()) {
            $data = $in->fgetcsv();
            if (!$data) {
                continue;
            }

            // Skip data for car not selected
            $data_key = $data[0].'.'.$data[1];
            if ($data_key !== $this->vehicle_key) {
                continue;
            }
            
            $entry = new FuelLogEntry();
            $entry->setDate($this->normalizeDate($data[2]));
            $entry->setOdo((double)$data[3]);
            $entry->setFuel((double)$data[4]);
            $entry->setPrice((double)$data[5]);
            $entry->setFullFillup($data[6] !== '1');
            $entry->setNotes($data[7]);

            $out->writeFuelLog($entry);
        }
    }

    /**
     * Normalizes date format for DateTime
     * @param $date string Date
     * @return string Date in YYYY-MM-DD
     *
     * Currently it only detects dd/mm/YYYY format and turns it into YYYY-MM-DD
     */
    protected function normalizeDate($date)
    {
        // Let's assume date could be written as X/Y/ZZZZ
        if (strlen($date) >= 8) {
            // Let's assume they're written with '/' as separator
            // and its actually D/M/YYYY, as we have no way of detecting M/D/YYYY when day part is < 13
            if ($date[1] === '/' || $date[2] === '/') {
                $parts = explode('/', $date, 3);
                return $parts[2] . '-' . $parts[1] . '-' . $parts[0]; // YYYY-MM-DD
            }
        }
        return $date; //no-op
    }

}