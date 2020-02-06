<?php

namespace FuelioImporter;

use Throwable;

class UploadError extends \RuntimeException
{
    public function __construct($file_array, $code = 0, Throwable $previous = null)
    {
        $message = 'File upload error';
        switch ($file_array['error']) {
            case UPLOAD_ERR_CANT_WRITE: $message = 'Cannot store file on server: Write error'; break;
            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_INI_SIZE: $message = 'Exceeded file size limit.'; break;
            case UPLOAD_ERR_NO_FILE: $message = 'No file sent'; break;
            case UPLOAD_ERR_PARTIAL:
        }
        parent::__construct($message, $code, $previous);
    }
}