<?php

namespace FuelioImporter\Form;

/**
 * Interface for cards configuration
 * @package FuelioImporter\Form
 * @author Kamil Kamiński
 */
interface IForm extends \ArrayAccess, \IteratorAggregate {
    /**
     * Processes $_POST data for form
     * @param $post_data array $_POST
     * @return void
     */
    public function process($post_data);

    /**
     * Returns form identification string for name=
     * @return string
     */
    public function getName();

    /**
     * Checks processed data for validation errors
     * @return bool
     */
    public function isValid();

    /**
     * Returns if processed data had form fields attached
     * @return bool
     */
    public function isSubmitted();

    /**
     * Returns processed data
     * @return \Traversable|null
     */
    public function getData();

    /**
     * Returns collected validation errors
     * @return \Throwable[]
     */
    public function getErrors();

}