<?php

declare(strict_types=1);

namespace FuelioImporter\Form;

/**
 * Interface for cards configuration
 * @package FuelioImporter\Form
 * @author Kamil Kamiński
 * @extends \ArrayAccess<string, FormFieldInterface>
 * @extends \IteratorAggregate<string, FormFieldInterface>
 */
interface FormInterface extends \ArrayAccess, \IteratorAggregate
{
    /**
     * Processes $_POST data for form
     * @param array<string,mixed> $post_data PHP's $_POST
     */
    public function process(array $post_data): void;

    /**
     * Returns form identification string for name=
     */
    public function getName(): string;

    /**
     * Checks processed data for validation errors
     */
    public function isValid(): bool;

    /**
     * Returns if processed data had form fields attached
     */
    public function isSubmitted(): bool;

    /**
     * Returns processed data
     * @return null|array<string,mixed>
     */
    public function getData(): ?iterable;

    /**
     * Returns collected validation errors
     * @return \Throwable[]
     */
    public function getErrors(): array;
}
