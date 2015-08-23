<?php

namespace FuelioImporter;

/**
 * Exception for reporting invalid input file format
 * @author Kamil Kamiński
 * @package Exceptions
 */
class InvalidFileFormatException extends \Exception {

    public function __construct($message = null, $code = 0, $previous = null) {
        parent::__construct($message ? $message : 'Provided file is in invalid format.', $code, $previous);
    }

}