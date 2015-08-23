<?php

namespace FuelioImporter;

/**
 * Exception for reporting invalid provider name
 * @author Kamil Kamiński
 * @package Exceptions
 */
class ProviderNotExistsException extends \Exception {
    
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct($message ? $message : 'No such provider exists.', $code, $previous);
    }};
