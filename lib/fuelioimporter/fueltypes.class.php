<?php

namespace FuelioImporter;

/**
 * Fuel types database
 * @author Kamil Kamiński
 * @version 20180124
 */
class FuelTypes {
    const FUEL_ROOT_GASOLINE = 100;
    const FUEL_ROOT_DIESEL = 200;
    const FUEL_ROOT_LPG = 300;
    const FUEL_ROOT_CNG = 400;
    const FUEL_ROOT_ETHANOL = 500;

    protected $list;

    public function __construct()
    {
        $this->list = array();
    }

    public function addType($root, $id, $name, $active)
    {
        $root = (int)$root;
        $element = array('name' => trim($name), 'active' => (int)(bool)$active, 'parent' => null);
        if ($root === null && !empty($name) && !empty($id) && ($root%100 === 0) ) {
            $this->list[$root] = $element;
            return;
        }

        if (!$this->validRootId($root)) {
            throw new \RuntimeException('Invalid root fuel type id');
        }

        $element['parent'] = &$this->list[$root];
    }

    public function findIdByName($sName)
    {
        $name = trim($sName);
        foreach ($this->list as $id => $element) {
            if (strtolower($element['name']) === $name) {
                return $id;
            }
        }
        return -1;
    }

    public function findNameById($nId)
    {
        if ($this->isValidId($nId)) {
            return $this->list[$nId]['name'];
        }
        return null;
    }

    public function isValidId($nId)
    {
        return isset($this->list[(int)$nId]);
    }

    public function validRootId($nId) {
        return $this->isValidId($nId) && ($nId%100 === 0);
    }

    public static function getTypes()
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
            $list->addType($root === '<null>' ? null : $root, $line[1], $line[2], $line[3]);

        }

        fclose($fh);
        return $list;
    }
}