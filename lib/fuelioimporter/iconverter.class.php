<?php

namespace FuelioImporter;
use SplFileObject;

interface IConverter {
    // Internal name of converter (as in hashtags)
    public function getName();
    // Full name of converter
    public function getTitle();
    // Array of errors during conversion
    public function getErrors();
    // Array of warnings
    public function getWarnings();
    // Method that processes given file returning SplTempFileObject
    public function processFile(SplFileObject $stream);
    // Method returns a CardInterface for visual representation
    public function getCard();
    // Optional stylesheet to include on page
    public function getStylesheetLocation();
    // Sets car name
    public function setCarName($name);
}