<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Exception for reporting invalid units
 * @author Kamil Kamiński
 * @package Exceptions
 */
class InvalidUnitException extends \RuntimeException
{
    public function __construct(string $message = 'Invalid unit specified.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
