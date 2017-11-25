<?php

namespace FuelioImporter\Form;

/**
 * Interface for form fields
 * @package FuelioImporter\Form
 * @author Kamil Kamiński
 */
interface IFormField {

    /**
     * Field name, used for name= attribute
     * @return string
     */
    public function getName();

    /**
     * Renders HTML string of field
     * @return string
     */
    public function render();

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
    public function setValue($value);

    /**
     * Runs validation of field data
     * @return bool Runs data validation
     * @throws \Throwable on error
     */
    public function isValid();

    /**
     * Sets pointer to parent form
     * @param IForm $form Field form
     * @return void
     */
    public function setForm(IForm $form);
}