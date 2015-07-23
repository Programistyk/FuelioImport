<?php

// Set include_path
$libdir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib';
$path_separator = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':';

ini_set('include_path', $libdir . $path_separator . ini_get('include_path'));

require_once 'autoloader.php';