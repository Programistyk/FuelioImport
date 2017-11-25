<?php

namespace FuelioImporter\Form\Field;

/**
 * Numeric Field Rendered with MDL sugar
 * @package FuelioImporter\Form\Field
 * @author Kamil Kamiński
 * @see NumericField
 */
class MDLNumericField extends NumericField
{
    public function __construct($name, array $options = array())
    {
        parent::__construct($name, array_merge(array('attributes' => array('class' => 'mdl-textfield__input')), $options));
    }

    public function render()
    {
        return sprintf('<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">%s<label class="mdl-textfield__label" for="sample4">%s</label><span class="mdl-textfield__error">Input is not a number!</span></div>',
            parent::render(),
            $this->options['label']
        );
    }
}