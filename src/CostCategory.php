<?php

declare(strict_types=1);

namespace FuelioImporter;

/**
 * Cost category model
 * @author Kamil Kamiński
 */
class CostCategory implements BackupEntryInterface
{
    /** @var integer Internal cost category id */
    private int $type_id;
    /** @var string Cost category name **/
    private string $name;
    /** @var integer Cost category priority **/
    private int $priority;
    /** @var string Category color, #rrggbb format **/
    private string $color;
    
    public function __construct(int $type_id, string $name, int $priority = 0, string $color = '')
    {
        $this->type_id = $type_id;
        $this->name = $name;
        $this->priority = $priority;
        $this->color = $color;
    }
    
    public function getTypeId(): int
    {
        return $this->type_id;
    }

    public function setTypeId(int $typeId): void
    {
        $this->type_id = $typeId;
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getColor(): string
    {
        return $this->color;
    }
    
    public function getData(): array
    {
        $vars = get_object_vars($this); 
        return array_values($vars);
    }
}