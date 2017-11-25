<?php


namespace FuelioImporter\Form\Field;

use FuelioImporter\Form\FormValidatorException;
use FuelioImporter\Form\IForm;
use FuelioImporter\Form\IFormField;

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
class NumericField implements IFormField
{
    /**
     * @var array Internal field options
     */
    protected $options;

    /**
     * @var mixed Raw field value as processed by form
     */
    protected $raw_value = null;

    /**
     * @var int|null normalized integer
     */
    protected $value = null;

    /**
     * @var string Field name
     */
    protected $name;

    /**
     * @var IForm Parent form
     */
    protected $form;

    public function __construct($name, $options = array())
    {
        $defaults = array('min' => 1, 'label' => $name, 'attributes' => array());

        $this->options = array_merge($defaults, $options);

        if ($name === null || empty((string)$name)) {
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
    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isValid()
    {
        if (!empty($this->value) && !is_numeric($this->value)) {
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

    public function setForm(IForm $form)
    {
        $this->form = $form;
    }

    public function setValue($value)
    {
        $this->raw_value = $value;

        $this->value = $this->normalizeValue($value);
    }

    public function render()
    {
        return sprintf('<input type="number" name="%s" value="%s"%s/>',
            addcslashes($this->form ? sprintf('%s[%s]', $this->form->getName(), $this->getName()) : $this->getName(), '"'),
            addcslashes((string)$this->getValue(), '"'),
            $this->getRenderingAttributes($this->options['attributes'])
        );
    }
    //</editor-fold>

    protected function normalizeValue($value) {
        return intval($value, 10);
    }

    protected function getRenderingAttributes($attributes) {
        $vals = array();
        foreach ($attributes as $name=>$value) {
            $vals[] = sprintf('%s="%s"', str_replace(' ', '_', $name), addcslashes($value,'"'));
        }
        return implode(' ', $vals);
    }
}