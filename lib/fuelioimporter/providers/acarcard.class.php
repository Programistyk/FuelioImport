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
        // todo: One day maybe we will use a proper class
        return array(
            array('Help', 'popup', 'acarhelp.html')
        );
    }
    
    public function getSupporting() {
        return '<p>You can upload an .abp backup file from your aCar and we will convert it into Fuelio\'s CSV format. Just tap this card, or drop a file onto it.</p><p>You can also import backups from aCar free version. Use Help button below to read more.</p>';
    }
}