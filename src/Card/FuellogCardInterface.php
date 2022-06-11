<?php
namespace FuelioImporter\Card;

use FuelioImporter\Form\FuelLogForm;
use FuelioImporter\Form\FormInterface;
use FuelioImporter\CardInterface;

class FuellogCardInterface implements CardInterface
{
    public function getClass(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return 'Fuel Log';
    }

    public function getMenu(): array
    {
        return [];
    }

    public function getActions(): array
    {
        return [
            ['Help', 'popup', 'https://github.com/Programistyk/FuelioImport/wiki/Converters---FuelLog']
        ];
    }

    public function getSupporting(): string
    {
        return '<p>You can drop or upload Fuel Log\'s CSV export into this card and we will convert its data to Fuelio\'s format.</p><p>Find export option in application menu, make sure you set correct fuel type in Fuelio\'s settings.</p>';
    }

    public function getForm(): FormInterface
    {
        return new FuelLogForm();
    }
}