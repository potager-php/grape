<?php

namespace Potager\Grape\Validators;

use Potager\Grape\DefaultMessages;
use Potager\Grape\FieldContext;

/**
 * Base class for number validation rules.
 * 
 * This abstract class provides a set of common validation rules for numeric values,
 * such as minimum, maximum, range, positivity, negativity, and more.
 * 
 * @package Potager\Grape\Validators
 */
abstract class NumberValidatorBase extends AbstractValidator
{
    /**
     * Applies the absolute value transformation to the field value.
     * 
     * @return static The current instance for method chaining.
     */
    public function abs(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            $value = abs($value);
            $ctx->mutate($value);
        };

        return $this;
    }

    /**
     * Validates that the field value is greater than or equal to the specified minimum.
     * 
     * @param int|float $min The minimum value.
     * @return static The current instance for method chaining.
     */
    public function min(int|float $min): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($min) {
            $value = $ctx->getValue();
            if ($value < $min)
                $ctx->report(DefaultMessages::$messages['min'], 'min', ['min' => $min]);
        };

        return $this;
    }

    /**
     * Validates that the field value is less than or equal to the specified maximum.
     * 
     * @param int|float $max The maximum value.
     * @return static The current instance for method chaining.
     */
    public function max(int|float $max): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($max) {
            $value = $ctx->getValue();
            if ($value > $max)
                $ctx->report(DefaultMessages::$messages['max'], 'max', ['max' => $max]);
        };

        return $this;
    }

    /**
     * Validates that the field value is within the specified range.
     * 
     * @param int|float $min The minimum value.
     * @param int|float $max The maximum value.
     * @return static The current instance for method chaining.
     */
    public function range(int|float $min, int|float $max): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($min, $max) {
            $value = $ctx->getValue();
            if ($value < $min || $value > $max)
                $ctx->report(DefaultMessages::$messages['range'], 'range', ['min' => $min, 'max' => $max]);
        };

        return $this;
    }

    /**
     * Validates that the field value is exactly zero.
     * 
     * @return static The current instance for method chaining.
     */
    public function zero(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value !== 0)
                $ctx->report(DefaultMessages::$messages['zero'], 'zero');
        };

        return $this;
    }

    /**
     * Validates that the field value is not zero.
     * 
     * @return static The current instance for method chaining.
     */
    public function notZero(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value === 0)
                $ctx->report(DefaultMessages::$messages['notZero'], 'notZero');
        };

        return $this;
    }

    /**
     * Validates that the field value is positive.
     * 
     * @return static The current instance for method chaining.
     */
    public function positive(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value < 0)
                $ctx->report(DefaultMessages::$messages['positive'], 'positive');
        };

        return $this;
    }

    /**
     * Validates that the field value is negative.
     * 
     * @return static The current instance for method chaining.
     */
    public function negative(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value >= 0)
                $ctx->report(DefaultMessages::$messages['negative'], 'negative');
        };

        return $this;
    }

    /**
     * Validates that the field value is odd.
     * 
     * @return static The current instance for method chaining.
     */
    public function odd(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value % 2 === 0)
                $ctx->report(DefaultMessages::$messages['odd'], 'odd');
        };

        return $this;
    }

    /**
     * Validates that the field value is even.
     * 
     * @return static The current instance for method chaining.
     */
    public function even(): static
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if ($value % 2 !== 0)
                $ctx->report(DefaultMessages::$messages['even'], 'even');
        };

        return $this;
    }

    /**
     * Validates that the field value is a multiple of the specified factor.
     * 
     * @param int|float $factor The factor to check against.
     * @return static The current instance for method chaining.
     */
    public function multipleOf(int|float $factor): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($factor) {
            $value = $ctx->getValue();
            if ($value % $factor !== 0)
                $ctx->report(DefaultMessages::$messages['multipleOf'], 'multipleOf', ['factor' => $factor]);
        };

        return $this;
    }
}