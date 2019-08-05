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

class DrivvoProvider implements IConverter
{
    protected $vehicles = array();
    /** @var string Vehicle key used to import data */
    protected $vehicle_key = null;
    /** @var int Vehicle index provided by user */
    protected $selected_vehicle = null;
    /** @var string|null Output filename */
    protected $output_filename = null;
    /** @var int distance unit */
    protected $dist_unit = 0;
  /** @var int fuel unit */
    protected $fuel_unit = 0;

    public function getName()
    {
        return 'drivvo';
    }

    public function getTitle()
    {
        return 'Drivvo';
    }

    public function getOutputFileName()
    {
        return $this->output_filename ?: $this->getTitle();
    }

    public function getStylesheetLocation()
    {
        return null;
    }

    public function setCarName($name)
    {
        if (!empty($name)) {
            $this->output_filename = $name;
        }
    }

    public function getCard()
    {
        return new DrivvoCard();
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

        $this->dist_unit = $form_data['dist_unit'];
        $this->fuel_unit = $form_data['fuel_unit'];

        // Configure reader
        $in->setFlags(\SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        // Prepare output generator
        $out = new FuelioBackupBuilder();

        $line = $in->fgetcsv();
        if ($line[0] !== '##Refuelling') {
            throw new InvalidFileFormatException();
        }

        $this->processVehicles($in, $out);

        // Import fillups
        $this->processFillups($in, $out);

        // Import Expense
        $this->processExpense($in, $out);

        // Import Service
        $this->processService($in, $out);

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
        $header = $in->fgetcsv();

        // Write out selected vehicle
        $out->writeVehicleHeader();

        // Prepare Vehicle
        $data = $this->vehicles[$this->vehicle_key];
        //$vname = trim($data[0]) . ' ' . trim($data[1]); // Build proper name: Make + Model;
        $vname = "Drivvo Car";
        $this->output_filename .= $vname;
        $description="Imported";
        
        $vehicle = new Vehicle(
            $vname,
            $description, // Use Notes as description
            $this->dist_unit,
            $this->fuel_unit,
            0
        );
        //$vehicle->setCsvDateFormat("yyyy-MM-dd");
        $out->writeVehicle($vehicle);
    }

    protected function processFillups(\SplFileObject $in, FuelioBackupBuilder $out)
    {
        $header = $in->fgetcsv();
        if ((count($header)>0 && count($header) < 8)) {
            throw new InvalidFileFormatException();
        }

        $out->writeFuelLogHeader();

        do {
            $data = $in->fgetcsv();
            if ($data[0]!=='' && $data[0]>0) {
            $entry = new FuelLogEntry();
            $entry->setDate($this->normalizeDate($data[1]));
            $entry->setOdo((double)$data[0]);
            $entry->setFuel((double)$data[5]);
            $entry->setVolumePrice((double)$data[3]);

            //Full fillup
            //In drivvo this is translated phrase - yes/no, si, no etc...
            //Saved in column $data[6]
            $fullfillup = $data[6];
            $ifull = 0;
            if ($fullfillup=='Si' || $fullfillup=='Yes' || $fullfillup=='Tak') {
                $ifull=1;
            } else if ($fullfillup=='No' || $fullfillup=='Nie') {
                $ifull=0;
            }


            $entry->setFullFillup($ifull);

            $entry->setPrice($date[4]);
            $entry->setNotes($data[18]);

            $out->writeFuelLog($entry);
        }

        } while (!$in->eof() && strpos($data[0], '#', 0) !== 0);
    }

    protected function processExpense(\SplFileObject $in, FuelioBackupBuilder $out) {
        // make,model,title,date,mileage,costs,note,recurrence
        if ($in->eof()) {
            return; // Turns out costs are optional in file, so skip if we are at its end
        }

        $header = $in->fgetcsv();

        // Since FuelLog does not have cost categories, we put all costs into a fake one
        $out->writeCostCategoriesHeader();
        $out->writeCostCategory(new CostCategory(1, 'Service'));
        $out->writeCostCategory(new CostCategory(2, 'Expense'));

        if (count($header)>0 && count($header)<7) {
            return;
            //throw new InvalidFileFormatException();
        }

        // Write costs header
        $out->writeCoststHeader();
        if (count($header)==7){
        do {
            $data = $in->fgetcsv();
            if ($data[0]!=='' && $data[0]>0 && count($header)==7) {
            $cost = new Cost();
            $cost->setOdo($data[0]);
            $cost->setDate($this->normalizeDate($data[1]));
            $cost->setCost((double)$data[2]);
            $cost->setCostCategoryId(2);
            $cost->setTitle(trim($data[3]));
            $cost->setNotes(trim($data[6]));
            $out->writeCost($cost);
            }
        } while (!$in->eof() && strpos($data[0], '#', 0) !== 0);
    } 

    }

    protected function processService(\SplFileObject $in, FuelioBackupBuilder $out) {
        // make,model,title,date,mileage,costs,note,recurrence
        if ($in->eof()) {
            return; // Turns out costs are optional in file, so skip if we are at its end
        }

        $header = $in->fgetcsv();
        if (count($header)>0 && count($header)<6) {
            return;
            //throw new InvalidFileFormatException();
        }

        do {
            $data = $in->fgetcsv();
            if ($data[0]!=='' && $data[0]>0 && count($header)==6) {
            $cost = new Cost();
            $cost->setOdo($data[0]);
            $cost->setDate($this->normalizeDate($data[1]));
            $cost->setCost((double)$data[2]);
            $cost->setCostCategoryId(1);
            $cost->setTitle(trim($data[3]));
            $cost->setNotes(trim($data[5]));
            $out->writeCost($cost);
            }
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
}