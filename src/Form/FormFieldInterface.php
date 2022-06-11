<?php

declare(strict_types=1);

namespace FuelioImporter\Form;

/**
 * Interface for form fields
 * @package FuelioImporter\Form
 * @author Kamil Kamiński
 */
interface FormFieldInterface {

    /**
     * Field name, used for name= attribute
     */
    public function getName(): string;

    /**
     * Renders HTML string of field
     */
    public function render(): string;

    /**
     * Returns normalized field data
     * @return mixed
     */
    public function getValue();

    /**
     * Sets field value, normalizes and validates
     * @param $value string Denormalized value
     * @return void
     */
    public function setValue(string $value): void;

    /**
     * Runs validation of field data
     * @return bool Runs data validation
     * @throws \Throwable on error
     */
    public function isValid(): bool;

    /**
     * Sets pointer to parent form
     * @param FormInterface $form Field form
     */
    public function setForm(FormInterface $form): void;
}
