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
        return array(
            array('Motostat', 'popup', 'https://www.motostat.pl/member/vehicles'),
            array('Help', 'popup', 'https://github.com/Programistyk/FuelioImport/wiki/Converters---Motostat')
        );
    }
    
    public function getMenu() {
        return null;
    }
    
    public function getSupporting() {
        return '<p>You can upload your <span class="">motostat.csv</span> file here and we will convert it into Fuelio\'s CSV format. Just tap this card or drop file onto it.</p><p>To export your car data, open Motostat, select your car and click on "Export". Make sure to select both checkboxes!';
    }

    public function getForm()
    {
        return null;
    }
}