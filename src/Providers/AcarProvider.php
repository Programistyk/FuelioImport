<?php

declare(strict_types=1);

namespace FuelioImporter\Providers;

use DateTime;
use FuelioImporter\Card\AcarCard;
use FuelioImporter\Cost;
use FuelioImporter\CostCategory;
use FuelioImporter\Form\FormValidatorException;
use FuelioImporter\FuelioBackupBuilder;
use FuelioImporter\FuelLogEntry;
use FuelioImporter\FuelTypes;
use FuelioImporter\CardInterface;
use FuelioImporter\ProviderInterface;
use FuelioImporter\InvalidFileFormatException;
use FuelioImporter\InvalidUnitException;
use FuelioImporter\Vehicle;
use SimpleXMLElement;
use SplFileObject;
use ZipArchive;

class AcarProvider implements ProviderInterface
{
    // Car name
    protected string $car_name = 'aCar import';
    /** @var array<string, array<string|int>|false> List of files in zip archive */
    protected array $archive_files = [];
    /** @var array<string, string> metadata.inf file contents as array */
    protected array $metadata = [];
    /** @var array<string, string|bool> preferences.xml file contents as array */
    protected array $preferences = [];
    /** @var array<int|string, CostCategory> List of expenses from expenses.xml (as CostCategory instances) */
    protected array $expenses = [];
    /** @var array<int|string, CostCategory> List of services from services.xml (as CostCategory instances) */
    protected array $services = [];
    // Vehicle number provided by form
    protected int $selected_vehicle = 1;
    /** @var array<int, array{name: string, category: string}> Fuel types declared in aCar backup */
    protected array $acar_fuels = [];
    protected ?FuelTypes $fuel_types = null;

    // @see IConverter
    public function getName(): string
    {
        return 'acar';
    }

    // @see IConverter
    public function getTitle(): string
    {
        return 'aCar ABP';
    }

    // @see IConverter
    public function getOutputFileName(): string
    {
        return 'aCar-car-' . $this->selected_vehicle;
    }

    // @see IConverter
    public function getStylesheetLocation(): ?string
    {
        return null;
    }

    // @see IConverter
    public function setCarName($name): void
    {
        if (!empty($name)) {
            $this->car_name = $name;
        }
    }

    // @see IConverter
    public function processFile(SplFileObject $in, ?iterable $form_data): FuelioBackupBuilder
    {
        if (!$form_data) {
            throw new FormValidatorException('Missing form data, you need to choose vehicle #');
        }
        // We need to verify that we've got valid archive

        $zip = new ZipArchive();
        $out = new FuelioBackupBuilder();

        /** @todo: Provide more detailed Zip-related error handling */
        if ($zip->open($in->getPathname()) !== true) {
            throw new InvalidFileFormatException(); // For basics
        }

        // If no metadata.inf, throw error
        // list contents
        $i = 0;
        while (($stat = $zip->statIndex($i++)) !== false) {
            $normalized_name = strtolower($stat['name']);
            $this->archive_files[$normalized_name] = $stat;
        }

        $this->validateInputFile($zip, $out);

        $this->readPreferences($zip, $out);

        $this->processFuelTypes($zip);

        // Read vehicle data
        $this->selected_vehicle = (int) $form_data['vehicle_id'];
        $data = $this->getVehicle($this->selected_vehicle, $zip);
        // Process vehicle header
        $this->processVehicle($data, $zip, $out);

        // Process fuellings
        $this->processFuellings($data, $zip, $out);

        // Process Costs (expenses and services)
        $this->processCosts($data, $zip, $out);

        $zip->close();
        $out->rewind();
        return $out;
    }

    // @see IConverter
    public function getErrors(): array
    {
        return [];
    }

    // @see IConverter
    public function getWarnings(): array
    {
        return [];
    }

    // @see IConverter
    public function getCard(): CardInterface
    {
        return new AcarCard();
    }

    /**
     * Reads preferences.xml into array
     * @param ZipArchive $in Input archive
     * @param FuelioBackupBuilder $out Output file
     */
    protected function readPreferences(ZipArchive $in, FuelioBackupBuilder $out): void
    {
        // Read all preferences and store them as array
        $xml = new \SimpleXMLElement(stream_get_contents($in->getStream('preferences.xml')));
        foreach ($xml->preference as $node) {
            $atts = $node->attributes();
            if (!$atts) {
                continue;
            }
            $key = (string) ($atts['name'] ?? '');
            $type = (string) ($atts['type'] ?? '');
            $value = (string) $node;

            if ($type === 'java.lang.Boolean') {
                $value = ($value === 'true');
            }

            $this->preferences[$key] = $value;
        }
    }

    /**
     * Reads metadata.inf into $metadata array
     * @param ZipArchive $in Input archive
     * @throws InvalidFileFormatException When no metadata.inf in archive
     */
    protected function readMetadata(ZipArchive $in): void
    {
        $metastream = $in->getStream('metadata.inf');
        if (false === $metastream) {
            throw new InvalidFileFormatException();
        }

        while (!feof($metastream)) {
            $entry = explode('=', fgets($metastream), 2);
            if (count($entry) == 2) {
                $this->metadata[trim($entry[0])] = trim($entry[1]);
            }
        }
    }

    /**
     * Throws error if provided file is not Full Backup of aCar data
     * @param ZipArchive $in Input archive
     * @param FuelioBackupBuilder $out Output file
     * @throws InvalidFileFormatException
     */
    protected function validateInputFile(ZipArchive $in, FuelioBackupBuilder $out): void
    {
        if (!isset($this->archive_files['metadata.inf'])) {
            throw new \FuelioImporter\InvalidFileFormatException();
        }

        $this->readMetadata($in);

        // At this moment we support only full backups
        if (@$this->metadata['acar.backup.type'] !== 'Full-Backup') {
            throw new \FuelioImporter\InvalidFileFormatException('At this moment we support only Full Backups!');
        }
    }

    /**
     * Reads vehicle node of vehicles.xml and stores Vehicle
     * @param SimpleXMLElement $data vehicle node
     * @param ZipArchive $in Input archive
     * @param FuelioBackupBuilder $out Output file
     * @throws InvalidUnitException
     */
    public function processVehicle(SimpleXMLElement $data, ZipArchive $in, FuelioBackupBuilder $out): void
    {
        $out->writeVehicleHeader();
        $vehicle = new Vehicle($this->car_name, '');

        $vehicle->setName((string)$data->name);
        $vehicle->setDescription((string) $data->notes);
        $vehicle->setMake((string)$data->make);
        $vehicle->setModel((string)$data->model);
        $vehicle->setYear((string)$data->year);
        $vehicle->setPlate((string)$data->{'license-plate'});
        $vehicle->setVIN((string)$data->vin);
        $vehicle->setInsurance((string)$data->{'insurance-policy'});
        $vehicle->setFuelUnit($this->getFuelUnit($data));
        $vehicle->setDistanceUnit($this->getDistanceUnit($data));
        $vehicle->setConsumptionUnit($this->getConsumptionUnit());

        // Little hack, let's extract first mappable fuel type
        $vehicle->setTankType(1, $this->determineVehicleFuelType($data));

        $out->writeVehicle($vehicle);
    }

    /**
     * Reads fuel unit from aCar preferences or vehicle node
     * @param SimpleXmlElement $vehicleNode Vehicle node for additional data
     * @return integer Vehicle constant
     * @throws InvalidUnitException On unsupported unit
     */
    protected function getFuelUnit(SimpleXMLElement $vehicleNode): int
    {
        // New aCar version stores volume unit per vehicle, not globally
        if (array_key_exists('acar.volume-unit', $this->preferences)) {
            $volume_unit = $this->preferences['acar.volume-unit'];
        } else {
            $volume_unit = (string)$vehicleNode->{'volume-unit'};
        }
        switch ($volume_unit) {
            case 'L':
            case 'liter':
                return Vehicle::LITRES;
            case 'us_gallon':
            case 'gal (US)':
                return Vehicle::GALLONS_US;
            case 'uk_gallon':
            case 'gallon': // TODO: Can anybody confirm this?
            case 'gal (UK)': return Vehicle::GALLONS_UK;
            default:
                throw new InvalidUnitException();
        }
    }

    /**
     * Reads distance unit from aCar preferences or vehicle node
     * @return integer Vehicle constant
     * @throws InvalidUnitException
     */
    protected function getDistanceUnit(SimpleXMLElement $vehicleNode): int
    {
        // New aCar version stores distance unit per vehicle, not globally
        if (array_key_exists('acar.distance-unit', $this->preferences)) {
            $distance_unit = $this->preferences['acar.distance-unit'];
        } else {
            $distance_unit = (string)$vehicleNode->{'distance-unit'};
        }
        switch ($distance_unit) {
            case 'm':
            case 'mi' :
            case 'mile' :
                return Vehicle::MILES;
            case 'km':
            case 'kilometer':
                return Vehicle::KILOMETERS;
            default:
                throw new InvalidUnitException();
        }
    }

    /**
     * Reads consumption unit from aCars preferences
     * @return integer Vehicle constant
     * @throws InvalidUnitException
     */
    protected function getConsumptionUnit(): int
    {
        // @TODO: check the format behind other options:
        // mpg (us), mpg (imperial), gal/100mi (us), gal/100mi (imperial), km/L, km/gal (us), km/gal (imperial). mi/L
        switch ($this->preferences['acar.fuel-efficiency-unit']) {

            case 'L/100km':
                return Vehicle::L_PER_100KM;
            case 'MPG (UK)': // TODO: To Confirm
                return Vehicle::MPG_UK;
            case 'MPG (US)':
                return Vehicle::MPG_US;
            case 'km/L':
                return Vehicle::KM_PER_L;
            default:
                throw new InvalidUnitException();
        }
    }

    /**
     * Returns handle to vehicle data in vehicles.xml
     * @param integer $iVehicle Vehicle number
     * @param ZipArchive $in
     * @return SimpleXmlElement
     * @throws InvalidFileFormatException
     * @throws FormValidatorException
     */
    protected function getVehicle(int $iVehicle, ZipArchive $in): SimpleXMLElement
    {
        $stream = $in->getStream('vehicles.xml');
        $xml = new \SimpleXMLElement(stream_get_contents($stream));
        if (!$xml) {
            throw new InvalidFileFormatException();
        }
        $children = $xml->children();
        if (empty($children)) {
            throw new InvalidFileFormatException();
        }
        if (!isset($children[$iVehicle-1])) {
            throw new FormValidatorException('There is no car #' . $iVehicle . ' in backup file.');
        }
        return $children[$iVehicle-1];
    }

    /**
     * Converts aCars date to something more PHP friendly
     * @param string $date
     * @return string ATOM-formatted DateTime string
     */
    protected function readDate(string $date): string
    {
        return DateTime::createFromFormat('m/d/Y - H:i', $date)->format(\DateTimeInterface::ATOM);
    }

    /**
     * Converts fillups from aCar to Fuelio format
     * @param SimpleXMLElement $data Vehicle node
     * @param ZipArchive $in Input archive
     * @param FuelioBackupBuilder $out Output file
     */
    protected function processFuellings(SimpleXMLElement $data, ZipArchive $in, FuelioBackupBuilder $out): void
    {
        $out->writeFuelLogHeader();

        foreach ($data->{'fillup-records'}->{'fillup-record'} as $record) {
            $entry = new FuelLogEntry();

            $entry->setDate($this->readDate((string)$record->date));
            $entry->setFuel((float) $record->volume);
            $entry->setPrice((float) $record->{'total-cost'});
            $entry->setOdo((int) $record->{'odometer-reading'});
            // According to Adrian Kajda consumption is calculated by app itself
            // and we should not store it
            // $consumption = (string) $record->{'fuel-efficiency'};
            // $entry->setConsumption($this->calculateConsumption($consumption, $this->getConsumptionUnit()));

            $entry->setGeoCoords((float) $record->latitude, (float) $record->longitude);
            $entry->setFullFillup((string) $record->partial !== 'true');
            $entry->setMissedEntries((int) ($record->{'previous-missed-fillups'} === 'true'));
            $notes = (string) $record->{'fuel-brand'} . ' ' . (string) $record->{'fueling-station-address'} . ' ' . (string) $record->notes;
            $entry->setNotes(trim($notes));
            $entry->setMissedEntries(0);
            $fuelType = $this->getFuelType((int)$record->{'fuel-type-id'});
            if (!$fuelType) {
                throw new InvalidUnitException('Cannot determine fuel unit');
            }
            $entry->setFuelType($fuelType);
            $out->writeFuelLog($entry);
        }
    }

    /**
     * Do not blame me for this calculations, got them online
     * @param float $consumption Fuel consumption
     * @param int $iFormat Consumption unit
     * @return float Fuel consumption at L/100km
     * @throws InvalidUnitException
     */
    /*protected function calculateConsumption($consumption, $iFormat) {
        $dConsumption = floatval(str_replace(',', '.', $consumption));
        switch ($iFormat) {
            case Vehicle::MPG_US:
                $rate = 235.21458329475;
                break;
            case Vehicle::MPG_UK:
                $rate = 282.48093627967;
                break;
            case Vehicle::KM_PER_L;
                return 100.0 / $dConsumption;
            case Vehicle::L_PER_100KM:
                return $dConsumption;
            case Vehicle::KM_PER_GAL_US:
                $rate = 378.541178;
                break;
            case Vehicle::KM_PER_GAL_UK:
                $rate = 454.609188;
            default:
                throw new \FuelioImporter\InvalidUnitException();
        }

        return $rate / $dConsumption;
    }*/

    /**
     * Reads services.xml and stores them as CostCategory
     * @param ZipArchive $in Input archive
     * @throws InvalidFileFormatException
     */
    protected function readServicesAsCategories(ZipArchive $in): void
    {
        // Depending on aCar version, this data are in services.xml or event-subtypes.xml
        if ($in->statName('event-subtypes.xml') !== false) {
            $this->readNewServiceDefinition(new \SimpleXMLElement(stream_get_contents($in->getStream('event-subtypes.xml'))));
        } elseif ($in->statName('services.xml') !== false) {
            $this->readOldServiceDefinition(new \SimpleXMLElement(stream_get_contents($in->getStream('services.xml'))));
        }

        throw new InvalidFileFormatException();
    }

    /**
     * Reads old-style service definitions
     * @param SimpleXMLElement $node
     */
    private function readOldServiceDefinition(SimpleXMLElement $node): void
    {
        foreach ($node->service as $service) {
            $atts = $service->attributes();
            $id = (int)$atts['id'];
            $name = (string) $service->name;
            $this->services[$id] = new CostCategory(count($this->services), $name);
        }
    }

    /**
     * Reads new service definitions
     * @param SimpleXMLElement $node
     */
    private function readNewServiceDefinition(SimpleXMLElement $node): void
    {
        foreach ($node->{'event-subtype'} as $subtype) {
            $atts = $subtype->attributes();

            // Skip non-expenses
            if ((string)$atts['type'] !== 'service') {
                continue;
            }

            $id = (int)$atts['id'];
            $name = (string) $subtype->name;

            $this->services[$id] = new CostCategory(count($this->services), $name);
        }
    }

    /**
     * Reads expenses.xml and stores them as CostCategory
     * @param ZipArchive $in Input archive
     * @throws InvalidFileFormatException
     */
    protected function readExpensesAsCategories(ZipArchive $in): void
    {
        // Depending on aCar version, this data are in expenses.xml or event-subtypes.xml
        if ($in->statName('event-subtypes.xml') !== false) {
            $this->readNewExpensesAsCategories(new \SimpleXMLElement(stream_get_contents($in->getStream('event-subtypes.xml'))));
        } elseif ($in->statName('expenses.xml') !== false) {
            $this->readOldExpensesAsCategories(new \SimpleXMLElement(stream_get_contents($in->getStream('expenses.xml'))));
        }
        throw new InvalidFileFormatException();
    }

    /**
     * Reads new expense definitions
     * @param SimpleXMLElement $node
     */
    private function readNewExpensesAsCategories(SimpleXMLElement $node): void
    {
        foreach ($node->{'event-subtype'} as $subtype) {
            $atts = $subtype->attributes();

            // Skip non-expenses
            if ((string)$atts['type'] !== 'expense') {
                continue;
            }

            $id = (int)$atts['id'];
            $name = (string) $subtype->name;

            $this->expenses[$id] = new CostCategory(count($this->expenses), $name);
        }
    }

    /**
     * Reads old-style expense definitions
     * @param SimpleXMLElement $node
     */
    private function readOldExpensesAsCategories(SimpleXMLElement $node): void
    {
        foreach ($node->expense as $expense) {
            $atts = $expense->attributes();
            $id = (int)$atts['id'];
            $name = (string) $expense->name;
            $this->expenses[$id] = new CostCategory(count($this->expenses), $name);
        }
    }

    /**
     * Builds list of cost categories for Fuelio
     * @param ZipArchive $in Input archive
     * @param FuelioBackupBuilder $out Output file
     */
    protected function processCostCategories(ZipArchive $in, FuelioBackupBuilder $out): void
    {
        $this->readExpensesAsCategories($in);
        $this->readServicesAsCategories($in);

        $id_start = FuelioBackupBuilder::SAFE_CATEGORY_ID;
        foreach ($this->expenses as $expense) {
            $expense->setTypeId($expense->getTypeId() + $id_start);
            $out->writeCostCategory($expense);
        }
        $id_start += count($this->expenses);
        foreach ($this->services as $service) {
            $service->setTypeId($service->getTypeId() + $id_start);
            $out->writeCostCategory($service);
        }
    }

    /**
     * Reads XML node of a single expense (vehicles.xml)
     * @param SimpleXMLElement $expense
     * @param FuelioBackupBuilder $out
     */
    protected function processExpense(SimpleXMLElement $expense, FuelioBackupBuilder $out): void
    {
        $cost = new Cost();
        $cost->setDate($this->readDate((string) $expense->date));
        $cost->setCost((float) $expense->{'total-cost'});
        $cost->setOdo((int) $expense->{'odometer-reading'});
        // Set category
        if ($expense->expenses && $expense->expenses->expense[0]) {
            $atts = $expense->expenses->expense[0]->attributes();
            $id = (string) $atts['id'];
            if (isset($this->expenses[$id])) {
                $cost->setCostCategoryId($this->expenses[$id]->getTypeId());
            }
        }
        // Set new-format category if available
        if (isset($expense->subtypes) && $expense->subtypes->subtype[0]) {
            $atts = $expense->subtypes->subtype[0]->attributes();
            $id = (string) $atts['id'];
            if (isset($this->expenses[$id])) {
                $cost->setCostCategoryId($this->expenses[$id]->getTypeId());
            }
        }

        $notes = (string) $expense->notes;
        $notes .= ' ' . (string) $expense->{'expense-center-name'} . ' ' . (string) $expense->{'expense-center-address'};

        // Let's not blow up CSV output with multi-line entries
        $notes = str_replace("\n", ', ', trim($notes));

        // Build title, something short, let's take first line of notes, till first ','
        $title = explode("\n", (string) $expense->notes, 2);
        $title = trim($title[0]);

        if (strpos($title, ',') !== false) {
            $title = substr($title, 0, strpos($title, ',') ?: null);
        }

        $cost->setNotes(trim($notes));
        $cost->setTitle($title);
        $out->writeCost($cost);
    }

    /**
     * Reads XML node of a single service (vehicles.xml)
     * @param SimpleXMLElement $service
     * @param FuelioBackupBuilder $out
     */
    protected function processService(SimpleXMLElement $service, FuelioBackupBuilder $out): void
    {
        $cost = new Cost();
        $cost->setDate($this->readDate((string) $service->date));
        $cost->setCost((float) $service->{'total-cost'});
        $cost->setOdo((int) $service->{'odometer-reading'});
        // Set category
        if ($service->services && $service->services->service[0]) {
            $atts = $service->services->service[0]->attributes();
            $id = (string) $atts['id'];
            if (isset($this->services[$id])) {
                $cost->setCostCategoryId($this->services[$id]->getTypeId());
            }
        }

        if (isset($service->subtypes) && $service->subtypes->subtype[0]) {
            $atts = $service->subtypes->subtype[0]->attributes();
            $id = (string) $atts['id'];
            if (isset($this->services[$id])) {
                $cost->setCostCategoryId($this->services[$id]->getTypeId());
            }
        }

        $notes = (string) $service->notes;
        $notes .= ' ' . (string) $service->{'expense-center-name'} . ' ' . (string) $service->{'expense-center-address'};

        // Let's not blow up CSV output with multi-line entries
        $notes = str_replace("\n", ', ', trim($notes));

        // Build title, something short, let's take first line of notes, till first ','
        $title = explode("\n", (string) $service->notes, 2);
        $title = trim($title[0]);

        if (strpos($title, ',') !== false) {
            $title = substr($title, 0, strpos($title, ',') ?: null);
        }

        $cost->setNotes(trim($notes));
        $cost->setTitle($title);
        $out->writeCost($cost);
    }

    /**
     * Converts expenses and services to Fuelio's costs
     * @param SimpleXMLElement $data Vehicle node
     * @param ZipArchive $in Input archive
     * @param FuelioBackupBuilder $out Output file
     */
    protected function processCosts(SimpleXMLElement $data, ZipArchive $in, FuelioBackupBuilder $out): void
    {
        $out->writeCostCategoriesHeader();

        $this->processCostCategories($in, $out);

        $out->writeCoststHeader();

        if (isset($data->{'expense-records'})) {
            foreach (@$data->{'expense-records'}->{'expense-record'} as $expense) {
                $this->processExpense($expense, $out);
            }
        }

        if (isset($data->{'service-records'})) {
            foreach (@$data->{'service-records'}->{'service-record'} as $service) {
                $this->processService($service, $out);
            }
        }

        // New file format, expenses and services are now called: events
        if (isset($data->{'event-records'})) {
            foreach (@$data->{'event-records'}->{'event-record'} as $event_record) {
                $type = (string)$event_record->type;
                if ($type === 'expense') {
                    $this->processExpense($event_record, $out);
                } elseif ($type === 'service') {
                    $this->processService($event_record, $out);
                }
            }
        }
    }

    protected function processFuelTypes(ZipArchive $in): void
    {
        if ($in->statName('fuel-types.xml') !== false) {
            $this->readFuelTypes(new \SimpleXMLElement(stream_get_contents($in->getStream('fuel-types.xml'))));
        } else {
            $this->fuel_types = null;
        }
    }

    protected function readFuelTypes(SimpleXMLElement $root_node): void
    {
        $this->acar_fuels = [];
        foreach ($root_node->{'fuel-type'} as $node) {
            $atts = $node->attributes();
            $id = (int)(string)$atts['id'];

            $element = array(
                'name' => (string)$node->name,
                'category' => (string)$node->category
            );

            $this->acar_fuels[$id] = $element;
        }
        $this->fuel_types = FuelTypes::getTypes();
    }

    protected function getFuelType(int $iAcarFuelType): ?int
    {
        if (!$this->fuel_types) {
            return null;
        }

        if (!isset($this->acar_fuels[$iAcarFuelType])) {
            return null;
        }


        // Normalize fuel name
        $name = $this->acar_fuels[$iAcarFuelType]['name'];

        switch ($name) {
            case 'Autogas/LPG': $name = 'LPG/GPL'; break;
            case 'CNG - Methane': $name = 'CNG'; break;
            case 'GPL': $name = 'GPL/LPG'; break;
        }

        $fuelio_type = $this->fuel_types->findIdByName($name);
        if ($fuelio_type === -1) {
            // Not found, lets assign generic category
            switch ($this->acar_fuels[$iAcarFuelType]['category']) {
                case 'gasoline': return FuelTypes::FUEL_ROOT_GASOLINE;
                case 'diesel': return FuelTypes::FUEL_ROOT_DIESEL;
                case 'bioalcohol': return FuelTypes::FUEL_ROOT_ETHANOL;
                case 'gas': return FuelTypes::FUEL_ROOT_LPG;
            }
        }

        return $fuelio_type !== -1 ? $fuelio_type : null;
    }

    protected function determineVehicleFuelType(SimpleXMLElement $vehicle): ?int
    {
        foreach ($vehicle->{'fillup-records'}->{'fillup-record'} as $fillup) {
            $sFuelType = (string)$fillup->{'fuel-type-id'};

            $got_type = $this->getFuelType((int)$sFuelType);

            if ($got_type) {
                return $got_type;
            }
        }
        return null;
    }
}
