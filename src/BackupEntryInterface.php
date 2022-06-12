<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Interface for generic Fuelio file entries
 * @author Kamil Kamiński
 */
interface BackupEntryInterface {
    /**
     * Returns array for fputcsv
     * @return array<null|int|string|float>
     */
    public function getData(): array;
}
