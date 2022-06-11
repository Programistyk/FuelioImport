<?php

declare(strict_types=1);

namespace FuelioImporter\Form;

use FuelioImporter\Form\BaseFormInterface;
use FuelioImporter\Form\Field\MDLNumericField;

class DrivvoForm extends BaseFormInterface {

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
