<?php

namespace Potager\Grape\Traits;

/**
 * Trait BehaviorConfiguration
 * 
 * Provides global configuration options for controlling validation behavior
 * across all validators in the Grape validation library.
 * 
 * This trait allows you to set default behaviors that will be applied
 * to all validators unless explicitly overridden on individual validators.
 */
trait BehaviorConfiguration
{
    /**
     * Default strict mode for all validators.
     * When true, validators will enforce strict type checking by default.
     */
    public static bool $defaultStrict = false;

    /**
     * Default required behavior for all validators.
     * When true, all fields will be required by default unless explicitly made optional.
     */
    public static bool $defaultRequired = false;

    /**
     * Default nullable behavior for all validators.
     * When true, all fields will accept null values by default.
     */
    public static bool $defaultNullable = false;

    /**
     * Automatically convert empty strings to null values during validation.
     * Useful for handling form data where empty inputs are submitted as empty strings.
     */
    public static bool $convertEmptyStringsToNull = false;
}
