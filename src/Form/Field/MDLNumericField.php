<?php

declare(strict_types=1);

namespace FuelioImporter\Form\Field;

/**
 * Numeric Field Rendered with MDL sugar
 * @package FuelioImporter\Form\Field
 * @author Kamil Kamiński
 * @see NumericField
 */
class MDLNumericField extends NumericField
{
    public function __construct(string $name, array $options = [])
    {
        parent::__construct(
            $name,
            array_merge(
                [
                    'attributes' => [
                        'class' => 'mdl-textfield__input'
                    ]
                ],
                $options
            )
        );
    }

    public function render(): string
    {
        return sprintf(
            '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">%s<label class="mdl-textfield__label" for="sample4">%s</label><span class="mdl-textfield__error">Input is not a number!</span></div>',
            parent::render(),
            $this->options['label']
        );
    }
}
