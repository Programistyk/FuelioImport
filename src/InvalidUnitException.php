<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Exception for reporting invalid units
 * @author Kamil Kamiński
 * @package Exceptions
 */
class InvalidUnitException extends \RuntimeException {
    public function __construct($message = 'Invalid unit specified.', $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
