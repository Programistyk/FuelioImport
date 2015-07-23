<?php

namespace FuelioImporter\Providers;
use FuelioImporter\IConverter;

class AcarProvider implements IConverter
{
    public function getName() {
        return 'acar';
    }
    
    public function getTitle() {
        return 'aCar ABD';
    }
    
    public function getStylesheetLocation() {
        return null;
    }
    
    public function processFile(\FuelioImporter\SplFileObject $stream) {
        throw new Exception('Not implemented yet');
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