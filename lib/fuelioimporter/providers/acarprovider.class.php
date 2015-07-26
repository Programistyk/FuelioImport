<?php

namespace FuelioImporter\Providers;

use \ZipArchive;
use \SplFileObject;
use FuelioImporter\IConverter;
use FuelioImporter\FuelioBackupBuilder;
use FuelioImporter\Vehicle;
use FuelioImporter\FuelLogEntry;
use \SimpleXMLElement;
use \DateTime;

class AcarProvider implements IConverter {

    protected $car_name = 'Acar import';
    protected $archive_files;
    protected $metadata = array();
    protected $preferences = array();

    public function getName() {
        return 'acar';
    }

    public function getTitle() {
        return 'aCar ABP';
    }

    public function getStylesheetLocation() {
        return null;
    }

    public function setCarName($name) {
        if (!empty($name)) {
            $this->car_name = $name;
        }
    }

    public function processFile(SplFileObject $stream) {
        // We need to verify that we've got valid archive

        $in = new ZipArchive();
        $out = new FuelioBackupBuilder();

        $in->open($stream->getPathname());

        // If no metadata.inf, throw error
        // list contents
        $i = 0;
        while (($stat = $in->statIndex($i++)) !== false) {
            $normalized_name = strtolower($stat['name']);
            $this->archive_files[$normalized_name] = $stat;
        }

        $this->validateInputFile($in, $out);

        $this->readPreferences($in, $out);

        // Read vehicle data
        $data = $this->getVehicle(0, $in);
        // Process vehicle header
        $this->processVehicle($data, $in, $out);

        // Process fuellings
        $this->processFuellings($data, $in, $out);

        $out->writeCostCategoriesHeader();

        $out->writeCoststHeader();


        $in->close();
        $out->rewind();
        return $out;
    }

    public function getErrors() {
        return array();
    }

    public function getWarnings() {
        return array();
    }

    public function getCard() {
        return new AcarCard();
    }

    protected function readPreferences(ZipArchive $in, FuelioBackupBuilder $out) {
        // Read all preferences and store them as array
        $xml = new \SimpleXMLElement(stream_get_contents($in->getStream('preferences.xml')));
        foreach ($xml->preference as $node) {
            $atts = $node->attributes();
            $key = (string) $atts['name'];
            $type = (string) $atts['type'];
            $value = (string) $node;

            if ($type == 'java.lang.Boolean') {
                $value = ($value == 'true');
            }

            $this->preferences[$key] = $value;
        }
    }

    protected function validateInputFile(ZipArchive $in, FuelioBackupBuilder $out) {
        if (!isset($this->archive_files['metadata.inf']))
            throw new \FuelioImporter\InvalidFileFormatException();

        $metastream = $in->getStream('metadata.inf');
        if (false === $metastream)
            throw new \FuelioImporter\InvalidFileFormatException();

        while (!feof($metastream)) {
            $entry = explode('=', fgets($metastream), 2);
            if (count($entry) == 2)
                $this->metadata[trim($entry[0])] = trim($entry[1]);
        }

        // At this moment we support only full backups
        if (@$this->metadata['acar.backup.type'] != 'Full-Backup')
            throw new \FuelioImporter\InvalidFileFormatException('At this moment we support only Full Backups!');
    }

    public function processVehicle(SimpleXMLElement $data, ZipArchive $in, FuelioBackupBuilder $out) {
        $out->writeVehicleHeader();
        $vehicle = new Vehicle($this->car_name, '');

        $vehicle->setName($data->name);
        $vehicle->setDescription((string) $data->notes);
        $vehicle->setMake($data->make);
        $vehicle->setModel($data->model);
        $vehicle->setYear($data->year);
        $vehicle->setPlate($data->{'license-plate'});
        $vehicle->setVIN($data->vin);
        $vehicle->setInsurance($data->{'insurance-policy'});
        $vehicle->setFuelUnit($this->getFuelUnit());
        $vehicle->setDistanceUnit($this->getDistanceUnit());
        $vehicle->setConsumptionUnit($this->getConsumptionUnit());

        $out->writeVehicle($vehicle);
    }

    protected function getFuelUnit() {
        // TODO: How does aCar mark US / UK gallons?
        switch ($this->preferences['acar.volume-unit']) {
            case 'L':
            default:
                return Vehicle::LITRES;
        }
    }

    protected function getDistanceUnit() {
        switch ($this->preferences['acar.distance-unit']) {
            case 'm':
            case 'mi' :
                return Vehicle::MILES;
            case 'km':
            default:
                return Vehicle::KILOMETERS;
        }
    }

    protected function getConsumptionUnit() {
        switch ($this->preferences['acar.fuel-efficiency-unit']) {
            case 'L/100km':
            default:
                return Vehicle::L_PER_100KM;
        }
    }

    /**
     * Returns handle to vehicle data in vehicles.xml
     * @param integer $iVehicle Vehicle number
     * @param ZipArchive $in
     * @return SimpleXmlElement
     * @throws \FuelioImporter\InvalidFileFormatException
     */
    protected function getVehicle($iVehicle, ZipArchive $in) {
        $stream = $in->getStream('vehicles.xml');
        $xml = new \SimpleXMLElement(stream_get_contents($stream));
        if (!$xml) {
            throw new \FuelioImporter\InvalidFileFormatException();
        }
        $children = $xml->children();
        if (empty($children)) {
            throw new \FuelioImporter\InvalidFileFormatException();
        }
        return $children[$iVehicle];
    }

    protected function processFuellings(SimpleXMLElement $data, ZipArchive $in, FuelioBackupBuilder $out) {
        $out->writeFuelLogHeader();

        foreach ($data->{'fillup-records'}->{'fillup-record'} as $record) {
            $entry = new FuelLogEntry();
            // Date format is ugly..
            $date_string = (string) $record->date;
            $dt = DateTime::createFromFormat('m/d/Y - H:i', $date_string);
            $entry->setDate($dt->format(DateTime::ATOM));

            $entry->setFuel((string) $record->volume);
            $entry->setPrice((string) $record->{'total-cost'});
            $entry->setOdo((string) $record->{'odometer-reading'});
            $entry->setConsumption((string) $record->{'fuel-efficiency'});
            $entry->setGeoCoords((string) $record->latitude, (string) $record->longitude);
            $entry->setFullFillup((string) $record->partial != 'true');
            $entry->setMissedEntries((string) $record->{'previous-missed-fillups'} == 'true');
            $notes = (string) $record->{'fuel-brand'} . ' ' . (string) $record->{'fueling-station-address'} . ' ' . (string) $record->notes;
            $entry->setNotes(trim($notes));
            $out->writeFuelLog($entry);
        }
    }

}
