<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Enums\UnknownPropertiesStrategy;
use Potager\Grape\Exceptions\InvalidSchemaException;
use Potager\Grape\FieldContext;

/**
 * Validator for complex data structures with multiple fields.
 * 
 * The SchemaValidator validates associative arrays against a schema definition
 * where each key represents a field name and each value is an AbstractValidator
 * instance that defines the validation rules for that field. * 
 */
class SchemaValidator extends AbstractValidator
{
    /**
     * Associative array mapping field names to their respective validators.
     * 
     * The array keys represent field names in the data structure being validated,
     * and the values must be instances of AbstractValidator that define the
     * validation rules for each field.
     * 
     * @var array<string, AbstractValidator>|null
     */
    private ?array $fieldDefinitions;

    /**
     * Strategy for handling unknown properties in the validated data.
     * 
     * Can be one of:
     * - 'discard': Remove unknown properties from the validated data (default)
     * - 'keep': Preserve unknown properties in the validated data
     * - 'reject': Report validation errors for unknown properties
     * 
     * @var UnknownPropertiesStrategy
     */
    private UnknownPropertiesStrategy $unknownPropertiesStrategy = UnknownPropertiesStrategy::Discard;

    /**
     * Creates a new schema validator with field definitions.
     * 
     * @param array<string, AbstractValidator>|null $fieldDefinitions Associative array mapping field names to validators
     */
    public function __construct(?array $fieldDefinitions = null)
    {
        $this->fieldDefinitions = $fieldDefinitions;

        // Register the base array validation rule
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!is_array($value)) {
                $ctx->fatal('{{ field }} must be an array', 'schema');
            } else {
                $ctx->mutate((array) $value);
            }
        };

        // Register field-specific validation rules
        $this->registerFieldValidators();
    }

    /**
     * Registers validation rules for each field defined in the schema.
     * 
     * This method validates that the input data contains all required fields
     * and applies the appropriate validator to each field's value.
     * 
     * @return void
     */
    protected function registerFieldValidators(): void
    {
        if (!$this->hasFieldDefinitions()) {
            return;
        }

        $this->assertValidDefinitionFormat();

        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();

            /** @var array<string, AbstractValidator> $definitions */
            $definitions = $this->fieldDefinitions ?? [];

            foreach ($definitions as $fieldName => $validator) {
                $nestedCtx = $ctx->buildNestedContext($fieldName);
                $isRequired = $validator->isRequired();

                if (!array_key_exists($fieldName, $value) && $isRequired) {
                    $nestedCtx->report("{{ field }} is required.", "required");
                } else if (array_key_exists($fieldName, $value)) {
                    $validator->applyRules($value[$fieldName], $nestedCtx);
                }
            }

            // If the schema allows unknown properties, we skip further checks (prevent unnecessary array processing)
            if ($this->unknownPropertiesStrategy === UnknownPropertiesStrategy::Keep) {
                return;
            }

            // Check for unknown fields that are not defined in the schema
            $unknownProperties = array_diff_key($value, $definitions);

            // If there are no unknown fields, we can skip further processing
            if (empty($unknownProperties)) {
                return;
            }

            // Handle unknown properties based on the configured strategy
            foreach ($unknownProperties as $fieldName => $fieldValue) {
                $nestedCtx = $ctx->buildNestedContext($fieldName);
                if ($this->unknownPropertiesStrategy === UnknownPropertiesStrategy::Reject) {
                    // Reject unknown properties with a validation error if the strategy is set to reject
                    $nestedCtx->report("{{ field }} is unknown.", 'unknown');
                } else {
                    // Discard unknown properties otherwise (remove them from the validated data)
                    $nestedCtx->discardField();
                }
            }
        };
    }

    /**
     * Checks if field definitions are available and not empty.
     * 
     * @return bool True if field definitions exist and are not empty, false otherwise
     */
    protected function hasFieldDefinitions(): bool
    {
        return $this->fieldDefinitions !== null && !empty($this->fieldDefinitions ?? []);
    }

    /**
     * Validates the format and content of field definitions.
     * 
     * Ensures that field definitions form an associative array where each value
     * is an instance of AbstractValidator. Throws an exception if the format
     * is invalid.
     * 
     * @return void
     * @throws InvalidSchemaException If the field definitions format is invalid
     */
    protected function assertValidDefinitionFormat(): void
    {
        if (array_is_list($this->fieldDefinitions)) {
            throw new InvalidSchemaException(
                "Schema definition must be an associative array with field names as keys. " .
                "Got a numeric/list array instead. Example: ['name' => \$validator, 'email' => \$validator]"
            );
        }

        foreach ($this->fieldDefinitions as $fieldName => $definition) {
            if (!$definition instanceof AbstractValidator) {
                $type = is_object($definition) ? get_class($definition) : gettype($definition);
                throw new InvalidSchemaException(
                    "Schema field '{$fieldName}' must be an instance of AbstractValidator, got {$type}. " .
                    "Expected validators like StringValidator, IntegerValidator, etc."
                );
            }
        }
    }

    /**
     * Configure whether to allow unknown properties in the validated data.
     *
     * When enabled, unknown fields that are not defined in the schema will be
     * ignored during validation instead of being discarded. This is useful when
     * you want to preserve additional data that might be added by external systems.
     *
     * @param bool $allow Whether to allow unknown properties (default: true)
     * @return static Returns the current validator instance for method chaining
     */
    public function allowUnknownProperties(bool $allow = true): static
    {
        $this->unknownPropertiesStrategy = $allow
            ? UnknownPropertiesStrategy::Keep
            : UnknownPropertiesStrategy::Discard;
        return $this;
    }

    /**
     * Configure whether to reject unknown properties in the validated data.
     *
     * When enabled, unknown fields that are not defined in the schema will cause
     * validation errors of type 'unknown' instead of being discarded or kept.
     * This is useful for strict validation where no extra fields should be present.
     *
     * @param bool $reject Whether to reject unknown properties (default: true)
     * @return static Returns the current validator instance for method chaining
     */
    public function rejectUnknownProperties(bool $reject = true): static
    {
        $this->unknownPropertiesStrategy = $reject
            ? UnknownPropertiesStrategy::Reject
            : UnknownPropertiesStrategy::Discard;
        return $this;
    }
}