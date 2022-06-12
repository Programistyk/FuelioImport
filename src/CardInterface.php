<?php

declare(strict_types=1);

namespace FuelioImporter;

use FuelioImporter\Form\FormInterface;

/**
 * Converter card interface for plugin GUI part
 * @todo Decide if we use cards and if we want plugin-based GUI
 * @todo Add file input accept= argument support
 * @author Kamil Kamiński
 */
interface CardInterface
{
    /**
     * Returns card CSS class
     */
    public function getClass(): string;

    /**
     * Returns card title
     */
    public function getTitle(): string;

    /**
     * Returns card supporting text
     */
    public function getSupporting(): string;

    /**
     * Returns action items
     * @return list<array<string>> Array of action menu entries
     */
    public function getActions(): array;

    /**
     * Returns card menu items
     * @return list<array<string>> Array of card menu entries
     */
    public function getMenu(): array;

    /**
     * Returns configuration form interface
     */
    public function getForm(): ?FormInterface;
}
