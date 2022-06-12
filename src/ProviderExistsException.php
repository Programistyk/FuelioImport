<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Exception thrown if conflicting provider name is found
 * @author Kamil Kamiński
 * @package Exceptions
 */
class ProviderExistsException extends \RuntimeException
{
    public function __construct(string $message = 'Provider already exists.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
