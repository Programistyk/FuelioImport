<?php

declare(strict_types=1);

namespace FuelioImporter\Form;

use FuelioImporter\Form\Field\MDLNumericField;

class FuelLogForm extends AbstractForm
{
    public function __construct()
    {
        $this[] = new MDLNumericField('vehicle_id', ['min' => 1, 'label' => 'Export vehicle #', 'value' => 1]);
    }

    public function getName(): string
    {
        return 'fuellog';
    }
}
