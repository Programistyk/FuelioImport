<?php
namespace FuelioImporter\Providers;

use FuelioImporter\ICard;

class FuellogCard implements ICard
{
    public function getClass()
    {
        return '';
    }

    public function getTitle()
    {
        return 'Fuel Log';
    }

    public function getMenu()
    {
        return array();
    }

    public function getActions()
    {
        return array(
            array('Help', 'popup', 'https://github.com/Programistyk/FuelioImport/wiki/Converters---FuelLog')
        );
    }

    public function getSupporting()
    {
        return '<p>You can drop or upload Fuel Log\'s CSV export into this card and we will convert its data to Fuelio\'s format.</p><p>Find export option in application menu.</p>';
    }

    public function getForm()
    {
        return null;
    }
}