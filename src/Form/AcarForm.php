<?php

declare(strict_types=1);

namespace FuelioImporter\Form;

use FuelioImporter\Form\Field\MDLNumericField;

class AcarForm extends BaseFormInterface {

    public function __construct()
    {
        $this[] = new MDLNumericField('vehicle_id', array('min' => 1, 'label' => 'Export vehicle #', 'value' => 1));
    }

    public function getName(): string
    {
        return 'acar';
    }
}
