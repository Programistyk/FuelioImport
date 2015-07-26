<?php

namespace FuelioImporter\Providers;
use FuelioImporter\IConverter;
use FuelioImporter\FuelioBackupBuilder;

class AcarProvider implements IConverter
{
    protected $car_name = 'Acar import';
    protected $out = null;
    protected $in = null;
    
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
        if (!empty($name))
        {
            $this->car_name = $name;
        }
    }
    
    public function processFile(\SplFileObject $stream) {
        // We need to verify that we've got valid archive
        
        $this->in = new \ZipArchive();
        $in = &$this->in;
        $in->open($stream->getPathname());
        
        // todo: Get metadata.inf, read backup type (must be full), store backup creation time as comment
        // If no metadata.inf, throw error
        // list contents
        $i = 0;
        while(($stat = $in->statIndex($i++)) !== false)
        {
            print_r($stat);
        }
        
        
        $this->out = new FuelioBackupBuilder();
        $out = &$this->out;
        $out->writeVehicleHeader();
        
        $out->writeVehicle($this->car_name, 'A test data for BackupBuilder testing');
        
        $out->writeFuelLogHeader();
        
        $out->writeCostCategoriesHeader();
        
        $out->writeCoststHeader();
        
        
        $in->close();
        $out->rewind();
        return $out;
    }
    
    public function getErrors()
    {
        return array();
    }
    
    public function getWarnings() {
        return array();
    }
    
    public function getCard() {
        return new AcarCard();
    }
}