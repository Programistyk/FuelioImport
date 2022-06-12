<?php

declare(strict_types=1);

namespace FuelioImporter\Form\Field;

use FuelioImporter\Form\FormValidatorException;
use FuelioImporter\Form\FormInterface;
use FuelioImporter\Form\FormFieldInterface;

/**
 * Numeric input field
 * @package FuelioImporter\Form\Field
 * @author Kamil Kamiński
 *
 * Supports attributes:
 * <ul><li>min => minimal value</li>
 * <li>label => field label</li>
 * <li>attributes => html attributes</li></ul>
 */
class NumericField implements FormFieldInterface
{
    /**
     * @var array<string,mixed> Internal field options
     */
    protected array $options;

    /**
     * @var mixed Raw field value as processed by form
     */
    protected $raw_value = null;

    /**
     * @var int|null normalized integer
     */
    protected ?int $value = null;

    /**
     * @var string Field name
     */
    protected string $name;

    /**
     * @var ?FormInterface Parent form
     */
    protected ?FormInterface $form = null;

    /** @param array<string,mixed> $options */
    public function __construct(string $name, array $options = [])
    {
        $defaults = array('min' => 1, 'label' => $name, 'attributes' => []);

        $this->options = array_merge($defaults, $options);

        if (empty($name)) {
            throw new \InvalidArgumentException('Field needs a name!');
        }
        $this->name = $name;
        if (array_key_exists('value', $options)) {
            $this->setValue($options['value']);
        }

        $attributes = array('min' => $this->options['min']);
        $this->options['attributes'] = array_merge($attributes, $this->options['attributes']);
    }

    //<editor-fold desc="Interface methods">
    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isValid(): bool
    {
        if (!empty($this->value)) {
            $val = '';
            if (function_exists('mb_strimwidth')) {
                $val = mb_strimwidth($val, 0, 20, '…');
            } else {
                $val = substr($val, 0, 20);
            }
            throw new FormValidatorException($val.' is not a valid numeric value.');
        }

        if (array_key_exists('min', $this->options) && $this->value < $this->options['min']) {
            throw new FormValidatorException('Field value must be bigger or equal to ' . $this->options['min']);
        }

        return true;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    /** @param float|int|string $value */
    public function setValue($value): void
    {
        $this->raw_value = $value;

        $this->value = $this->normalizeValue($value);
    }

    public function render(): string
    {
        return sprintf(
            '<input type="number" name="%s" value="%s"%s/>',
            addcslashes($this->form ? sprintf('%s[%s]', $this->form->getName(), $this->getName()) : $this->getName(), '"'),
            addcslashes((string)$this->getValue(), '"'),
            $this->getRenderingAttributes($this->options['attributes'])
        );
    }
    //</editor-fold>

    /** @param string|int|float $value */
    protected function normalizeValue($value): int
    {
        return (int) $value;
    }

    /** @param iterable<string,string|int|float> $attributes */
    protected function getRenderingAttributes(iterable $attributes): string
    {
        $vals = array();
        foreach ($attributes as $name => $value) {
            $vals[] = sprintf('%s="%s"', str_replace(' ', '_', $name), addcslashes((string) $value, '"'));
        }
        return implode(' ', $vals);
    }
}
