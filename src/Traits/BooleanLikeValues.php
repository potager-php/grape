<?php

namespace Potager\Grape\Traits;

/**
 * Trait BooleanLikeValues
 * 
 * Manages custom boolean-like values for validation.
 * 
 * This trait allows you to define which values should be considered "truthy" 
 * or "falsy" beyond PHP's default boolean evaluation. Useful for handling
 * form data, configuration values, and user input where various strings
 * and numbers should be interpreted as boolean values.
 */
trait BooleanLikeValues
{
    /**
     * Values that should be considered truthy in validation.
     */
    protected static array $truthy = [true, "true", "1", 1, "on", "yes", "y", "enable"];

    /**
     * Values that should be considered falsy in validation.
     */
    protected static array $falsy = [false, "false", "0", 0, "off", "no", "n", "disable"];

    /**
     * Add values that should be considered truthy.
     * 
     * Extends the list of values that will be interpreted as true.
     * Only accepts boolean, string, and numeric values.
     * 
     * @param array $values Array of values to add to the truthy list
     * 
     * @example Grape::addTruthy(['yes', 'oui', 'si', 'enabled'])
     * @example Grape::addTruthy([1, '1', 'active', 'confirmed'])
     */
    public static function addTruthy(array $values): void
    {
        $values = array_filter($values, function ($item): bool {
            return is_bool($item) || is_string($item) || is_numeric($item);
        });

        self::$truthy = array_merge(self::$truthy, $values);
    }

    /**
     * Add values that should be considered falsy.
     * 
     * Extends the list of values that will be interpreted as false.
     * Only accepts boolean, string, and numeric values.
     * 
     * @param array $values Array of values to add to the falsy list
     * 
     * @example Grape::addFalsy(['no', 'non', 'nein', 'disabled'])
     * @example Grape::addFalsy([0, '0', 'inactive', 'denied'])
     */
    public static function addFalsy(array $values): void
    {
        $values = array_filter($values, function ($item): bool {
            return is_bool($item) || is_string($item) || is_numeric($item);
        });

        self::$falsy = array_merge(self::$falsy, $values);
    }

    /**
     * Remove values from the truthy list.
     * 
     * @param array $values Array of values to remove from the truthy list
     * 
     * @example Grape::removeTruthy(['y']) // Remove 'y' from truthy values
     */
    public static function removeTruthy(array $values): void
    {
        self::$truthy = array_filter(self::$truthy, function ($item) use ($values) {
            return !in_array($item, $values);
        });
    }

    /**
     * Remove values from the falsy list.
     * 
     * @param array $values Array of values to remove from the falsy list
     * 
     * @example Grape::removeFalsy(['n']) // Remove 'n' from falsy values
     */
    public static function removeFalsy(array $values): void
    {
        self::$falsy = array_filter(self::$falsy, function ($item) use ($values) {
            return !in_array($item, $values);
        });
    }

    /**
     * Replace all truthy values with a new set.
     * 
     * Completely replaces the current truthy values list.
     * Only accepts boolean, string, and numeric values.
     * 
     * @param array $values Array of values that should be considered truthy
     * 
     * @example Grape::setTruthy(['confirmed', 'approved', 'accepted'])
     */
    public static function setTruthy(array $values): void
    {
        $values = array_filter($values, function ($item): bool {
            return is_bool($item) || is_string($item) || is_numeric($item);
        });

        self::$truthy = $values;
    }

    /**
     * Replace all falsy values with a new set.
     * 
     * Completely replaces the current falsy values list.
     * Only accepts boolean, string, and numeric values.
     * 
     * @param array $values Array of values that should be considered falsy
     * 
     * @example Grape::setFalsy(['denied', 'rejected', 'cancelled'])
     */
    public static function setFalsy(array $values): void
    {
        $values = array_filter($values, function ($item): bool {
            return is_bool($item) || is_string($item) || is_numeric($item);
        });

        self::$falsy = $values;
    }

    /**
     * Get all values currently considered truthy.
     * 
     * Returns the complete list of values that will be interpreted as true,
     * including the default 'true' boolean value.
     * 
     * @return array List of all truthy values
     */
    public static function getTruthy(): array
    {
        return self::uniqueStrict([true, ...self::$truthy]);
    }

    /**
     * Get all values currently considered falsy.
     * 
     * Returns the complete list of values that will be interpreted as false,
     * including the default 'false' boolean value.
     * 
     * @return array List of all falsy values
     */
    public static function getFalsy(): array
    {
        return self::uniqueStrict([false, ...self::$falsy]);
    }

    /**
     * Reset both truthy and falsy values to their defaults.
     * 
     * Restores the original default lists of truthy and falsy values,
     * removing any custom additions or modifications.
     * 
     * @example Grape::resetBooleanValues() // Resets to defaults
     */
    public static function resetBooleanValues(): void
    {
        self::$truthy = [true, "true", "1", 1, "on", "yes", "y", "enable"];
        self::$falsy = [false, "false", "0", 0, "off", "no", "n", "disable"];
    }

    /**
     * Remove duplicates while preserving strict comparison.
     * 
     * Internal utility method that removes duplicate values from an array
     * using strict comparison (===) to maintain type safety.
     * 
     * @param array $values Array to remove duplicates from
     * @return array Array with duplicates removed
     */
    protected static function uniqueStrict(array $values): array
    {
        $unique = [];
        foreach ($values as $v) {
            $already = false;
            foreach ($unique as $u) {
                if ($u === $v) {
                    $already = true;
                    break;
                }
            }
            if (!$already) {
                $unique[] = $v;
            }
        }
        return $unique;
    }
}
