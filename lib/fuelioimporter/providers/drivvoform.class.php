<?php

namespace FuelioImporter\Providers;

use FuelioImporter\Form\BaseForm;
use FuelioImporter\Form\Field\MDLNumericField;

class DrivvoForm extends BaseForm {

    public function __construct()
    {
        $this[] = new MDLNumericField('dist_unit', array('min' => 0, 'label' => 'Distance Unit #', 'value' => 0));
        $this[] = new MDLNumericField('fuel_unit', array('min' => 0, 'label' => 'Fuel Unit #', 'value' => 0));

    }

    public function getName()
    {
        return 'drivvo';
    }
}