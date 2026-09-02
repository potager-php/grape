<?php

namespace Potager\Grape;

use Potager\Grape\Contracts\ErrorCollectorContract;
use Potager\Grape\Contracts\MessageProviderContract;

/**
 * Class FieldContext
 *
 * Represents the validation context of a specific field during the validation process.
 * 
 * This class encapsulates all the information needed to validate a single field within
 * a potentially nested data structure. It maintains the current value being validated,
 * tracks the hierarchical path to the field, manages validation state, and provides
 * error reporting capabilities.
 * 
 * Key features:
 * - Hierarchical validation: Supports nested contexts for complex data structures
 * - Error staging: Allows optional conditional error reporting with staged/deferred execution
 * - Method chaining: Most mutating operations return $this for fluent interfaces
 * - Path tracking: Maintains dot-notation paths for precise error reporting
 * - Value mutation: Supports data transformation during validation
 * - Fatal error handling: Distinguishes between regular and fatal validation errors
 * 
 * By default, validation errors are reported immediately to the error collector.
 * Error staging can be enabled per context to defer error reporting until explicitly
 * committed, which is useful for conditional validation scenarios.
 * 
 * The context can represent either a root-level validation (no parent) or a nested
 * field validation (with parent context). Nested contexts inherit the error collector
 * and build upon the parent's path to create fully qualified field paths.
 *
 */
class FieldContext
{
    /**
     * The current value of the field under validation.
     * This is stored as a reference to allow direct mutation of the original data.
     *
     * @var mixed
     */
    private mixed $value;

    /**
     * The name or index of the field under validation.
     * Null indicates this is the root context without a specific field name.
     *
     * @var string|int|null
     */
    private string|int|null $name;

    /**
     * Indicates whether validation for this field has passed.
     * Set to false when any validation error is reported.
     *
     * @var bool
     */
    private bool $valid = true;

    /**
     * Indicates whether this field has encountered a fatal error.
     * Fatal errors typically prevent further validation processing on this field.
     *
     * @var bool
     */
    private bool $hasFatalError = false;

    /**
     * The parent context if this field is nested; null if it's root.
     * Used to traverse the validation hierarchy and access parent data.
     *
     * @var FieldContext|null
     */
    private ?FieldContext $parent;

    /**
     * The hierarchical path to the current field, used for error reporting and sanitization.
     * Each element represents a key or index in the nested data structure.
     *
     * @var array<int, string|int>
     */
    private array $path;

    /**
     * Collection of error reporting functions that are staged for later execution.
     * Allows validation to defer error reporting until explicitly committed.
     *
     * @var array<int, callable(): void>
     */
    private array $stagedErrors = [];

    /**
     * Indicates whether error staging is enabled for this context.
     * When false (default), errors are reported immediately to the collector.
     * When true, errors are staged and must be committed later.
     *
     * @var bool
     */
    private bool $stagingEnabled = false;

    /**
     * The error collector used to report validation messages.
     * Shared across all contexts in the same validation tree to centralize error collection.
     *
     * @var ErrorCollectorContract
     */
    private ErrorCollectorContract $collector;

    /**
     * The message provider used to retrieve custom validation messages.
     * Allows for dynamic message resolution based on field paths and rules.
     *
     * @var MessageProviderContract
     */
    private MessageProviderContract $messageProvider;

    /**
     * FieldContext constructor.
     *
     * Initializes a validation context for a specific field, linking to parent context if nested.
     * When a parent is provided, the context inherits the error collector and builds upon the path.
     * For root contexts, a new error collector is created and the path starts empty.
     *
     * @param mixed $value Reference to the current field's value, allowing direct mutation.
     * @param string|int|null $name Name or index of the current field. Null for root context.
     * @param FieldContext|null $parent Optional parent context for nested validation. Null for root.
     */
    public function __construct(
        mixed &$value,
        string|int|null $name,
        MessageProviderContract $messageProvider,
        ErrorCollectorContract $errorCollector,
        ?FieldContext $parent = null,
    ) {
        // Store a reference to the value being validated (allows mutation)
        $this->value = &$value;

        // Set the field name or index (null for root context)
        $this->name = $name;

        // Link to parent context if this is a nested field (null for root)
        $this->parent = $parent;

        // Link both the message provider and the error collector
        $this->messageProvider = $messageProvider;
        $this->collector = $errorCollector;

        // Determine context initialization based on whether this is root or nested
        if ($parent) {
            // For nested fields, build the path by appending this field's name to the parent's path
            $this->path = [...$parent->path, $name];
        } else {
            // For root context, start with an empty path (no parent)
            $this->path = [];

        }
    }

    /**
     * Retrieves the current value under validation.
     *
     * Returns the raw value that is being validated. This could be of any type
     * (string, array, object, etc.) depending on the data structure being validated.
     *
     * @return mixed The current value (could be any type).
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the validation error collector.
     *
     * Returns the shared error collector instance that accumulates validation errors
     * across the entire validation tree. This is the same instance used by all
     * contexts in the validation hierarchy.
     *
     * @return ErrorCollectorContract The error collector instance
     */
    public function getCollector(): ErrorCollectorContract
    {
        return $this->collector;
    }

    /**
     * Returns the name or index of the current field.
     *
     * For array elements, this will be the numeric index. For object properties,
     * this will be the property name. For the root context, this returns null.
     *
     * @return string|int|null Name/index of field, or null if root.
     */
    public function getName(): string|int|null
    {
        return $this->name;
    }

    /**
     * Checks whether the current context has a parent (i.e., is nested).
     *
     * Returns true if this context represents a nested field within a larger data structure.
     * Returns false if this is the root context representing the top-level data being validated.
     *
     * @return bool True if it has a parent, false if it's the root context.
     */
    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    /**
     * Gets the parent context of the current field.
     *
     * Returns the parent FieldContext instance if this is a nested field, allowing
     * traversal up the validation hierarchy. Returns null for the root context.
     *
     * @return FieldContext|null Parent context or null if root.
     */
    public function getParent(): FieldContext|null
    {
        return $this->parent;
    }

    /**
     * Returns the collected validation error messages.
     *
     * Retrieves all validation errors that have been committed to the error collector.
     * This includes errors from this field and all nested fields in the validation tree.
     * Staged errors that haven't been committed yet are not included.
     *
     * @return array<string, array{rule: string, message: string}> 
     * Associative array indexed by field paths with error details.
     */
    // public function getMessages(): array
    // {
    //     return $this->collector->all();
    // }

    /**
     * Checks if the field has passed all validations.
     *
     * Returns true if no validation errors have been reported for this specific field.
     * This only considers the current field's validity, not the global validation state.
     * Use isGloballyValid() to check if the entire validation tree is valid.
     *
     * @return bool True if valid, false if errors exist.
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Checks if the entire validation process (including all nested fields) is valid.
     * This method checks the global error collector to determine if any errors exist
     * anywhere in the validation tree.
     *
     * Use this method to determine if the complete data structure being validated
     * has passed all validation rules, including nested fields and arrays.
     *
     * @return bool True if no errors exist globally, false if any field has errors.
     */
    public function isGloballyValid(): bool
    {
        return !$this->collector->hasErrors();
    }

    /**
     * Checks if the field has encountered any validation errors.
     * 
     * This is the inverse of isValid() - returns true if this specific field
     * has failed validation, false if it has passed all validation rules.
     *
     * @return bool True if errors exist, false if valid.
     */
    public function hasErrors(): bool
    {
        return !$this->valid
            || $this->hasFatalError
            || !empty($this->stagedErrors);
    }

    /**
     * Checks if the field has encountered a fatal validation error.
     *
     * Fatal errors are severe validation failures that typically prevent
     * further validation processing on this field. They are usually caused
     * by type mismatches or fundamental data structure issues.
     *
     * @return bool True if a fatal error occurred, false otherwise.
     */
    public function hasFatalError(): bool
    {
        return $this->hasFatalError;
    }

    /**
     * Gets the field's path as an array of keys (for nested structures).
     *
     * Returns the hierarchical path from the root to this field as an array of keys.
     * Each element represents a step in the nested data structure navigation.
     * For example, accessing user.address.city would return ['user', 'address', 'city'].
     *
     * @return array<int, string|int> Sequence of keys/indexes from root to current field.
     */
    public function getPathSegments(): array
    {
        return $this->path;
    }

    /**
     * Returns the dot-notated string path to the field (e.g., "user.address.city").
     *
     * Converts the hierarchical path to a dot-separated string representation,
     * commonly used for error reporting and field identification in forms.
     * Returns null for the root context which has no path.
     *
     * @return string|null Qualified path, or null if root context.
     */
    public function getQualifiedPath(): string|null
    {
        if (empty($this->path)) {
            return null;
        }

        return implode('.', $this->path);
    }

    /**
     * Returns a wildcard path where numeric indices are replaced with asterisks.
     *
     * This method creates a path pattern useful for validation rules that apply to
     * all elements in indexed arrays. Numeric indices (0, 1, 2, etc.) are replaced
     * with '*' while string keys in associative arrays are preserved.
     *
     * @return string|null Wildcard path pattern, or null if root context.
     */
    public function getWildcardPath(): string|null
    {
        if (empty($this->path)) {
            return null;
        }

        $wildcardPath = array_map(function ($segment): string {
            // Replace numeric indices with asterisks, keep string keys as-is
            return is_int($segment) ? '*' : $segment;
        }, $this->path);

        return implode('.', $wildcardPath);
    }

    /**
     * Updates the value under validation.
     *
     * Replaces the current field value with a new value. This is commonly used
     * for data sanitization and transformation during the validation process.
     * The new value becomes the current value for subsequent validation rules.
     *
     * @param mixed $value New value to replace the current one (can be any type).
     * @return self Returns this instance for method chaining.
     */
    public function mutate(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Discards (removes) this field from its parent context.
     *
     * Removes this field's key-value pair from the parent's data structure.
     * This is useful for removing invalid or unwanted fields during validation.
     * Cannot be called on the root context as it has no parent to remove from.
     *
     * @return self Returns this instance for method chaining.
     * @throws \LogicException If trying to discard root context or if parent is missing.
     */
    public function discardField(): self
    {
        // Ensure this is not the root context (root cannot be discarded)
        if (!$this->hasParent()) {
            throw new \LogicException('Cannot discard root context or empty path.');
        }

        // Retrieve the parent context
        $parent = $this->getParent();

        // Get a reference to the parent value
        $parentValue = &$parent->value;

        // Remove this field from the parent structure using its name/index
        unset($parentValue[$this->getName()]);

        // Return this context for method chaining
        return $this;
    }

    /**
     * Reports a validation error for this field and marks it as invalid.
     *
     * If staging is enabled, stages the error for later commit when commitStagedErrors() is called.
     * If staging is disabled (default), immediately reports the error to the collector.
     * The error message can contain a '{{ field }}' placeholder that will be replaced
     * with the field name.
     *
     * @param string $message The error message (can contain '{{ field }}' placeholder).
     * @param string $rule The validation rule identifier (e.g., 'required', 'email').
     * @param array<string, mixed> $meta Additional data for message interpolation (optional).
     * @return self Returns this instance for method chaining.
     */
    public function report(string $message, string $rule, array $meta = []): self
    {
        // Mark this field as invalid
        $this->valid = false;

        // Retrieve the custom message for this rule using the message provider
        $message = $this->messageProvider->getMessage($message, $rule, $this, $meta);

        if ($this->isStagingEnabled()) {
            // Stage the error for later committing
            $this->stagedErrors[] = function () use ($rule, $message): void {
                $this->collector->report($this, $rule, $message);
            };
        } else {
            // Report the error immediately
            $this->collector->report($this, $rule, $message);
        }

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Reports a fatal validation error and stops further validation on this field.
     *
     * Marks the field as having encountered a fatal error and reports it as a regular
     * validation error. Fatal errors typically indicate severe issues like type mismatches
     * or fundamental data structure problems that prevent further validation processing.
     * The error follows the same staging behavior as regular errors - it's staged if
     * staging is enabled, or reported immediately if staging is disabled.
     *
     * @param string $message Fatal error message (can contain '{{ field }}' placeholder).
     * @param string $rule Validation rule identifier (e.g., 'type', 'required').
     * @return self Returns this instance for method chaining.
     */
    public function fatal(string $message, string $rule, array $meta = []): self
    {
        $this->hasFatalError = true;

        $this->report($message, $rule, $meta);
        return $this;
    }

    /**
     * Commits all staged errors to the error collector.
     *
     * Executes all staged error reporting functions, adding their errors to the
     * shared error collector. This makes the errors visible in validation results
     * and clears the staged errors array. Use this method to finalize error reporting
     * after validation rules have been processed.
     *
     * @return self Returns this instance for method chaining.
     */
    public function commitStagedErrors(): self
    {
        foreach ($this->stagedErrors as $error) {
            $error();
        }
        $this->clearStagedErrors();
        return $this;
    }

    /**
     * Clears all staged errors without committing them to the error collector.
     *
     * Discards all staged error reporting functions without executing them.
     * This effectively cancels any pending error reports, allowing validation
     * to continue without those errors being recorded. Useful for conditional
     * validation where errors may need to be discarded based on later conditions.
     *
     * @return self Returns this instance for method chaining.
     */
    public function clearStagedErrors(): self
    {
        $this->stagedErrors = [];
        return $this;
    }

    /**
     * Enables error staging for this context.
     *
     * When staging is enabled, validation errors are not immediately reported to the
     * error collector. Instead, they are staged and must be explicitly committed later
     * using commitStagedErrors(). This allows for conditional error reporting.
     *
     * @return self Returns this instance for method chaining.
     */
    public function enableStaging(): self
    {
        $this->stagingEnabled = true;
        return $this;
    }

    /**
     * Disables error staging for this context.
     *
     * When staging is disabled (default behavior), validation errors are immediately
     * reported to the error collector. Any currently staged errors remain staged
     * and must still be committed separately if needed.
     *
     * @return self Returns this instance for method chaining.
     */
    public function disableStaging(): self
    {
        $this->stagingEnabled = false;
        return $this;
    }

    /**
     * Checks if error staging is currently enabled for this context.
     *
     * Returns true if errors are being staged for later commit, false if they
     * are being reported immediately to the error collector.
     *
     * @return bool True if staging is enabled, false otherwise.
     */
    public function isStagingEnabled(): bool
    {
        return $this->stagingEnabled;
    }

    /**
     * Creates a new FieldContext for a nested field (e.g., sub-array or object property).
     *
     * Creates a new validation context for a nested field within the current value.
     * If the current value is an array and contains the specified key, the nested
     * context will reference that value. Otherwise, it references a null value.
     * The new context inherits the error collector and builds upon the current path.
     *
     * @param string|int $key Key/index of the nested field to create context for.
     * @return FieldContext New context instance for the nested field.
     */
    public function buildNestedContext(int|string $key): FieldContext
    {
        if (is_array($this->value) && array_key_exists($key, $this->value)) {
            $nestedValue = &$this->value[$key];
        } else {
            $null = null;
            $nestedValue = &$null;
        }

        return new FieldContext($nestedValue, $key, $this->messageProvider, $this->collector, $this);
    }
}