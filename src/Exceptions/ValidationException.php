<?php

namespace Potager\Grape\Exceptions;

use Exception;

/**
 * Exception thrown when validation fails.
 * 
 * This exception provides detailed information about validation errors,
 * including the specific messages and the raw value that caused the failure.
 */
class ValidationException extends Exception
{
    /**
     * Validation error messages.
     * 
     * @var array An array of validation error messages (format is defined by the used collector).
     */
    protected array $messages;

    /**
     * The raw value that caused the validation failure.
     * 
     * @var mixed|null The raw value, or null if not set.
     */
    protected mixed $rawValue = null;

    /**
     * Constructs a new ValidationException.
     * 
     * @param array $messages Validation error messages.
     * @param string|null $message Optional exception message. Defaults to 'Validation failed'.
     */
    public function __construct(array $messages = [], ?string $message = null)
    {
        $this->messages = $messages;
        parent::__construct($message ?? 'Validation failed');
    }

    /**
     * Retrieves the validation error messages.
     * 
     * @return array An array of validation error messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Attaches the raw value that caused the validation failure.
     * 
     * @param mixed $rawValue The raw value to attach.
     * @return static The current instance for method chaining.
     */
    public function attachRaw(mixed $rawValue): static
    {
        $this->rawValue = $rawValue;
        return $this;
    }

    /**
     * Retrieves the raw value that caused the validation failure.
     * 
     * @return mixed|null The raw value, or null if not set.
     */
    public function getRawValue(): mixed
    {
        return $this->rawValue;
    }
}