<?php

namespace FuelioImporter;

/**
 * Exception for reporting invalid units
 * @author Kamil Kamiński
 * @package Exceptions
 */
class InvalidUnitException extends \Exception {
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct($message ? $message : 'Invalid unit specified.', $code, $previous);
    }
}