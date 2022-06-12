<?php

declare(strict_types=1);

namespace FuelioImporter\Providers;

use FuelioImporter\Card\MotostatCard;
use FuelioImporter\CardInterface;
use FuelioImporter\ProviderInterface;
use FuelioImporter\FuelioBackupBuilder;
use FuelioImporter\CostCategory;
use FuelioImporter\Cost;
use FuelioImporter\FuelLogEntry;
use FuelioImporter\InvalidFileFormatException;
use FuelioImporter\Vehicle;
use SplFileObject;

class MotostatProvider implements ProviderInterface
{
    protected string $car_name = 'Motostat import';
    /** @var array<array<string|null>> */
    protected array $costs_data = [];
    /** @var array<string,CostCategory> */
    protected array $categories = [];
    /** @var list<Cost> */
    protected array $costs = [];

    public function __construct() {
        // keys are normalized categories names (see findCategory)
        $this->categories = [
            'purchase_price' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 1, 'Purchase price'),
            'tech_inspection' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 2, 'Tech inspection'),
            'miscellaneous' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 3, 'Miscellaneous'),
            'repair' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 4, 'Repair'),
            'maintenance' => new CostCategory(2, 'Maintenance'),
            'tires_change' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 5, 'Tires change'),
            'care' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 6, 'Care'),
            'spare_parts' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 7, 'Spare parts'),
            'inspection' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 8, 'inspection'),
            'oil_change' => new CostCategory(FuelioBackupBuilder::SAFE_CATEGORY_ID + 9, 'Oil change'),
            'insurance' => new CostCategory(31, 'Insurance')
        ];
    }

    public function getName(): string
    {
        return 'motostat';
    }

    public function getTitle(): string
    {
        return 'Motostat';
    }

    public function getOutputFileName(): string
    {
        return $this->getTitle();
    }

    public function getCard(): CardInterface
    {
        return new MotostatCard();
    }

    public function setCarName($name): void
    {
        if (!empty($name)) {
            $this->car_name = $name;
        }
    }

    public function getErrors(): array
    {
        return [];
    }

    public function getWarnings(): array
    {
        return [];
    }

    public function getStylesheetLocation(): ?string
    {
        return null;
    }

    public function processFile(\SplFileObject $in, ?iterable $form_data): FuelioBackupBuilder
    {
        if ($in->isDir() || ($in->isFile() && !$in->isReadable())) {
            throw new InvalidFileFormatException();
        }

        $out = new FuelioBackupBuilder();

        $in->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        // Verify file header
        $head = $in->fgetcsv(';');

        if (!$head || count($head) !== 23) {
            throw new InvalidFileFormatException();
        }

        // First column might have BOM
        $hasBOM = strpos($head[0] ?? '', pack('CCC', 239, 187, 191)) === 0;
        if ($hasBOM) {
            $head[0] = substr($head[0] ?? '', 3);
        }

        if ($head[0] !== 'cost_id') {
            throw new InvalidFileFormatException();
        }

        $out->writeVehicleHeader();
        $out->writeVehicle(new Vehicle($this->car_name, 'Motostat CSV conversion'));

        // process fuel log and prepare cost_data
        $this->processFuelings($in, $out);

        // process cost data and categories
        $this->processCostData($out);

        // empty memory
        $this->cleanup();
        return $out;
    }

    //﻿cost_id;fueling_id;cost_type;date;fuel_id;gas_station_id;odometer;trip_odometer;quantity;cost;notes;fueling_type;tires;driving_style;route_motorway;route_country;route_city;bc_consumption;bc_avg_speed;ac;currency;fuel_name;gas_station_name
    protected function processFuelings(SplFileObject $in, FuelioBackupBuilder $out): void
    {
        $out->writeFuelLogHeader();
        while (!$in->eof() && (($log = $in->fgetcsv(';')) !== false)) {
            // log only fillups, costs have dedicated category
            if (!empty($log[0])) {
                $this->costs_data[] = $log;
                continue;
            }
            if (empty($log[1])) {
                continue; // no fueling_id
            }

            $entry = new FuelLogEntry();
            $entry->setDate((string)$log[3]);
            $entry->setOdo((int)$log[6]);
            $entry->setFuel((float)$log[8]);
            $entry->setFullFillup($log[11] === 'full');
            $entry->setPrice((float)$log[9]);
            $entry->setConsumption((float)$log[18]);
            $entry->setNotes((string)$log[21]);

            $out->writeFuelLog($entry);
        }
    }

    /**
     * Initializes costs and cost_categories arrays
     */
    protected function processCostData(FuelioBackupBuilder $out): void
    {
        foreach ($this->costs_data as $line) {
            $category = $this->findCategory((string)$line[2]);
            $cost = new Cost();
            $cost->setTitle($category->getName());
            $cost->setDate((string)$line[3]);
            $cost->setOdo((int)$line[6]);
            $cost->setCostCategoryId($category->getTypeId());
            $cost->setNotes((string)$line[10]);
            $cost->setCost((float)$line[9]);

            $this->costs[] = $cost;
        }
        $this->costs_data = array();

        $out->writeCostCategoriesHeader();
        foreach ($this->categories as $category) {
            $out->writeCostCategory($category);
        }

        $out->writeCoststHeader();

        foreach ($this->costs as $cost) {
            $out->writeCost($cost);
        }
    }

    /**
     * Returns CostCategory instance
     * @param string $category_name Category name
     */
    protected function findCategory(string $category_name): CostCategory
    {
        // Normalize name
        $normalized_category_name = strtolower(trim($category_name));

        // Return if already exists
        if (array_key_exists($normalized_category_name, $this->categories)) {
            return $this->categories[$normalized_category_name];
        }

        // Add new category
        $category = new CostCategory(count($this->categories) + 1, $category_name);
        $this->categories[$normalized_category_name] = $category;
        return $category;
    }

    protected function cleanup(): void
    {
        $this->costs = [];
        $this->categories = [];
    }
}
