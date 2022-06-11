<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Exception for reporting invalid input file format
 * @author Kamil Kamiński
 * @package Exceptions
 */
class InvalidFileFormatException extends \RuntimeException {

    public function __construct($message = 'Provided file is in invalid format.', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
