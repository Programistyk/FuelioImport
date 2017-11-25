<?php

namespace FuelioImporter;
use \SplFileObject;

/**
 * Interface for converters
 * @author Kamil Kamiński
 */
interface IConverter {
    /**
     * Internal name of converter (as in hashtags)
     * @return string
     */
    public function getName();
    
    /**
     * Full name of converter
     * @return string
     */
    public function getTitle();

    /**
     * Filename part of output
     * @return string
     */
    public function getOutputFileName();
    
    /**
     * Array of errors during conversion
     * @return array
     */
    public function getErrors();
    
    /**
     * Array of warnings
     * @return array()
     */
    public function getWarnings();
    
    /**
     * Method that processes given file returning SplTempFileObject
     * @return FuelioBackupBuilder
     */
    public function processFile(SplFileObject $stream, $form_data);
    
    /**
     * Method returns a CardInterface for visual representation
     * @return ICard
     */
    public function getCard();
    
    /**
     * Optional stylesheet to include on page
     * @return string|null
     */
    public function getStylesheetLocation();
    
    /**
     * Sets car name
     * 
     * @param string $name Car name
     */
    public function setCarName($name);
}