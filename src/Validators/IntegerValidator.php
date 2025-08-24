<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\FieldContext;
use Potager\Grape\Traits\CanBeUnique;

/**
 * Validator for integer values.
 * 
 * This class extends the NumberValidatorBase and provides additional functionality
 * for validating and transforming integer values, including strict type checking
 * and clamping values within a range. It also supports uniqueness checks through
 * the CanBeUnique trait.
 * 
 * @package Potager\Grape\Validators
 */
class IntegerValidator extends NumberValidatorBase
{
    use CanBeUnique;

    /**
     * Create a new integer validator instance.
     * 
     * This constructor adds a rule to ensure that the field value is an integer.
     * In strict mode, only integer values are accepted. In non-strict mode,
     * numeric strings that represent integers are also accepted.
     * 
     * @param bool $strict Whether to enforce strict integer type checking.
     */
    public function __construct(bool $strict)
    {
        $this->rules[] = function (FieldContext $ctx) use ($strict): void {
            $value = $ctx->getValue();
            if ($strict && !is_int($value))
                $ctx->fatal(DefaultMessages::$messages['integer'], 'integer');
            else if (!$strict && (!is_numeric($value) || intval($value) != $value))
                $ctx->fatal(DefaultMessages::$messages['integer'], 'integer');
            else
                $ctx->mutate((int) intval($value));
        };
    }

    /**
     * Clamps the field value within the specified range.
     * 
     * The value is adjusted to be no less than the minimum and no greater than the maximum.
     * 
     * @param int $min The minimum allowable value.
     * @param int $max The maximum allowable value.
     * @return static The current instance for method chaining.
     */
    public function clamp(int $min, int $max): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($min, $max): void {
            $value = $ctx->getValue();
            $clampedValue = max($min, min($value, $max));
            $ctx->mutate($clampedValue);
        };

        return $this;
    }
}