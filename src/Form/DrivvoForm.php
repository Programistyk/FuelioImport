<?php

declare(strict_types=1);

namespace FuelioImporter\Form;

use FuelioImporter\Form\AbstractForm;
use FuelioImporter\Form\Field\MDLNumericField;

class DrivvoForm extends AbstractForm {

    public function __construct()
    {
        $this[] = new MDLNumericField('dist_unit', array('min' => 0, 'label' => 'Distance Unit #', 'value' => 0));
        $this[] = new MDLNumericField('fuel_unit', array('min' => 0, 'label' => 'Fuel Unit #', 'value' => 0));

    }

    public function getName(): string
    {
        return 'drivvo';
    }
}
