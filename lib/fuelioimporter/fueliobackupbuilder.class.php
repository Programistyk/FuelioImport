<?php

namespace FuelioImporter;

/**
 * Fuelio Backup Builder is a stream implementation of backup csv generating.
 * 
 * For IProvider implementation, Fuelio requires complete Vehicle + Fuel Log blocks. Costs are optional.
 *
 * @brief Backup file generator
 * @author Kamil Kamiński
 * @version 20180124
 * 
 */
class FuelioBackupBuilder extends \SplTempFileObject {
    
    /** Date format used in file / static date conversion target */
    const DATE_FORMAT = 'd.m.Y';
    /** A 'safe' CostCategory id for importing categories */
    const SAFE_CATEGORY_ID = 50;

    /**
     * Writes Vehicle header part
     */
    public function writeVehicleHeader()
    {
        $this->fwrite("## Vehicle,,,,,,,,,,,,,,\n");
        $this->fputcsv(array('Name','Description','DistUnit','FuelUnit','ConsumptionUnit','ImportCSVDateFormat', 'VIN', 'Insurance', 'Plate', 'Make', 'Model', 'Year', 'TankCount', 'Tank1Type', 'Tank2Type', 'Active'));
    }
    
    /**
     * Writes Vehicle data
     * @param \FuelioImporter\Vehicle $vehicle
     */
    public function writeVehicle(Vehicle $vehicle)
    {
        $this->fputcsv($vehicle->getData());
    }
    
    /**
     * Writes fuel log header
     */
    public function writeFuelLogHeader()
    {
        $this->fwrite("## Log,,,,,,,,,,,\n");
        $this->fputcsv(array('Data','Odo(km)','Fuel(litres)','Full','Price(optional)','l/100km(optional)','latitude(optional)','longitude(optional)','City(optional)','Notes(optional)','Missed'));
    }
    
    /**
     * Writes fuel log entry
     * @param \FuelioImporter\FuelLogEntry $entry
     */
    public function writeFuelLog(FuelLogEntry $entry)
    {
        $this->fputcsv($entry->getData());
    }
    
    /**
     * Writes cost categories header starting optional costs backup
     */
    public function writeCostCategoriesHeader()
    {
        $this->fwrite("## CostCategories,,,,,,,,,,,\n");
        $this->fputcsv(array('CostTypeID', 'Name', 'priority'));
    }
    
    /**
     * Writes costs header into stream
     */
    public function writeCoststHeader()
    {
        $this->fwrite("## Costs,,,,,,,,,,,,,,,\n");
        $this->fputcsv(array('CostTitle', 'Date', 'Odo', 'CostTypeID', 'Notes', 'Cost', 'flag', 'idR', 'read', 'RemindOdo', 'RemindDate', 'isTemplate', 'RepeatOdo', 'RepeatMonths', 'isIncome', 'UniqueId'));
    }
    
    /**
     * Writes cost category into stream
     * @param \FuelioImporter\CostCategory $category
     */
    public function writeCostCategory(CostCategory $category)
    {
        $this->fputcsv($category->getData());
    }
    
    /**
     * Writes cost into stream
     * @param \FuelioImporter\Cost $cost
     */
    public function writeCost(Cost $cost)
    {
        $this->fputcsv($cost->getData());
    }
}
