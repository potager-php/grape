<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Grape;
use Potager\Grape\FieldContext;
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Contracts\MessageProviderContract;
use Potager\Grape\Contracts\ErrorCollectorContract;

abstract class AbstractValidator
{
    /**
     * Collection of validation rules to be applied to the field value.
     * 
     * Each rule is a callable that accepts a FieldContext and performs
     * validation logic, potentially modifying the context's value or
     * adding validation errors.
     * 
     * @var callable[]
     */
    protected $rules = [];

    /**
     * Determines whether null values are acceptable for this validator.
     * 
     * When true, null values will pass validation without applying
     * any other rules. When false, null values will trigger a
     * validation error.
     * 
     * @var bool
     */
    protected $nullable = false;

    /**
     * Indicates whether this field is required in the validation schema.
     * 
     * This property is used by parent validators (like SchemaValidator)
     * to determine if missing fields should cause validation errors.
     * 
     * @var bool
     */
    protected $required = false;

    /**
     * Optional message provider for custom error messages.
     * 
     * If set, this provider will be used to retrieve localized or
     * custom error messages for validation failures. If null,
     * default messages will be used.
     * 
     * @var MessageProviderContract|null
     */
    protected ?MessageProviderContract $messageProvider = null;

    protected $errorCollectorFactory = null;

    /**
     * Validates a value against the configured rules and returns the processed value.
     * 
     * @param mixed $value The value to validate
     * @return mixed The validated and potentially transformed value
     * @throws ValidationException When validation fails
     */
    public function validate($value, ?MessageProviderContract $messageProvider = null, ?ErrorCollectorContract $errorCollector = null): mixed
    {
        // Store original value for error reporting
        $originalValue = $value;

        // Retrieve the message provider for custom error messages
        $messageProvider ??= $this->messageProvider ?? Grape::getMessageProvider();

        // Retrieve the error collector for custom error formatting
        $errorCollector ??= $this->getErrorCollector() ?? Grape::getErrorCollector();

        // Create validation context to track errors and transformations
        $context = new FieldContext($value, null, messageProvider: $messageProvider, errorCollector: $errorCollector);

        // Execute validation rules within the context
        $this->applyRules($value, $context);

        // Check if validation passed globally
        if (!$context->isGloballyValid()) {
            // Throw exception with error details and original value
            throw $errorCollector->createError()->attachRaw($originalValue);
        }

        // Return the validated (and potentially transformed) value
        return $context->getValue();
    }

    /**
     * Attempts to validate a value without throwing an exception.
     *
     * Returns a 2-tuple [ValidationException|null, mixed|null]:
     * - index 0: a ValidationException when validation failed, otherwise null
     * - index 1: the validated value when validation succeeded, otherwise null
     *
     * This variant is useful when callers prefer to handle errors inline
     * instead of using exceptions.
     *
     * @param mixed $value The value to validate
     * @param MessageProviderContract|null $messageProvider Optional custom message provider
     * @return array{0:ValidationException|null,1:mixed|null} Tuple [error, value]
     */
    public function check($value, ?MessageProviderContract $messageProvider = null): array
    {
        try {
            // Attempt to validate the value and return the validated value as the second element of the tuple
            return [null, $this->validate($value, $messageProvider)];
        } catch (ValidationException $e) {
            // Catch validation exceptions and return them as the first element of the tuple
            return [$e, null];
        }
    }

    /**
     * Applies all configured validation rules to the given value within the provided context.
     * 
     * This method handles null value validation first, then iterates through
     * all rules, applying them sequentially until a fatal error is encountered
     * or all rules have been processed.
     * 
     * @param mixed $value The value to validate against the rules
     * @param FieldContext $ctx The validation context for tracking errors and transformations
     * @return void
     * @internal This method is for internal use by the validation framework
     */
    public function applyRules($value, FieldContext $ctx)
    {
        // Handle null values first - either allow them or fail immediately
        if ($value === null) {
            if ($this->nullable) {
                return;
            }

            $ctx->fatal('{{ field }} cannot be null', 'not_nullable');
            return;
        }

        // Apply each rule sequentially, stopping if a fatal error occurs
        foreach ($this->rules as $rule) {
            if (!$ctx->hasFatalError()) {
                $rule($ctx);
            }
        }
    }

    /**
     * Sets a custom message provider for this validator.
     * 
     * This allows the validator to use a specific message provider
     * for retrieving localized or custom error messages. If not set,
     * the default message provider will be used.
     * 
     * @param MessageProviderContract $provider The message provider to use
     * @return static Returns the validator instance for method chaining
     */
    public function setMessageProvider(MessageProviderContract $provider): static
    {
        $this->messageProvider = $provider;
        return $this;
    }

    public function setErrorCollector(callable $factory): static
    {
        $testInstance = $factory();
        if (!$testInstance instanceof ErrorCollectorContract) {
            throw new \InvalidArgumentException('The provided factory must return an instance of ErrorCollectorContract.');
        }

        $this->errorCollectorFactory = $factory;
        return $this;
    }

    protected function getErrorCollector(): ?ErrorCollectorContract
    {
        if (!$this->errorCollectorFactory) {
            return null;
        }

        $factory = $this->errorCollectorFactory;
        return $factory();
    }
    /**
     * Configures the validator to accept null values.
     * 
     * When a validator is marked as nullable, null values will pass
     * validation without triggering any validation rules. This is
     * useful for optional fields that may legitimately be null.
     * 
     * @return static Returns the validator instance for method chaining
     */
    public function nullable(): static
    {
        $this->nullable = true;
        return $this;
    }

    /**
     * Marks this validator as required in the validation schema.
     * 
     * Required validators must have their associated field present
     * in the data being validated. Parent validators (like ObjectValidator)
     * use this information to ensure all required fields are provided.
     * 
     * @return static Returns the validator instance for method chaining
     */
    public function required(): static
    {
        $this->required = true;
        return $this;
    }

    /**
     * Checks if this validator is marked as required.
     * 
     * This method is used internally by parent validators to determine
     * whether missing fields should trigger validation errors. Required
     * validators must have their associated field present in the input data.
     * 
     * @return bool True if the validator is required, false otherwise
     * @internal This method is for internal use by the validation framework
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Adds a custom validation rule to the validator.
     * 
     * The provided rule must be a callable that accepts a FieldContext
     * as its only argument. This allows for flexible, user-defined validation
     * logic that can be applied to the field value.
     * 
     * @param callable $rule The custom validation rule to add
     * @return static Returns the validator instance for method chaining
     * @throws \InvalidArgumentException If the rule does not accept a FieldContext parameter
     */
    public function custom(callable $rule): static
    {
        $reflection = new \ReflectionFunction($rule);
        $parameters = $reflection->getParameters();

        if (count($parameters) !== 1) {
            throw new \InvalidArgumentException('Custom rule must accept exactly one parameter');
        }

        $parameter = $parameters[0];
        $type = $parameter->getType();

        if (!$type instanceof \ReflectionNamedType || $type->getName() !== FieldContext::class) {
            throw new \InvalidArgumentException('Custom rule must accept a FieldContext parameter');
        }

        $this->rules[] = $rule;
        return $this;
    }

}