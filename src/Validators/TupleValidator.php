<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\Enums\UnknownPropertiesStrategy;
use Potager\Grape\FieldContext;

/**
 * Validator for validating tuples (fixed-size arrays with specific types for each index).
 *
 * This validator ensures that the value is an array and validates each element
 * against the corresponding validator in the provided list of item validators.
 * It supports strategies for handling unknown items.
 */
class TupleValidator extends AbstractValidator
{
    /**
     * Strategy for handling unknown items in the tuple.
     *
     * @var UnknownPropertiesStrategy
     */
    protected UnknownPropertiesStrategy $unknownItemStrategy = UnknownPropertiesStrategy::Discard;

    /**
     * List of validators for each tuple element.
     *
     * @var array<AbstractValidator>
     */
    private array $elementValidators;

    /**
     * Constructs a TupleValidator with a list of element validators.
     *
     * @param array<AbstractValidator> $elementValidators Validators for each tuple element.
     * @throws \InvalidArgumentException If any item in $elementValidators is not an AbstractValidator.
     */
    public function __construct(array $elementValidators)
    {
        $this->elementValidators = array_values($elementValidators);
        $this->validateElementValidators();

        $minLength = count($elementValidators);

        // Add rule to validate the tuple structure
        $this->rules[] = function (FieldContext $ctx) use ($minLength): void {
            $value = $ctx->getValue();

            // Ensure the value is an array and meets the minimum length requirement
            if (!is_array($value) || count($value) < $minLength) {
                $ctx->fatal(DefaultMessages::$messages['tuple'], 'tuple', ['length' => $minLength]);
            } else {
                $ctx->mutate(array_values($value));
            }
        };

        $this->registerElementValidators();
    }

    /**
     * Validates that all element validators are instances of AbstractValidator.
     *
     * @return void
     * @throws \InvalidArgumentException If any item in $elementValidators is invalid.
     */
    protected function validateElementValidators(): void
    {
        foreach ($this->elementValidators as $index => $validator) {
            if (!$validator instanceof AbstractValidator) {
                $type = is_object($validator) ? get_class($validator) : gettype($validator);
                throw new \InvalidArgumentException(
                    "Element validator at index $index must be an instance of AbstractValidator, got $type."
                );
            }
        }
    }

    /**
     * Registers validation rules for each element in the tuple.
     *
     * This method ensures that each tuple element is validated against its corresponding validator.
     *
     * @return void
     */
    protected function registerElementValidators(): void
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();

            foreach ($value as $index => $item) {
                $nestedCtx = $ctx->buildNestedContext($index);

                // Validate known elements
                if (isset($this->elementValidators[$index])) {
                    $this->elementValidators[$index]->applyRules($item, $nestedCtx);
                } else {
                    // Handle unknown elements based on the strategy
                    if ($this->unknownItemStrategy === UnknownPropertiesStrategy::Discard) {
                        $nestedCtx->discardField();
                    } elseif ($this->unknownItemStrategy === UnknownPropertiesStrategy::Reject) {
                        $ctx->report(DefaultMessages::$messages['unknownItem'], 'unknownItem', ['index' => $index]);
                    }
                }
            }
        };
    }

    /**
     * Sets the strategy to allow unknown items in the tuple.
     *
     * @return static Returns the validator instance for method chaining.
     */
    public function allowUnknownItems(): static
    {
        $this->unknownItemStrategy = UnknownPropertiesStrategy::Keep;
        return $this;
    }

    /**
     * Sets the strategy to reject unknown items in the tuple.
     *
     * @return static Returns the validator instance for method chaining.
     */
    public function rejectUnknownItems(): static
    {
        $this->unknownItemStrategy = UnknownPropertiesStrategy::Reject;
        return $this;
    }

    /**
     * Sets the strategy to discard unknown items in the tuple.
     *
     * @return static Returns the validator instance for method chaining.
     */
    public function discardUnknownItems(): static
    {
        $this->unknownItemStrategy = UnknownPropertiesStrategy::Discard;
        return $this;
    }

    /**
     * Sets the rule to ensure all elements in the tuple are unique.
     *
     * @return static Returns the validator instance for method chaining.
     */
    public function distinct(?callable $resolver = null): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($resolver): void {
            $value = $ctx->getValue();
            if ($resolver !== null) {
                $value = array_map($resolver, $value);
            }
            if (count($value) !== count(array_unique($value))) {
                $ctx->fatal(DefaultMessages::$messages['tupleDistinct'], 'tupleDistinct');
            }
        };
        return $this;
    }
}