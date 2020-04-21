<?php

/**
 * Basic autoloader implementation
 * @param string $class Class name
 */
function my_autoloader($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, strtolower($class));
    require_once($file . '.class.php');
}

// If PHP is new enough lets behave like civilized developer
if (function_exists('spl_autoload_register')) {
    spl_autoload_register('my_autoloader');
} else {
    // Dark ancient times...
    function __autoload($class) {
        return my_autoloader($class);
    }
}