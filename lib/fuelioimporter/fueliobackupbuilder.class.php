<?php

namespace FuelioImporter;

class FuelioBackupBuilder extends \SplTempFileObject {
    
    const DATE_FORMAT = 'd.m.Y';

    public function writeVehicleHeader()
    {
        $this->fwrite("## Vehicle,,,,,,,,,,,\n");
        $this->fputcsv(array('Name','Description','DistUnit','FuelUnit','ConsumptionUnit','ImportCSVDateFormat', 'VIN', 'Insurance', 'Plate', 'Make', 'Model', 'Year'));
    }
    
    public function writeVehicle(Vehicle $vehicle)
    {
        $this->fputcsv($vehicle->getData());
    }
    
    public function writeFuelLogHeader()
    {
        $this->fwrite("## Log,,,,,,,,,,,\n");
        $this->fputcsv(array('Data','Odo(km)','Fuel(litres)','Full','Price(optional)','l/100km(optional)','latitude(optional)','longitude(optional)','City(optional)','Notes(optional)','Missed'));
    }
    
    public function writeFuelLog(FuelLogEntry $entry)
    {
        $this->fputcsv($entry->getData());
    }
    
    public function writeCostCategoriesHeader()
    {
        $this->fwrite("## CostCategories,,,,,,,,,,,\n");
        $this->fputcsv(array('CostTypeID', 'Name', 'priority'));
    }
    
    public function writeCoststHeader()
    {
        $this->fwrite("## Costs,,,,,,,,,,,\n");
        $this->fputcsv(array('CostTitle', 'Date', 'Odo', 'CostTypeID', 'Notes', 'Cost', 'flag', 'idR', 'read', 'RemindOdo', 'RemindDate'));
    }
    
    public function writeCostCategory(CostCategory $category)
    {
        $this->fputcsv($category->getData());
    }
    
    public function writeCost(Cost $cost)
    {
        $this->fputcsv($cost->getData());
    }
}
