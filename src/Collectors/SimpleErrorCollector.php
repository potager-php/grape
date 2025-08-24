<?php

namespace Potager\Grape\Collectors;

use Potager\Grape\Contracts\ErrorCollectorContract;
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\FieldContext;

/**
 * Simple implementation of the ErrorCollectorContract.
 *
 * This class collects validation errors and provides methods to check for errors,
 * report new errors, and create a ValidationException containing all errors.
 */
class SimpleErrorCollector implements ErrorCollectorContract
{
    /**
     * @var ?string Optional global message for the validation errors.
     */
    protected ?string $message;

    /**
     * @var array An array to store validation error messages grouped by field paths.
     */
    protected array $messages = [];

    /**
     * Constructor to initialize the error collector.
     *
     * @param ?string $message Optional global message for the validation errors.
     */
    public function __construct(?string $message = null)
    {
        $this->message = $message;
    }

    /**
     * Check if there are any validation errors.
     *
     * @return bool True if there are validation errors, false otherwise.
     */
    public function hasErrors(): bool
    {
        return !empty($this->messages);
    }

    /**
     * Report a validation error for a specific field and rule.
     *
     * @param FieldContext $field The context of the field that failed validation.
     * @param string $rule The validation rule that was violated.
     * @param string $message The error message describing the validation failure.
     * @return void
     */
    public function report(FieldContext $field, string $rule, string $message): void
    {
        $path = $field->getQualifiedPath();
        $this->messages[$path] ??= [];
        $this->messages[$path][] = [
            'rule' => $rule,
            'message' => $message,
            'path' => $path
        ];
    }

    /**
     * Create a ValidationException containing all reported errors.
     *
     * @return ValidationException The exception representing the validation errors.
     */
    public function createError(): ValidationException
    {
        return new ValidationException($this->messages, $this->message);
    }
}