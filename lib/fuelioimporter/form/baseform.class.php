<?php

namespace FuelioImporter\Form;

/**
 * Class for basic IForm implementation
 *
 * Implementation uses IFormField for building and processing form data
 * @package FuelioImporter\Form
 * @author Kamil Kamiński
 */
abstract class BaseForm implements IForm {
    /**
     * @var IFormField[] Internal array of form fields
     */
    protected $fields;

    /**
     * @var array Submitted data
     */
    protected $data;

    /**
     * @var \Throwable[] Array of validation errors
     */
    protected $errors;

    /**
     * @var bool Determines if form data was submitted
     */
    protected $is_submitted;

    //<editor-fold desc="Interface methods">
    public function offsetExists($offset)
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Offset name must be of string type.');
        }
        return array_key_exists($offset, $this->fields);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->fields[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if ($offset !== null && !is_string($offset)) {
            throw new \InvalidArgumentException('Offset name must be of string type.');
        }

        if (!$value instanceof IFormField) {
            throw new \InvalidArgumentException('Only IFormField instances are allowed.');
        }

        if ($offset === null) {
            $offset = $value->getName();
        }

        $this->fields[$offset] = $value;
        $value->setForm($this);
    }

    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    public function getData()
    {
        $out = array();
        foreach($this->fields as $name => $field) {
            $data = null;
            if ($field->isValid()) {
                $data = $field->getValue();
            }
            $out[$name] = $data;
        }
        return $out;
    }

    abstract public function getName();

    public function isSubmitted()
    {
        return $this->is_submitted;
    }

    public function isValid()
    {
        return $this->isSubmitted() && empty($this->errors);
    }

    public function process($post_data)
    {
        $this->is_submitted = array_key_exists($this->getName(), $post_data);
        if (!$this->is_submitted) {
            return;
        }

        foreach ($post_data[$this->getName()] as $name=>$value) {
            if (!$this->offsetExists($name)) {
                throw new \InvalidArgumentException('Unexpected form field.');
            }

            $field = $this->offsetGet($name);
            try {
                $field->setValue($value);
                if (!$field->isValid()) {
                    throw new \InvalidArgumentException('Form data is invalid');
                }
            } catch (\Throwable $ex) {
                $this->errors[$name] = $ex;
            }

            $this->data[$name] = $field->getValue();
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    public function getErrors()
    {
        return $this->errors;
    }
    //</editor-fold>
}