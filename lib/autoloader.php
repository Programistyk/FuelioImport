<?php

/**
 * Basic autoloader implementation
 * @param string $class Class name
 */
function my_autoloader($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, strtolower($class));
    require_once($file . '.class.php');
}

if (PHP_VERSION_ID < 50102) {
    die('You are using PHP version that is too old for this code.');
}

spl_autoload_register('my_autoloader');