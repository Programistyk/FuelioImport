<?php

namespace FuelioImporter;

/**
 * Interface for generic Fuelio file entries
 * @author Kamil Kamiński
 */
interface IBackupEntry {
    /**
     * Returns array for fputcsv
     * @return array
     */
    public function getData();
}