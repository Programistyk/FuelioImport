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
        return '<p>You can drop or upload Drivvo CSV export into this card and we will convert its data to Fuelio\'s format.</p><p>Export to CSV is possible in Pro version of the app. We are able to import basic fillups, expenses and service data.</p>
        <p>See help for info about units. Default units are (0 - km) and (0 - liters)';
    }

    public function getForm()
    {
        return new DrivvoForm();
    }
}