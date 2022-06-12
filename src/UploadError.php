<?php

declare(strict_types=1);

namespace FuelioImporter;

use Throwable;

class UploadError extends \RuntimeException
{
    /** @param array<string,mixed> $file_array */
    public function __construct(array $file_array, int $code = 0, ?Throwable $previous = null)
    {
        switch ($file_array['error']) {
            case UPLOAD_ERR_CANT_WRITE: $message = 'Cannot store file on server: Write error'; break;
            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_INI_SIZE: $message = 'Exceeded file size limit.'; break;
            case UPLOAD_ERR_NO_FILE: $message = 'No file sent'; break;
            case UPLOAD_ERR_PARTIAL:
            default: $message = 'File upload error'; break;
        }
        parent::__construct($message, $code, $previous);
    }
}
