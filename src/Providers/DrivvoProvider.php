<?php

declare(strict_types=1);

namespace FuelioImporter\Providers;

use FuelioImporter\Card\DrivvoCard;
use FuelioImporter\Cost;
use FuelioImporter\CostCategory;
use FuelioImporter\Form\FormValidatorException;
use FuelioImporter\FuelioBackupBuilder;
use FuelioImporter\FuelLogEntry;
use FuelioImporter\CardInterface;
use FuelioImporter\ProviderInterface;
use FuelioImporter\InvalidFileFormatException;
use FuelioImporter\Vehicle;
use SplFileObject;

class DrivvoProvider implements ProviderInterface
{
    /** @var string Output filename */
    protected string $output_filename = '';
    /** @var int distance unit */
    protected int $dist_unit = 0;
    /** @var int fuel unit */
    protected int $fuel_unit = 0;
    /** @var array<string> list of warnings */
    protected array $warnings = [];

    private const FUELLING_HEADERS = [
        '##Refuelling',
        '#Reabastecimiento'
    ];

    private const SERVICE_HEADERS = [
        '##Service',
        '#Servicio'
    ];

    private const EXPENSE_HEADERS = [
        '##Expense',
    ];

    private const TRUTHY = [
        'Si',
        'Yes',
        'Tak',
        'Ja'
    ];

//    private const FALSY = [
//        'No',
//        'Nie',
//        'Nein'
//    ];

    public function getName(): string
    {
        return 'drivvo';
    }

    public function getTitle(): string
    {
        return 'Drivvo';
    }

    public function getOutputFileName(): string
    {
        return $this->output_filename ?: $this->getTitle();
    }

    public function getStylesheetLocation(): ?string
    {
        return null;
    }

    public function setCarName($name): void
    {
        if (!empty($name)) {
            $this->output_filename = $name;
        }
    }

    public function getCard(): CardInterface
    {
        return new DrivvoCard();
    }

    public function getErrors(): array
    {
        return [];
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function processFile(SplFileObject $in, ?iterable $form_data): FuelioBackupBuilder
    {
        if ($in->isDir() || ($in->isFile() && !$in->isReadable())) {
            throw new InvalidFileFormatException('File is not readable');
        }

        if (!$form_data) {
            throw new FormValidatorException('No form data received');
        }

        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        $this->dist_unit = (int) $form_data['dist_unit'];
        $this->fuel_unit = (int) $form_data['fuel_unit'];

        // Configure reader
        $in->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        // Prepare output generator
        $out = new FuelioBackupBuilder();

        // Find starting header
        $this->rewindToHeader($in, self::FUELLING_HEADERS);

        if ($in->eof()) {
            throw new InvalidFileFormatException('File format is not recognized.');
        }

        $this->processVehicles($in, $out);

        // Import fillups
        $this->processFillups($in, $out);

        // Since FuelLog does not have cost categories, we put all costs into a fake one
        $out->writeCostCategoriesHeader();
        $out->writeCostCategory(new CostCategory(1, 'Service'));
        $out->writeCostCategory(new CostCategory(2, 'Expense'));

        // Write costs header
        $out->writeCoststHeader();

        // Import Expense
        $this->processExpense($in, $out);

        // Import Service
        $this->processService($in, $out);

        return $out;
    }

    /**
     * Reads vehicles from Fuel Log's export
     * @param SplFileObject $in
     * @param FuelioBackupBuilder $out
     */
    protected function processVehicles(SplFileObject $in, FuelioBackupBuilder $out): void
    {
        // Write out selected vehicle
        $out->writeVehicleHeader();

        // Prepare Vehicle
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
        $out->writeVehicle($vehicle);
    }

    protected function processFillups(SplFileObject $in, FuelioBackupBuilder $out): void
    {
        $this->rewindToHeader($in, self::FUELLING_HEADERS);
        if ($in->eof()) {
            throw new InvalidFileFormatException();
        }

        $header = $in->fgetcsv();
        if (!$header || count($header) < 8) {
            throw new InvalidFileFormatException();
        }

        $out->writeFuelLogHeader();

        do {
            $data = $in->fgetcsv();
            if ($data && $data[0] !== '' && $data[0] > 0) {
                $entry = new FuelLogEntry();
                $entry->setDate($this->normalizeDate((string)$data[1]));
                $entry->setOdo((int)$data[0]);
                $entry->setFuel((float)$data[5]);
                $entry->setVolumePrice((float)$data[3]);

                //Full fillup
                //In drivvo this is translated phrase - yes/no, si, no etc...
                //Saved in column $data[6]
                $fullfillup = $data[6];
                $ifull = (int)in_array($fullfillup, self::TRUTHY, true);

                $entry->setFullFillup((bool)$ifull);

                $entry->setPrice((float)$data[4]);
                $entry->setNotes((string)$data[18]);

                $out->writeFuelLog($entry);
            }
        } while (!$in->eof() && $data && strpos($data[0] ?? '', '#', 0) !== 0);
    }

    protected function processExpense(SplFileObject $in, FuelioBackupBuilder $out): void
    {
        // make,model,title,date,mileage,costs,note,recurrence
        $this->rewindToHeader($in, self::EXPENSE_HEADERS);
        if ($in->eof()) {
            return; // Turns out costs are optional in file, so skip if we are at its end
        }

        $header = $in->fgetcsv();

        if (!$header || count($header) < 7) {
            $this->warnings[] = 'Skipping expenses as the header is not recognized.';
            return;
        }

        do {
            $data = $in->fgetcsv();
            if ($data && $data[0] !== '' && $data[0] > 0) {
                $cost = new Cost();
                $cost->setOdo((int)$data[0]);
                $cost->setDate($this->normalizeDate((string)$data[1]));
                $cost->setCost((float)$data[2]);
                $cost->setCostCategoryId(2);
                $cost->setTitle(trim($data[3] ?? ''));
                $cost->setNotes(trim($data[6] ?? ''));
                $out->writeCost($cost);
            }
        } while (!$in->eof() && strpos($data[0] ?? '', '#', 0) !== 0);
    }

    protected function processService(SplFileObject $in, FuelioBackupBuilder $out): void
    {
        // make,model,title,date,mileage,costs,note,recurrence

        $this->rewindToHeader($in, self::SERVICE_HEADERS);
        if ($in->eof()) {
            return; // Turns out costs are optional in file, so skip if we are at its end
        }
        $header = $in->fgetcsv();

        if (!$header || count($header) < 6) {
            $this->warnings[] = 'Ignoring services  as the header is not recognized.';
            return;
        }

        do {
            $data = $in->fgetcsv();
            if ($data && $data[0] !== '' && $data[0] > 0 && count($header) === 6) {
                $cost = new Cost();
                $cost->setOdo((int)$data[0]);
                $cost->setDate($this->normalizeDate((string)$data[1]));
                $cost->setCost((float)$data[2]);
                $cost->setCostCategoryId(1);
                $cost->setTitle(trim($data[3] ?? ''));
                $cost->setNotes(trim($data[5] ?? ''));
                $out->writeCost($cost);
            }
        } while (!$in->eof() && strpos($data[0] ?? '', '#', 0) !== 0);
    }

    /**
     * Normalizes date format for DateTime
     * @param $date string Date
     * @return string Date in YYYY-MM-DD
     *
     * Currently it only detects dd/mm/YYYY format and turns it into YYYY-MM-DD
     */
    protected function normalizeDate(string $date): string
    {
        // Let's assume date could be written as X/Y/ZZZZ
        // Let's assume it's written with '/' as separator
        // and it's actually D/M/YYYY, as we have no way of detecting M/D/YYYY when day part is < 13
        if (strlen($date) >= 8 && ($date[1] === '/' || $date[2] === '/')) {
            $parts = explode('/', $date, 3);
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0]; // YYYY-MM-DD
        }
        return $date; //no-op
    }

    /** @param array<string> $headers */
    protected function rewindToHeader(SplFileObject $file, array $headers): void
    {
        $file->rewind();
        do {
            $line = $file->fgetcsv();
        } while (!$file->eof() && !in_array($line[0] ?? '', $headers, true));
    }
}
