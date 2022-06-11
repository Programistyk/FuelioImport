<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Fuel types database
 * @author Kamil Kamiński
 * @version 20180124
 * @psalm-type TypeEntry array{name: string, active: int, parent: null|array}
 */
class FuelTypes {
    public const FUEL_ROOT_GASOLINE = 100;
    public const FUEL_ROOT_DIESEL = 200;
    public const FUEL_ROOT_ETHANOL = 300;
    public const FUEL_ROOT_LPG = 400;
    public const FUEL_ROOT_CNG = 500;
    public const FUEL_ROOT_ELECTRICITY = 600;
    public const FUEL_ROOT_FLEX = 700;

    /** @var array<int, TypeEntry> */
    protected array $list;

    public function __construct()
    {
        $this->list = array();
    }

    public function addType(?int $root, int $id, string $name, bool $active): void
    {
        $element = ['name' => trim($name), 'active' => (int) $active, 'parent' => null];
        if ($root === null && !empty($name) && !empty($id) && ($root%100 === 0) ) {
            $this->list[$id] = $element;
            return;
        }

        if (!$this->validRootId($root)) {
            throw new \RuntimeException('Invalid root fuel type id');
        }

        /** @psalm-suppress PossiblyNullArrayOffset */
        $element['parent'] = &$this->list[$root];
    }

    public function findIdByName($sName): int
    {
        $name = trim($sName);
        foreach ($this->list as $id => $element) {
            if (strtolower($element['name']) === $name) {
                return $id;
            }
        }
        return -1;
    }

    public function findNameById($nId): ?string
    {
        if ($this->isValidId($nId)) {
            return $this->list[$nId]['name'];
        }
        return null;
    }

    public function isValidId($nId): bool
    {
        return isset($this->list[(int)$nId]);
    }

    public function validRootId($nId): bool
    {
        return $this->isValidId($nId) && ($nId%100 === 0);
    }

    public static function getTypes(): FuelTypes
    {
        $list = new FuelTypes();

        $fh = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'FuelType.csv', 'r');
        if (!$fh) {
            throw new \RuntimeException('Failed opening FuelTypes.csv');
        }
        $head = fgetcsv($fh);
        if ($head[0] !== 'Id') {
            throw new \RuntimeException('Invalid FuelTypes.csv format!');
        }

        while(!feof($fh)) {
            $line = fgetcsv($fh);
            $root = trim($line[1]);
            $list->addType($root === '<null>' ? null : (int) $root, (int) $line[0], $line[2], (bool) $line[3]);

        }

        fclose($fh);
        return $list;
    }
}