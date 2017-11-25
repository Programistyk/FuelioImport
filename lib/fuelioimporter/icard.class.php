<?php

namespace FuelioImporter;
use FuelioImporter\Form\IForm;

/**
 * Converter card interface for plugin GUI part
 * @todo Decide if we use cards and if we want plugin-based GUI
 * @todo Add file input accept= argument support 
 * @author Kamil Kamiński
 */
interface ICard {
    /**
     * Returns card CSS class
     * @return string
     */
    public function getClass();
    
    /**
     * Returns card title
     * @return string
     */
    public function getTitle();
    
    /**
     * Returns card supporting text
     * @return string
     */
    public function getSupporting();
    
    /**
     * Returns action items
     * @return array Array of action menu entries
     */
    public function getActions();
    
    /**
     * Returns card menu items
     * @return array Array of card menu entries
     */
    public function getMenu();

    /**
     * Returns configuration form interface
     * @return IForm|null
     */
    public function getForm();
}
