<?php

// set debug if we are working on localhost
if (in_array(@$_SERVER['REMOTE_ADDR'], ['::1', '127.0.0.1'])) {
    define('DEBUG', true);
}

require_once __DIR__ . '/../vendor/autoload.php';
