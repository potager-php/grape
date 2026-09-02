<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\FieldContext;

/**
 * Validator for float values.
 * 
 * This class extends the NumberValidatorBase and provides additional functionality
 * for validating and transforming float values, including strict type checking,
 * rounding, flooring, and clamping values within a range.
 * 
 * @package Potager\Grape\Validators
 */
class FloatValidator extends NumberValidatorBase
{
    /**
     * Create a new float validator instance.
     * 
     * This constructor adds a rule to ensure that the field value is a float.
     * In strict mode, only float values are accepted. In non-strict mode,
     * numeric strings that represent floats are also accepted.
     * 
     * @param bool $strict Whether to enforce strict float type checking.
     */
    public function __construct(bool $strict)
    {
        $this->rules[] = function (FieldContext $ctx) use ($strict): void {
            $value = $ctx->getValue();
            if ($strict && !is_float($value))
                $ctx->fatal(DefaultMessages::$messages['float'], 'float');
            else if (!$strict && !is_numeric($value))
                $ctx->fatal(DefaultMessages::$messages['float'], 'float');
            else
                $ctx->mutate((float) floatval($value));
        };
    }

    /**
     * Rounds the field value to the specified precision.
     * 
     * @param int $precision The number of decimal digits to round to. Defaults to 0.
     * @param 1|2|3|4 $mode The rounding mode. Defaults to PHP_ROUND_HALF_UP.
     * @return static The current instance for method chaining.
     */
    public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($precision, $mode): void {
            $value = $ctx->getValue();
            $value = round($value, $precision, $mode);
            $ctx->mutate($value);
        };

        return $this;
    }

    /**
     * Floors the field value to the nearest integer less than or equal to the value.
     * 
     * @return static The current instance for method chaining.
     */
    public function floor(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            $value = floor($value);
            $ctx->mutate($value);
        };

        return $this;
    }

    /**
     * Clamps the field value within the specified range.
     * 
     * The value is adjusted to be no less than the minimum and no greater than the maximum.
     * 
     * @param float $min The minimum allowable value.
     * @param float $max The maximum allowable value.
     * @return static The current instance for method chaining.
     */
    public function clamp(float $min, float $max): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($min, $max): void {
            $value = $ctx->getValue();
            $clampedValue = max($min, min($value, $max));
            $ctx->mutate($clampedValue);
        };

        return $this;
    }

    /**
     * Validates that the field value is NaN (Not a Number).
     * 
     * @return static The current instance for method chaining.
     */
    public function NaN(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!is_nan($value))
                $ctx->report(DefaultMessages::$messages['NaN'], 'NaN');
        };

        return $this;
    }

    /**
     * Validates that the field value is not NaN (Not a Number).
     * 
     * @return static The current instance for method chaining.
     */
    public function notNaN(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (is_nan($value))
                $ctx->report(DefaultMessages::$messages['notNaN'], 'notNaN');
        };

        return $this;
    }

    /**
     * Validates that the field value does not have a decimal part.
     * 
     * @return static The current instance for method chaining.
     */
    public function withoutDecimal(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if ($value != floor($value))
                $ctx->report(DefaultMessages::$messages['withoutDecimal'], 'withoutDecimal');
        };

        return $this;
    }
}