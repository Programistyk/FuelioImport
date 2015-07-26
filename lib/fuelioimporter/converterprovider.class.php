<?php

namespace FuelioImporter;

use \IteratorAggregate;
use \DirectoryIterator;

class ConverterProvider implements IteratorAggregate {

    private $providers = array();
    private $classes_loaded = false;

    // Interface impltementation
    public function getIterator() {
        if (!$this->classes_loaded)
            $this->initialize();

        return new \ArrayIterator($this->providers);
    }
    
    public function get($name)
    {
        if (!$this->classes_loaded)
            $this->initialize();
        if (isset($this->providers[$name]))
            return $this->providers[$name];
        throw new \FuelioImporter\ProviderNotExistsException();
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
                    $instance = new $classname();
                    if (isset($this->providers[$instance->getName()]))
                        throw new \FuelioImporter\ProviderExistsException();
                    
                    $this->providers[$instance->getName()] = $instance;
                }
            }
        }
        $this->classes_loaded = true;
    }

}
