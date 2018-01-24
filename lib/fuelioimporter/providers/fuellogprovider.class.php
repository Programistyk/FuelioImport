<?php

namespace FuelioImporter\Providers;

use FuelioImporter\Cost;
use FuelioImporter\CostCategory;
use FuelioImporter\FuelioBackupBuilder;
use FuelioImporter\FuelLogEntry;
use FuelioImporter\IConverter;
use FuelioImporter\InvalidFileFormatException;
use FuelioImporter\InvalidUnitException;
use FuelioImporter\Vehicle;

class FuellogProvider implements IConverter
{
    protected $vehicles = array();
    /** @var string Vehicle key used to import data */
    protected $vehicle_key = null;
    /** @var int Vehicle index provided by user */
    protected $selected_vehicle = null;

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
        $this->selected_vehicle = intval($form_data['vehicle_id'], 10) - 1;
        if ($this->selected_vehicle < 0) {
            $this->selected_vehicle = null; // Autoselect
        }
        $this->processVehicles($in, $out);

        // Import fillups
        $this->processFillups($in, $out);

        // Import costs
        $this->processCosts($in, $out);

        return $out;
    }

    /**
     * Reads vehicles from Fuel Log's export
     * @param \SplFileObject $in
     * @param FuelioBackupBuilder $out
     * @throws InvalidFileFormatException
     * @throws InvalidUnitException
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

        // If user provided a valid index, select that vehicle
        if ($this->selected_vehicle !== null) {
            $keys = array_keys($this->vehicles);

            if (isset($keys[$this->selected_vehicle])) {
                $this->vehicle_key = $keys[$this->selected_vehicle];
            }
        }

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
            $this->getDistanceUnit($data[3]),
            $this->getVolumeUnit($data[4]),
            $this->getConsumptionUnit($data[5])
        );
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

        do {
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
        } while (!$in->eof() && strpos($data[0], '#', 0) !== 0);
    }

    protected function processCosts(\SplFileObject $in, FuelioBackupBuilder $out) {
        // make,model,title,date,mileage,costs,note,recurrence
        if ($in->eof()) {
            return; // Turns out costs are optional in file, so skip if we are at its end
        }

        $header = $in->fgetcsv();
        if ($header[0] !== 'make' || count($header) !== 8) {
            throw new InvalidFileFormatException();
        }

        // Since FuelLog does not have cost categories, we put all costs into a fake one
        $out->writeCostCategoriesHeader();
        $out->writeCostCategory(new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID, 'FuelLog Import'));

        // Write costs header
        $out->writeCoststHeader();

        do {
            $data = $in->fgetcsv();
            if (!$data) {
                continue;
            }

            // Skip data for car not selected
            $data_key = $data[0].'.'.$data[1];
            if ($data_key !== $this->vehicle_key) {
                continue;
            }

            $cost = new Cost();
            $cost->setCost((double)$data[5]);
            $cost->setCostCategoryId(FuelioBackupBuilder::SAFE_CATEGORY_ID);
            $cost->setDate($this->normalizeDate($data[3]));
            $cost->setTitle(trim($data[2]));
            $cost->setNotes(trim($data[6]));
            $cost->setOdo($data[4]);
            $cost->setReminderDate($this->convertCostReminder($this->normalizeDate($data[3]), $data[7]));
            $cost->setRepeatMonths($this->convertRepeatMonths($data[7]));
            $out->writeCost($cost);

        } while (!$in->eof() && strpos($data[0], '#', 0) !== 0);

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
            // Let's assume it's written with '/' as separator
            // and it's actually D/M/YYYY, as we have no way of detecting M/D/YYYY when day part is < 13
            if ($date[1] === '/' || $date[2] === '/') {
                $parts = explode('/', $date, 3);
                return $parts[2] . '-' . $parts[1] . '-' . $parts[0]; // YYYY-MM-DD
            }
        }
        return $date; //no-op
    }


    /**
     * Returns distance unit extracted from log
     * @param $raw string
     * @return int Vehicle const
     * @throws InvalidUnitException
     */
    protected function getDistanceUnit($raw)
    {
        /* Based on FuelLog's explanations.txt */

        switch ((int)$raw) {
            case 1 : return Vehicle::KILOMETERS;
            case 2 : return Vehicle::MILES;
            case 3 : throw new InvalidUnitException('Hours as distance units are not supported.');
            default : throw new InvalidUnitException('Unsupported distance unit: ' . substr($raw, 1, 10));
        }
    }

    /**
     * Returns volume unit extracted from log
     * @param $raw string
     * @return int Vehicle const
     * @throws InvalidUnitException
     */
    protected function getVolumeUnit($raw)
    {
        /* Based on FuelLog's explanations.txt */

        switch ((int)$raw) {
            case 1 : return Vehicle::LITRES;
            case 2 : return Vehicle::GALLONS_US;
            case 3 : return Vehicle::GALLONS_UK;
            case 4 : throw new InvalidUnitException('kWh as volume unit is not supported.');
            case 5 : throw new InvalidUnitException('Kilogram as volume units is not supported.');
            case 6 : throw new InvalidUnitException('Gasoline Gallon Equivalent as volume unit is not supported.');
            default: throw new InvalidUnitException('Unsupported volue unit: ' . substr($raw, 1, 10));
        }
    }

    /**
     * Returns consumption unit extracted from log
     * @param $raw string
     * @return int Vehicle const
     * @throws InvalidUnitException
     */
    protected function getConsumptionUnit($raw)
    {

        /* Based on FuelLog's explanations.txt */

        switch ((int)$raw) {
            case 1 : return Vehicle::L_PER_100KM;
            case 2 : return Vehicle::MPG_US;
            case 3 : return Vehicle::MPG_UK;
            case 4 : return Vehicle::KM_PER_L;
//            5 = l/km
//              6 = l/mi
//              7 = l/100mi
//              8 = mi/l
//              9 = gal(us)/km
//             10 = gal(us)/100km
//             11 = gal(us)/mi
//             12 = gal(us)/100mi
            case 13 : return Vehicle::KM_PER_GAL_US;
//             14 = gal(uk)/km
//             15 = gal(uk)/100km
//             16 = gal(uk)/mi
//             17 = gal(uk)/100mi
            case 18: return Vehicle::KM_PER_GAL_UK;
//             19 = kWh/km
//             20 = kWh/100km
//             21 = kWh/mi
//             22 = kWh/100mi
//             23 = km/kWh
//             24 = mi/kWh
//             25 = kg/km
//             26 = kg/100km
//             27 = kg/mi
//             28 = kg/100mi
//             29 = km/kg
//             30 = mi/kg
//             31 = gge/km
//             32 = gge/100km
//             33 = gge/mi
//             34 = gge/100mi
//             35 = km/gge
//             36 = mi/gge
//             37 = l/h
//             38 = h/l
//             39 = gal(us)/h
//             40 = h/gal(us)
//             41 = gal(uk)/h
//             42 = h/gal(uk)
//             43 = kWh/h
//             44 = h/kWh
//             45 = kg/h
//             46 = h/kg
//             47 = gge/h
//             48 = h/gge
            default: throw new InvalidUnitException('Selected fuel consumption unit is not supported.');
        }
    }

    /**
     * Returns cost date moved according to recurrence type
     * @param $sDate string current cost date
     * @param $raw_recurrence string
     * @return null|string New reminders date
     * @throws InvalidUnitException
     */
    protected function convertCostReminder($sDate, $raw_recurrence)
    {
        /* Based on FuelLog's explanations.txt */

        if (empty($sDate)) {
            return Cost::EMPTY_DATE;
        }
        $new_date = new \DateTime($sDate);

        switch((int)$raw_recurrence) {
            case 0 : return Cost::EMPTY_DATE; // One-time
            case 1 : $step = 'P1D'; break; // Daily cost
            case 2 : $step = 'P1W'; break; // Weekly cost
            case 3 : $step = 'P1M'; break; // Monthly cost
            case 4 : $step = 'P2M'; break; // Bimonthly cost
            case 5 : $step = 'P1Y'; break; // Yearly cost
            case 6 : $step = 'P3M'; break; // Quarterly cost
            case 7 : $step = 'P6M'; break; // Half-yearly cost
            case 8 : $step = 'P2Y'; break; // Every two years cost
            default: throw new InvalidUnitException('Invalid cost recurrence type.');
        }

        $new_date->modify($step);
        return $new_date->format('Y-m-d');
    }

    /**
     * Returns number of months of recurrence
     * @param $raw_recurrence string
     * @return int Number of recurrence months
     */
    protected function convertRepeatMonths($raw_recurrence)
    {
        switch((int)$raw_recurrence) {
            case 3 : return 1;
            case 4 : return 2;
            case 5 : return 12;
            case 6 : return 3;
            case 7 : return 6;
            case 8 : return 24;
            default: return 0; // By default there is no monthly repetition
        }
    }
}