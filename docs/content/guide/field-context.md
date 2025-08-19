---
outline: deep
---

# Field Context

The `FieldContext` object is available within custom rules, custom message providers, and other validation-related logic. It provides detailed information about the field currently being validated, including its name, path, and value.

The most commonly used methods on `FieldContext` are:

-   **`getValue()`** – Retrieves the current value being validated.
-   **`getName()`** – Returns the name or index of the current field.
-   **`report()`** – Reports a validation error for the current field.
-   **`mutate()`** – Modifies the current value being validated.

Below is the complete definition of `FieldContext`:

```php
class FieldContext
{
    /**
     * Retrieves the current value under validation.
     *
     * Returns the raw value that is being validated. This could be of any type
     * (string, array, object, etc.) depending on the data structure being validated.
     *
     * @return mixed The current value (could be any type).
     */
    public function getValue(): mixed;

    /**
     * Get the validation error collector.
     *
     * Returns the shared error collector instance that accumulates validation errors
     * across the entire validation tree. This is the same instance used by all
     * contexts in the validation hierarchy.
     *
     * @return ValidationErrorCollectorInterface The error collector instance
     */
    public function getCollector(): ValidationErrorCollectorInterface;

    /**
     * Returns the name or index of the current field.
     *
     * For array elements, this will be the numeric index. For object properties,
     * this will be the property name. For the root context, this returns null.
     *
     * @return string|int|null Name/index of field, or null if root.
     */
    public function getName(): string|int|null;

    /**
     * Checks whether the current context has a parent (i.e., is nested).
     *
     * Returns true if this context represents a nested field within a larger data structure.
     * Returns false if this is the root context representing the top-level data being validated.
     *
     * @return bool True if it has a parent, false if it's the root context.
     */
    public function hasParent(): bool;

    /**
     * Gets the parent context of the current field.
     *
     * Returns the parent FieldContext instance if this is a nested field, allowing
     * traversal up the validation hierarchy. Returns null for the root context.
     *
     * @return FieldContext|null Parent context or null if root.
     */
    public function getParent(): FieldContext|null;

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
    public function getMessages(): array;

    /**
     * Checks if the field has passed all validations.
     *
     * Returns true if no validation errors have been reported for this specific field.
     * This only considers the current field's validity, not the global validation state.
     * Use isGloballyValid() to check if the entire validation tree is valid.
     *
     * @return bool True if valid, false if errors exist.
     */
    public function isValid(): bool;

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
    public function isGloballyValid(): bool;

    /**
     * Checks if the field has encountered any validation errors.
     *
     * This is the inverse of isValid() - returns true if this specific field
     * has failed validation, false if it has passed all validation rules.
     *
     * @return bool True if errors exist, false if valid.
     */
    public function hasErrors(): bool;

    /**
     * Checks if the field has encountered a fatal validation error.
     *
     * Fatal errors are severe validation failures that typically prevent
     * further validation processing on this field. They are usually caused
     * by type mismatches or fundamental data structure issues.
     *
     * @return bool True if a fatal error occurred, false otherwise.
     */
    public function hasFatalError(): bool;

    /**
     * Gets the field's path as an array of keys (for nested structures).
     *
     * Returns the hierarchical path from the root to this field as an array of keys.
     * Each element represents a step in the nested data structure navigation.
     * For example, accessing user.address.city would return ['user', 'address', 'city'].
     *
     * @return array<int, string|int> Sequence of keys/indexes from root to current field.
     */
    public function getPathSegments(): array;

    /**
     * Returns the dot-notated string path to the field (e.g., "user.address.city").
     *
     * Converts the hierarchical path to a dot-separated string representation,
     * commonly used for error reporting and field identification in forms.
     * Returns null for the root context which has no path.
     *
     * @return string|null Qualified path, or null if root context.
     */
    public function getQualifiedPath(): string|null;

    /**
     * Returns a wildcard path where numeric indices are replaced with asterisks.
     *
     * This method creates a path pattern useful for validation rules that apply to
     * all elements in indexed arrays. Numeric indices (0, 1, 2, etc.) are replaced
     * with '*' while string keys in associative arrays are preserved.
     *
     * @return string|null Wildcard path pattern, or null if root context.
     */
    public function getWildcardPath(): string|null;

    /**
     * Retrieves the PDO instance used for database-related validation.
     *
     * Returns the PDO database connection that can be used by validators
     * requiring database access, such as uniqueness checks or reference validation.
     * This property must be set before use, typically during context initialization.
     *
     * @return \PDO Active PDO database connection instance.
     */
    public function getPDO(): \PDO;

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
    public function mutate(mixed $value): self;

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
    public function discardField(): self;

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
    public function report(string $message, string $rule, array $meta = []): self;

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
    public function fatal(string $message, string $rule, array $meta = []): self;

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
    public function commitStagedErrors(): self;

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
    public function clearStagedErrors(): self;

    /**
     * Enables error staging for this context.
     *
     * When staging is enabled, validation errors are not immediately reported to the
     * error collector. Instead, they are staged and must be explicitly committed later
     * using commitStagedErrors(). This allows for conditional error reporting.
     *
     * @return self Returns this instance for method chaining.
     */
    public function enableStaging(): self;

    /**
     * Disables error staging for this context.
     *
     * When staging is disabled (default behavior), validation errors are immediately
     * reported to the error collector. Any currently staged errors remain staged
     * and must still be committed separately if needed.
     *
     * @return self Returns this instance for method chaining.
     */
    public function disableStaging(): self;

    /**
     * Checks if error staging is currently enabled for this context.
     *
     * Returns true if errors are being staged for later commit, false if they
     * are being reported immediately to the error collector.
     *
     * @return bool True if staging is enabled, false otherwise.
     */
    public function isStagingEnabled(): bool;

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
    public function buildNestedContext(int|string $key): FieldContext;
}
```
