<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\FieldContext;
use Potager\Grape\Grape;

/**
 * Validator for boolean values.
 * 
 * This class ensures that the field value is a boolean. It supports strict type checking
 * and provides additional methods to validate specific boolean states (true or false).
 * 
 * @package Potager\Grape\Validators
 */
class BooleanValidator extends AbstractValidator
{
    /**
     * Create a new boolean validator instance.
     * 
     * This constructor adds a rule to ensure that the field value is a boolean.
     * In strict mode, only boolean values are accepted. In non-strict mode,
     * values that can be interpreted as true or false are also accepted.
     * 
     * @param bool $strict Whether to enforce strict boolean type checking.
     */
    public function __construct(bool $strict)
    {
        $this->rules[] = function (FieldContext $ctx) use ($strict) {
            $value = $ctx->getValue();
            if ($strict && !is_bool($value))
                $ctx->fatal(DefaultMessages::$messages['boolean'], 'boolean');
            else if (!$strict && !Grape::helpers()->isTrue($value) && !Grape::helpers()->isFalse($value))
                $ctx->fatal(DefaultMessages::$messages['boolean'], 'boolean');
            else
                $ctx->mutate((bool) Grape::helpers()->isTrue($value));
        };
    }

    /**
     * Validates that the field value is true.
     * 
     * @return static The current instance for method chaining.
     */
    public function true(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value === false)
                $ctx->report(DefaultMessages::$messages['true'], 'true');
        };

        return $this;
    }

    /**
     * Validates that the field value is false.
     * 
     * @return static The current instance for method chaining.
     */
    public function false(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value === true)
                $ctx->report(DefaultMessages::$messages['false'], 'false');
        };

        return $this;
    }
}