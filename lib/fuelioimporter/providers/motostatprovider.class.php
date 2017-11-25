<?php

namespace FuelioImporter\Providers;

use FuelioImporter\IConverter;
use FuelioImporter\FuelioBackupBuilder;
use FuelioImporter\CostCategory;
use FuelioImporter\Cost;
use FuelioImporter\FuelLogEntry;
use FuelioImporter\InvalidFileFormatException;
use FuelioImporter\Vehicle;
use \SplFileObject;

class MotostatProvider implements IConverter {

    protected $car_name = 'Motostat import';
    protected $costs_data = array();
    // @see __construct
    protected $categories = array();
    protected $costs = array();

    public function __construct() {
        // keys are normalized categories names (see findCategory)
        $this->categories = array(
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
        );
    }

    public function getName() {
        return 'motostat';
    }

    public function getTitle() {
        return 'Motostat';
    }

    public function getCard() {
        return new MotostatCard();
    }

    public function setCarName($name) {
        if (!empty($name)) {
            $this->car_name = $name;
        }
    }

    public function getErrors() {
        return array();
    }

    public function getWarnings() {
        return array();
    }

    public function getStylesheetLocation() {
        return null;
    }

    public function processFile(\SplFileObject $in, $form_data) {
        if ($in->isDir() || ($in->isFile() && !$in->isReadable())) {
            throw new InvalidFileFormatException();
        }

        $out = new FuelioBackupBuilder();

        $in->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        // Verify file header
        $head = $in->fgetcsv(';');

        if (count($head) !== 23) {
            throw new InvalidFileFormatException();
        }

        // First column might have BOM
        $hasBOM = substr($head[0], 0, 3) === pack('CCC', 239, 187, 191);
        if ($hasBOM) {
            $head[0] = substr($head[0], 3);
        }

        if ($head[0] !== 'cost_id') {
            throw new InvalidFileFormatException();
        }

        $out->writeVehicleHeader();
        $out->writeVehicle(new Vehicle($this->car_name, 'Motostat CSV conversion'));

        // process fuel log and prepare cost_data
        $this->processFuelings($in, $out);

        // process cost data and categories
        $this->processCostData($in, $out);

        // empty memory
        $this->cleanup();
        return $out;
    }

    //﻿cost_id;fueling_id;cost_type;date;fuel_id;gas_station_id;odometer;trip_odometer;quantity;cost;notes;fueling_type;tires;driving_style;route_motorway;route_country;route_city;bc_consumption;bc_avg_speed;ac;currency;fuel_name;gas_station_name
    protected function processFuelings(SplFileObject $in, FuelioBackupBuilder $out) {
        $out->writeFuelLogHeader();
        while (!$in->eof() && (($log = $in->fgetcsv(';')) !== false)) {
            // log only fillups, costs have dedicated category
            if (!empty($log[0])) {
                $this->costs_data[] = $log;
                continue;
            }
            if (empty($log[1]))
                continue; // no fueling_id

            $entry = new FuelLogEntry();
            $entry->setDate($log[3]);
            $entry->setOdo($log[6]);
            $entry->setFuel($log[8]);
            $entry->setFullFillup($log[11] === 'full');
            $entry->setPrice($log[9]);
            $entry->setConsumption($log[18]);
            $entry->setNotes($log[21]);

            $out->writeFuelLog($entry);
        }
    }

    /**
     * Initializes costs and cost_categories arrays
     */
    protected function processCostData(SplFileObject $in, FuelioBackupBuilder $out) {
        foreach ($this->costs_data as $line) {
            $category = $this->findCategory($line[2]);
            $cost = new Cost();
            $cost->setTitle($category->getName());
            $cost->setDate($line[3]);
            $cost->setOdo($line[6]);
            $cost->setCostCategoryId($category->getTypeId());
            $cost->setNotes($line[10]);
            $cost->setCost($line[9]);

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
     * @return CostCategory
     */
    protected function findCategory($category_name) {
        // Normalize name
        $normalized_category_name = strtolower(trim($category_name));

        // Return if already exists
        if (array_key_exists($normalized_category_name, $this->categories)) {
            return $this->categories[$normalized_category_name];
        }

        // Add new category
        $category = new CostCategory(count($this->categories) + 1, $category_name);
        $this->categories[] = $category;
        return $category;
    }

    protected function cleanup() {
        $this->costs = array();
        $this->categories = array();
    }

}
