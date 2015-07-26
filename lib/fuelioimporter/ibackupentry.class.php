<?php

namespace FuelioImporter;

interface IBackupEntry {
    // Returns fputcsv array
    public function getData();
}