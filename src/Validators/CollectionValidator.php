<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\FieldContext;

/**
 * Validator for array values with support for item validation and various constraints.
 * 
 * This validator can validate array structures, enforce size constraints, check for
 * uniqueness, and optionally validate each item in the array using a nested validator.
 */
class CollectionValidator extends AbstractValidator
{
    /**
     * Flag to determine whether invalid items should be skipped from the array
     * instead of causing validation failure.
     */
    protected $skipInvalids = false;

    /**
     * Create a new ArrayValidator instance.
     * 
     * @param AbstractValidator|null $itemValidator Optional validator to apply to each array item
     */
    public function __construct(
        private ?AbstractValidator $itemValidator = null
    ) {
        // Core validation rule: ensure the value is an array
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!is_array($value)) {
                $ctx->fatal('{{ field }} must be an array', 'collection');
            } else {
                $ctx->mutate((array) $value);
            }
        };

        // Register item validator if provided
        $this->registerItemValidator();
    }


    /**
     * Registers a validation rule for array items when an item validator is configured.
     * 
     * This method adds a validation rule that iterates through each item in an array value,
     * applying the configured item validator to each element. It creates nested contexts
     * for proper error handling and supports skipping invalid items when configured to do so.
     * 
     * The validation process:
     * - Casts the field value to an array
     * - Iterates through each array item with its index
     * - Creates a nested validation context for each item with staging enabled
     * - Applies the item validator rules to each item
     * - Either skips invalid items (if skipInvalids is true) or commits their errors
     * 
     * @return void
     */
    protected function registerItemValidator(): void
    {
        if (!$this->hasItemValidator()) {
            return;
        }

        $this->rules[] = function (FieldContext $ctx): void {
            // Retrieve the array value from the context
            $value = (array) $ctx->getValue();

            // Iterate through each item in the array
            foreach ($value as $index => $item) {
                // Create a nested context for this specific array item
                $nestedCtx = $ctx->buildNestedContext($index)->enableStaging();

                // Validate the item using the provided validator
                $this->itemValidator->applyRules($item, $nestedCtx);

                // If the item has errors and we're skipping invalids, skip it
                if ($nestedCtx->hasErrors() && $this->skipInvalids) {
                    $nestedCtx->clearStagedErrors()->discardField();
                    continue;
                }

                // Commit any staged errors for this item
                $nestedCtx->commitStagedErrors();
            }
        };
    }

    /**
     * Check if this validator has an item validator configured.
     * 
     * @return bool True if an item validator is set, false otherwise
     */
    protected function hasItemValidator(): bool
    {
        return $this->itemValidator !== null;
    }

    /**
     * Add a minimum length constraint to the array.
     * 
     * @param int $min Minimum number of items required
     * @return static Returns the validator instance for method chaining
     */
    public function minLength(int $min): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($min): void {
            $value = (array) $ctx->getValue();
            // Check if array has fewer items than the minimum required
            if (count($value) < $min)
                $ctx->report(DefaultMessages::$messages['collectionMinLength'], 'collectionMinLength', ['length' => $min]);
        };

        return $this;
    }

    /**
     * Add a maximum length constraint to the array.
     * 
     * @param int $max Maximum number of items allowed
     * @return static Returns the validator instance for method chaining
     */
    public function maxLength(int $max): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($max): void {
            $value = (array) $ctx->getValue();
            // Check if array has more items than the maximum allowed
            if (count($value) > $max)
                $ctx->report(DefaultMessages::$messages['collectionMaxLength'], 'collectionMaxLength', ['length' => $max]);
        };

        return $this;
    }

    public function fixedLength(int $length): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($length): void {
            $value = (array) $ctx->getValue();
            // Check if array has a different number of items than the specified length
            if (count($value) !== $length)
                $ctx->report(DefaultMessages::$messages['collectionFixedLength'], 'collectionFixedLength', ['length' => $length]);
        };

        return $this;
    }

    /**
     * Require the array to be empty.
     * 
     * @return static Returns the validator instance for method chaining
     */
    public function empty(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = (array) $ctx->getValue();
            // Fail validation if the array contains any items
            if (!empty($value))
                $ctx->report(DefaultMessages::$messages['collectionEmpty'], 'collectionEmpty');
        };

        return $this;
    }

    /**
     * Require the array to contain at least one item.
     * 
     * @return static Returns the validator instance for method chaining
     */
    public function notEmpty(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = (array) $ctx->getValue();
            // Fail validation if the array is empty
            if (empty($value))
                $ctx->report(DefaultMessages::$messages['collectionNotEmpty'], 'collectionNotEmpty');
        };

        return $this;
    }

    /**
     * Require all array items to be distinct (unique).
     * 
     * @param callable|null $resolver Optional function to resolve the comparison key for each item.
     *                               Receives ($item, $index) and should return the value to compare.
     *                               If null, items are compared directly.
     * @return static Returns the validator instance for method chaining
     */
    public function distinct(callable $resolver = null): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($resolver): void {
            $value = (array) $ctx->getValue();
            $seen = []; // Track values we've already encountered

            foreach ($value as $index => $item) {
                // Determine what value to use for uniqueness comparison
                $keyToCheck = $resolver ? $resolver($item, $index) : $item;

                // Check if we've seen this value before
                if (isset($seen[$keyToCheck])) {
                    $ctx->report(DefaultMessages::$messages['distinct'], 'distinct');
                    return; // Exit early on first duplicate found
                }

                // Remember this value for future comparisons
                $seen[$keyToCheck] = true;
            }
        };

        return $this;
    }

    /**
     * Normalize the array with sequential numeric keys starting from 0.
     * 
     * This mutation rule will transform associative arrays or arrays with non-sequential
     * keys into a numerically indexed array with consecutive keys.
     * 
     * @return static Returns the validator instance for method chaining
     */
    public function normalize(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = (array) $ctx->getValue();
            // Use array_values to normalize the array with sequential numeric keys
            $ctx->mutate(array_values($value));
        };

        return $this;
    }

    /**
     * Remove all empty string and null values from the array.
     * 
     * This mutation rule will remove any items that are either null or empty strings,
     * while preserving other "falsy" values like 0, false, or empty arrays.
     * 
     * @return static Returns the validator instance for method chaining
     */
    public function compact(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = (array) $ctx->getValue();
            $filtered = [];

            // Filter out null values and empty strings, but keep other falsy values
            foreach ($value as $index => $item) {
                // Keep the item if it's not null and not an empty string
                if ($item !== null && $item !== '') {
                    $filtered[$index] = $item;
                }
            }

            $ctx->mutate($filtered);
        };

        return $this;
    }

    /**
     * Configure whether invalid items should be skipped from the array instead of causing validation failure.
     * 
     * When enabled, items that fail validation will be silently removed from the array.
     * When disabled (default), any invalid item will cause the entire validation to fail.
     * 
     * @param bool $skip Whether to skip invalid items (default: true)
     * @return static Returns the validator instance for method chaining
     */
    public function skipInvalids(bool $skip = true): static
    {
        $this->skipInvalids = $skip;
        return $this;
    }

    public function validateKeys(callable $resolver): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($resolver): void {
            $value = (array) $ctx->getValue();
            foreach ($value as $key => $item) {
                $nestedCtx = $ctx->buildNestedContext($key);
                $resolver($key, $nestedCtx);
            }
        };
        return $this;
    }

    /**
     * Apply a mutation function to transform the keys of the array.
     * 
     * The callable receives the current key and should return the new key.
     * This allows for key transformations like normalization, case conversion, etc.
     * 
     * @param callable $resolver Function that receives ($key) and returns the new key
     * @return static Returns the validator instance for method chaining
     */
    public function mutateKeys(callable $resolver): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($resolver): void {
            $value = (array) $ctx->getValue();
            $mutated = [];

            foreach ($value as $key => $item) {
                $newKey = $resolver($key);
                $mutated[$newKey] = $item;
            }

            $ctx->mutate($mutated);
        };
        return $this;
    }


}