<?php

declare(strict_types=1);

namespace FuelioImporter;

use ArrayIterator;
use DirectoryIterator;
use IteratorAggregate;

/**
 * Converters provider
 *
 * Provides interface for converting plugins detection and loading
 * @author Kamil Kamiński
 * @implements IteratorAggregate<string, ProviderInterface>
 */
class ConverterProvider implements IteratorAggregate
{
    /** @var array<string,ProviderInterface> Internal storage of found providers */
    protected array $providers = [];
    /** @var boolean $classes_loaded Flag for autodetecting available plugins */
    protected bool $classes_loaded = false;

    /**
     * Interface implementation for iterating over available plugins
     * @return ArrayIterator<string, ProviderInterface>
     */
    public function getIterator(): ArrayIterator
    {
        if (!$this->classes_loaded) {
            $this->initialize();
        }

        return new ArrayIterator($this->providers);
    }

    /**
     * Returns converter by its name
     * @param string $name Converter name
     * @return ProviderInterface
     * @throws ProviderNotExistsException
     */
    public function get(string $name): ProviderInterface
    {
        if (!$this->classes_loaded) {
            $this->initialize();
        }
        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }
        throw new ProviderNotExistsException();
    }

    /**
     * Initializes classes and propagates $providers array
     *
     * This method reads all files in namespace FuelioImporter\Providers
     * classes implementing IConverter interface
     */
    public function initialize(): void
    {
        $di = new DirectoryIterator(__DIR__ . DIRECTORY_SEPARATOR . 'Providers');
        foreach ($di as $spl_file) {
            if ($spl_file->isFile() && !$spl_file->isDot() && $spl_file->isReadable() && strpos($spl_file->getBasename(), 'Provider.php') !== false) {

                /* We should have a new class, verify and check if it's implementing IConverter
                 * Autoloader here will do the job searching for valid file
                 * it should not misbehave as we are using FuelioImporter namespace
                 */

                $classname = 'FuelioImporter\\Providers\\' . ucfirst($spl_file->getBasename('.php'));

                if (!class_exists($classname, true)) {
                    continue;
                }

                if (is_subclass_of($classname, ProviderInterface::class, true)) {
                    $instance = new $classname();
                    if (isset($this->providers[$instance->getName()])) {
                        throw new ProviderExistsException();
                    }

                    $this->providers[$instance->getName()] = $instance;
                }
            }
        }
        $this->classes_loaded = true;
    }
}
