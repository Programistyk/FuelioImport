<?php

declare(strict_types=1);

namespace FuelioImporter\Form;

/**
 * Class for basic Form implementation
 *
 * Implementation uses FormFieldInterface for building and processing form data
 * @package FuelioImporter\Form
 * @author Kamil Kamiński
 */
abstract class AbstractForm implements FormInterface
{
    /**
     * @var FormFieldInterface[] Internal array of form fields
     */
    protected array $fields;

    /**
     * @var array<string,int|float|string> Submitted data
     */
    protected array $data;

    /**
     * @var \Throwable[] Array of validation errors
     */
    protected array $errors;

    /**
     * @var bool Determines if form data was submitted
     */
    protected bool $is_submitted;

    //<editor-fold desc="Interface methods">
    public function offsetExists($offset): bool
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Offset name must be of string type.');
        }
        return array_key_exists($offset, $this->fields);
    }

    public function offsetGet($offset): ?FormFieldInterface
    {
        return $this->offsetExists($offset) ? $this->fields[$offset] : null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset !== null && !is_string($offset)) {
            throw new \InvalidArgumentException('Offset name must be of string type.');
        }

        if (!$value instanceof FormFieldInterface) {
            throw new \InvalidArgumentException('Only IFormField instances are allowed.');
        }

        if ($offset === null) {
            $offset = $value->getName();
        }

        $this->fields[$offset] = $value;
        $value->setForm($this);
    }

    public function offsetUnset($offset): void
    {
        unset($this->fields[$offset]);
    }

    public function getData(): ?iterable
    {
        $out = [];
        foreach ($this->fields as $name => $field) {
            $data = null;
            if ($field->isValid()) {
                $data = $field->getValue();
            }
            $out[$name] = $data;
        }
        return $out;
    }

    abstract public function getName(): string;

    public function isSubmitted(): bool
    {
        return $this->is_submitted;
    }

    public function isValid(): bool
    {
        return $this->isSubmitted() && empty($this->errors);
    }

    public function process($post_data): void
    {
        $this->is_submitted = array_key_exists($this->getName(), $post_data);
        if (!$this->is_submitted) {
            return;
        }

        foreach ($post_data[$this->getName()] as $name => $value) {
            $field = $this[$name] ?? null;
            if (!$field) {
                throw new \InvalidArgumentException('Unexpected form field.');
            }

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

    /** @return \ArrayIterator<string, FormFieldInterface> */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->fields);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
    //</editor-fold>
}
