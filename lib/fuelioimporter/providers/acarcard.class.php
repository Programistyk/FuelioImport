<?php

namespace FuelioImporter\Providers;
use FuelioImporter\ICard;

class AcarCard implements ICard {
    public function getClass()
    {
        return '';
    }
    
    public function getTitle() {
        return 'aCar ABP Converter for Fuelio';
    }
    
    public function getMenu() {
        return array();
    }
    
    public function getActions() {
        return array();
    }
    
    public function getSupporting() {
        return '';
    }
}