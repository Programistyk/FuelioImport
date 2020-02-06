<?php

namespace FuelioImporter;

use Throwable;

class NoFileUploadedException extends \Exception
{
    public function __construct($message = 'No uploaded file, file might be too big for this server.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}