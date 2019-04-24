<?php
namespace FuelioImporter\Providers;

use FuelioImporter\ICard;

class DrivvoCard implements ICard
{
    public function getClass()
    {
        return '';
    }

    public function getTitle()
    {
        return 'Drivvo';
    }

    public function getMenu()
    {
        return array();
    }

    public function getActions()
    {
        return array(
            array('Help', 'popup', 'https://github.com/Programistyk/FuelioImport/wiki/Converters---Drivvo')
        );
    }

    public function getSupporting()
    {
        return '<p>You can drop or upload Drivvo CSV export into this card and we will convert its data to Fuelio\'s format.</p><p>Find export option in application menu, make sure you set correct fuel type in Fuelio\'s settings.</p>';
    }

    public function getForm()
    {
        return new DrivvoForm();
    }
}