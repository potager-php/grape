<?php

namespace Potager\Grape\Validators;

use Potager\Grape\DefaultMessages;
use Potager\Grape\FieldContext;

/**
 * Validator for numeric values.
 * 
 * This class extends the NumberValidatorBase and provides additional functionality
 * for validating and transforming numeric values, including strict type checking
 * and clamping values within a range.
 * 
 * @package Potager\Grape\Validators
 */
class NumberValidator extends NumberValidatorBase
{
    /**
     * Create a new number validator instance.
     * 
     * @param bool $strict Whether to enforce strict number type checking.
     *                     In strict mode, only integer and float values are accepted.
     *                     In non-strict mode, numeric strings are cast to numbers.
     */
    public function __construct(bool $strict = false)
    {
        $this->rules[] = function (FieldContext $ctx) use ($strict): void {
            $value = $ctx->getValue();

            if ($strict) {
                if (!is_int($value) && !is_float($value))
                    $ctx->fatal(DefaultMessages::$messages['number'], 'number');
            } else {
                if (is_numeric($value)) {
                    $numericValue = is_float($value) || str_contains((string) $value, '.')
                        ? (float) $value
                        : (int) $value;
                    $ctx->mutate($numericValue);
                } else {
                    $ctx->fatal(DefaultMessages::$messages['number'], 'number');
                }
            }
        };
    }

    /**
     * Clamps the field value within the specified range.
     * 
     * The value is adjusted to be no less than the minimum and no greater than the maximum.
     * 
     * @param int|float $min The minimum allowable value.
     * @param int|float $max The maximum allowable value.
     * @return static The current instance for method chaining.
     */
    public function clamp(int|float $min, int|float $max): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($min, $max) {
            $value = $ctx->getValue();
            $clampedValue = max($min, min($value, $max));
            $ctx->mutate($clampedValue);
        };

        return $this;
    }
}