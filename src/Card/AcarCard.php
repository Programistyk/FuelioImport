<?php

declare(strict_types=1);

namespace FuelioImporter\Card;

use FuelioImporter\Form\AcarForm;
use FuelioImporter\Form\FormInterface;
use FuelioImporter\CardInterface;

class AcarCard implements CardInterface
{
    public function getClass(): string
    {
        return '';
    }
    
    public function getTitle(): string
    {
        return 'aCar ABP Converter for Fuelio';
    }
    
    public function getMenu(): array
    {
        return [];
    }
    
    public function getActions(): array
    {
        return [
            ['Help', 'popup', 'https://github.com/Programistyk/FuelioImport/wiki/Converters---aCar']
        ];
    }
    
    public function getSupporting(): string
    {
        return '<p>You can upload an .abp backup file from your aCar and we will convert it into Fuelio\'s CSV format. Just tap this card, or drop a file onto it.</p><p>You can also import backups from aCar free version. Use Help button below to read more.</p>';
    }

    public function getForm(): FormInterface
    {
        return new AcarForm();
    }
}
