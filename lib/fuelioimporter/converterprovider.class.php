<?php

namespace FuelioImporter;

use \IteratorAggregate;
use DirectoryIterator;

class ConverterProvider implements IteratorAggregate {

    private $providers = array();
    private $classes_loaded = false;

    // Interface impltementation
    public function getIterator() {
        if (!$this->classes_loaded)
            $this->initialize();

        return new \ArrayIterator($this->providers);
    }

    /**
     * Initializes classes and propagates $providers array
     * 
     * This method reads all files in namespace FuelioImporter\Providers
     * classes implementing IConverter interface
     */
    public function initialize() {
        $di = new \DirectoryIterator(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'providers');
        foreach ($di as $spl_file) {
            if ($spl_file->isFile() && !$spl_file->isDot() && $spl_file->isReadable() && stripos($spl_file->getBasename(), 'provider.class.php') !== false) {
                
                /* We should have a new class, verify and check if it's implementing IConverter
                 * Autoloader here will do the job searching for valid file
                 * it should not misbehave as we are using FuelioImporter namespace
                 */
                
                $classname = 'FuelioImporter\\Providers\\' . ucfirst($spl_file->getBasename('.class.php'));
                
                if (!class_exists($classname, true))
                        continue;
                
                if (in_array('FuelioImporter\\IConverter', class_implements($classname, true)))
                {
                    $this->providers[] = new $classname();
                }
            }
        }
        $this->classes_loaded = true;
    }

}
