<?php

// Set include_path
$libdir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib';
$path_separator = stripos(PHP_OS, 'WIN') === 0 ? ';' : ':';

ini_set('include_path', $libdir . $path_separator . ini_get('include_path'));

// set debug if we are working on localhost
if (in_array($_SERVER['REMOTE_ADDR'], array('::1', '127.0.0.1'))) {
    define('DEBUG', true);
}

require_once $libdir . DIRECTORY_SEPARATOR . 'autoloader.php';
