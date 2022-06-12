<?php

declare(strict_types=1);

namespace FuelioImporter\Card;

use FuelioImporter\Form\FormInterface;
use FuelioImporter\CardInterface;

class MotostatCard implements CardInterface
{
    public function getTitle(): string
    {
        return 'Motostat';
    }
    
    public function getClass(): string
    {
        return '';
    }
    
    public function getActions(): array
    {
        return [
            ['Motostat', 'popup', 'https://www.motostat.pl/member/vehicles'],
            ['Help', 'popup', 'https://github.com/Programistyk/FuelioImport/wiki/Converters---Motostat'],
        ];
    }
    
    public function getMenu(): array
    {
        return [];
    }
    
    public function getSupporting(): string
    {
        return '<p>You can upload your <span class="">motostat.csv</span> file here and we will convert it into Fuelio\'s CSV format. Just tap this card or drop file onto it.</p><p>To export your car data, open Motostat, select your car and click on "Export". Make sure to select both checkboxes!';
    }

    public function getForm(): ?FormInterface
    {
        return null;
    }
}
