<?php

namespace FuelioImporter\Providers;
use FuelioImporter\ICard;

class MotostatCard implements ICard {
    public function getTitle() {
        return 'Motostat';
    }
    
    public function getClass() {
        return null;
    }
    
    public function getActions() {
        return null;
    }
    
    public function getMenu() {
        return null;
    }
    
    public function getSupporting() {
        return 'You can import Motostat fueling data';
    }
}