<?php

require_once 'bootstrap.php';

try {
    $provider = new FuelioImporter\ConverterProvider();

    $converter = $provider->get(@$_POST['c']);

// Check if we've got any file
    if (!isset($_FILES['f']))
        throw new FuelioImporter\NoFileUploadedException();

    $file = &$_FILES['f'];

    if ($file['error'])
        throw new FuelioImporter\UploadError($file);

    $converter->setCarName($_POST['n']);
    $fname = 'FuelioBackup-' . ucfirst(preg_replace('/\s+/', '-', $converter->getTitle())) . '.csv';

    if (defined('DEBUG'))
        header('Content-Type: text/plain, charset=UTF-8');
    else {
        header('Content-Type: text/csv, charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fname . '"');
    }
    $outfile = $converter->processFile(new SplFileObject($file['tmp_name']));
    $outfile->rewind();
    $outfile->fpassthru();
} catch (\Exception $ex) {
    if (defined('DEBUG')) {
        @header('Content-Type: text/html; charset=UTF-8'); // lets make reading xdebug output easier ;)
        throw $ex;
    }
    header('Content-Type: text/plain, charset=UTF-8', true, 500);
    die('An unknown error occured.');
}