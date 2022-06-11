<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Exception for reporting invalid provider name
 * @author Kamil Kamiński
 * @package Exceptions
 */
class ProviderNotExistsException extends \RuntimeException
{
    public function __construct($message = 'No such provider exists.', $code = null, $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
