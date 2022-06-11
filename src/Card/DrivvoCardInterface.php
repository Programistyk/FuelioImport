<?php
namespace FuelioImporter\Card;

use FuelioImporter\Form\DrivvoForm;
use FuelioImporter\Form\FormInterface;
use FuelioImporter\CardInterface;

class DrivvoCardInterface implements CardInterface
{
    public function getClass(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return 'Drivvo';
    }

    public function getMenu(): array
    {
        return [];
    }

    public function getActions(): array
    {
        return [
            ['Help', 'popup', 'https://github.com/Programistyk/FuelioImport/wiki/Converters---Drivvo']
        ];
    }

    public function getSupporting(): string
    {
        return '<p>You can drop or upload Drivvo CSV export into this card and we will convert its data to Fuelio\'s format.</p><p>Export to CSV is possible in Pro version of the app. We are able to import basic fillups, expenses and service data.</p>
        <p>See help for info about units. Default units are (0 - km) and (0 - liters)';
    }

    public function getForm(): FormInterface
    {
        return new DrivvoForm();
    }
}
