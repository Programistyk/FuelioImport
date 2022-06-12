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
    public function __construct(string $message = 'No such provider exists.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
