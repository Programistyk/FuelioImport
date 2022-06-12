<?php

declare(strict_types=1);

namespace FuelioImporter;

use Throwable;

class NoFileUploadedException extends \RuntimeException
{
    public function __construct(string $message = 'No uploaded file, file might be too big for this server.', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
