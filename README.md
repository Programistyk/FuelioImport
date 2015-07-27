# FuelioImport
Data converter for importingo into great Fuelio app. Currently very limited (but working) GUI is available for PHP 5 built-int webserver.

FuelioImport is developed from the need to move personal fillups history from Motostat and aCar, but it is designed to offer ease of extension for new formats.
We are working to run this tool online, targeting the first release.

## Supported formats
Supported formats are:

 * Motostat (.csv)
 * aCar full backup (.abp) (with geolocation!)

## Limitations
Current versions of Fuelio app keep cost categories car-independent. If you have entered non-standard categories, Fuelio will assign imported costs to them,
leaving cost categories defined in our export. Because of this, there is no guarantee that your categories will be kept and we suggest to convert only data
for completely new installation of the app.

Fuelio's file format supports only one car definition in file, so if you convert backup containing more cars (like aCar's), only first car is going to be imported.

aCar format support only Litres as Fuel Unit and l/100km as Consumption Unit.

## Requirements
FuelioImport converter is built for current PHP version, but anything 5.1+ should do the job.

 * For aCar backup we need SimpleXMLElement and Zip support