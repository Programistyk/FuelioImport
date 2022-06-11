<?php

declare(strict_types=1);

namespace FuelioImporter;

use SplFileObject;

/**
 * Interface for converters
 * @author Kamil Kamiński
 */
interface ProviderInterface {
    /**
     * Internal name of converter (as in hashtags)
     */
    public function getName(): string;
    
    /**
     * Full name of converter
     */
    public function getTitle(): string;

    /**
     * Filename part of output
     */
    public function getOutputFileName(): string;
    
    /**
     * Array of errors during conversion
     * @return array<array-key,string>
     */
    public function getErrors(): array;
    
    /**
     * Array of warnings
     * @return array<array-key,string>
     */
    public function getWarnings(): array;
    
    /**
     * Method that processes given file returning SplTempFileObject
     * @todo Setup form_data type
     */
    public function processFile(SplFileObject $in, $form_data): FuelioBackupBuilder;
    
    /**
     * Method returns a CardInterface for visual representation
     */
    public function getCard(): CardInterface;
    
    /**
     * Optional stylesheet to include on page
     */
    public function getStylesheetLocation(): ?string;
    
    /**
     * Sets car name
     * 
     * @param string $name Car name
     */
    public function setCarName(string $name): void;
}
