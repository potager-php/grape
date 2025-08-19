<?php

namespace Potager\Grape\Contracts;

use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\FieldContext;

/**
 * Interface for collecting and managing validation errors.
 *
 * This contract defines the methods required for tracking validation errors,
 * reporting them, and creating exceptions when necessary.
 */
interface ErrorCollectorContract
{
    /**
     * Check if there are any validation errors.
     *
     * This flag is used by Grape to determine if the request contains one or more validation errors.
     *
     * @return bool True if there are validation errors, false otherwise.
     */
    public function hasErrors(): bool;

    /**
     * Report a validation error.
     *
     * This method allows adding a validation error for a specific field and rule.
     *
     * @param FieldContext $field 
     * @param string $rule The validation rule that was violated.
     * @param string $message The error message describing the validation failure.
     * @return void
     */
    public function report(FieldContext $field, string $rule, string $message): void;

    /**
     * Create a validation exception.
     *
     * This method generates a `ValidationException` containing all reported errors.
     *
     * @return ValidationException The exception representing the validation errors.
     */
    public function createError(): ValidationException;
}