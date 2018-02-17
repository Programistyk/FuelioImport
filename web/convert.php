<?php

require_once 'bootstrap.php';

try {
    $provider = new FuelioImporter\ConverterProvider();

    $converter = $provider->get(@$_POST['c']);

// Check if we've got any file
    if (!isset($_FILES['f']) && (!isset($_POST['datastream']) || empty($_POST['datastream'])))
        throw new FuelioImporter\NoFileUploadedException();

    if (isset($_FILES['f']) && !empty($_FILES['f']['tmp_name'])) {
        $file = &$_FILES['f'];

        if ($file['error'])
            throw new FuelioImporter\UploadError($file);
        $infile = new SplFileObject($file['tmp_name']);
    }
    else {
        // Rely on datastream
        $infile = new SplFileObject('data://' . substr($_POST['datastream'],5)); // Skip data: part
    }

    $converter->setCarName($_POST['n']);

    // Validate optional form
    $form = $converter->getCard()->getForm();
    if ($form) {
        $form->process($_POST);
        if (!$form->isValid()) {
            $errors = $form->getErrors();
            throw array_pop($errors);
        }
    }

    $outfile = $converter->processFile($infile, $form ? $form->getData() : null);
    $fname = 'FuelioBackup-' . ucfirst(preg_replace('/\s+/', '-', $converter->getOutputFileName())) . '.csv';
    /*if (defined('DEBUG'))
        header('Content-Type: text/plain, charset=UTF-8');
    else {*/
    header('Content-Type: text/csv, charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . filter_var($fname, FILTER_SANITIZE_URL). '"');
    //}
    $outfile->rewind();
    $outfile->fpassthru();
} catch (Exception $ex) {
    header('Content-Disposition:', true);
    header('Content-Type: text/html, charset=UTF-8', true, 500);
    include '../view/error_template.php';
}