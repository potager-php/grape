<?php

namespace Potager\Grape;

use Potager\Grape\Contracts\MessageProviderContract;

/**
 * A simplified implementation of MessageProvider that allows direct initialization
 * of custom messages and field display names through the constructor.
 * 
 * This class provides a hierarchical message resolution strategy and a convenient way
 * to set up validation messages and field aliases at instantiation time.
 * 
 * @package Potager\Grape
 */
class SimpleMessageProvider implements MessageProviderContract
{
    /**
     * Custom validation messages indexed by their keys.
     * Keys can be:
     * - Qualified paths: "user.name.required"
     * - Wildcard paths: "tags.*.integer"
     * - Rule names: "required"
     * 
     * @var array<string, string>
     */
    protected array $customMessages = [];

    /**
     * Field name aliases for display purposes.
     * Maps internal field names to user-friendly display names.
     * 
     * @var array<string, string>
     */
    protected array $fieldDisplayNames = [];

    /**
     * Creates a new SimpleMessageProvider with optional custom messages and field display names.
     * 
     * This constructor provides a convenient way to initialize the message provider
     * with predefined validation messages and field aliases without requiring
     * separate method calls after instantiation.
     * 
     * @param array<string, string> $messages Custom validation messages indexed by their keys.
     *                                       Keys can be rule names ("required"), qualified paths 
     *                                       ("user.name.required"), or wildcard paths ("*.name.required")
     * @param array<string, string> $fields Field display name aliases mapping internal field names
     *                                      to user-friendly display names (e.g., ["user_id" => "User ID"])
     */
    public function __construct(array $messages = [], array $fields = [])
    {
        $this->customMessages = $messages;
        $this->fieldDisplayNames = $fields;
    }

    /**
     * Interpolates placeholders in a message template with actual values.
     * 
     * Replaces placeholders in the format {key} with corresponding values
     * from the context array. This allows for dynamic message generation
     * with field names, values, and other contextual information.
     * 
     * @param string $messageTemplate The message template containing placeholders
     * @param array<string, mixed> $interpolationContext Key-value pairs for placeholder replacement
     * @return string The interpolated message with placeholders replaced
     */
    protected function interpolateMessage(string $messageTemplate, array $interpolationContext = []): string
    {
        foreach ($interpolationContext as $placeholderKey => $replacementValue) {
            $messageTemplate = str_replace("{{$placeholderKey}}", (string) $replacementValue, $messageTemplate);
        }
        return $messageTemplate;
    }

    /**
     * Retrieves a validation error message using a hierarchical resolution strategy.
     * 
     * The method searches for custom messages in the following priority order:
     * 1. Field-specific message: "{qualified_path}.{rule}" (e.g., "user.name.required")
     * 2. Wildcard path message: "{wildcard_path}.{rule}" (e.g., "*.name.required")
     * 3. Rule-only message: "{rule}" (e.g., "required")
     * 4. Default fallback message
     * 
     * @param string $defaultMessage The fallback message if no custom message is found
     * @param string $rule The validation rule name (e.g., "required", "min", "max")
     * @param FieldContext $field The context object containing field information
     * @param array<string, mixed> $meta Additional data for message interpolation
     * @return string The final interpolated validation message
     */
    public function getMessage(string $defaultMessage, string $rule, FieldContext $field, array $meta = []): string
    {
        // Get the display name for the field (using alias if available)
        $fieldDisplayName = $this->fieldDisplayNames[$field->getQualifiedPath()]
            ?? $this->fieldDisplayNames[$field->getWildcardPath()]
            ?? $field->getName()
            ?? 'value';

        // Strategy 1: Look for field-specific message using qualified path
        $qualifiedMessageKey = "{$field->getQualifiedPath()}.{$rule}";
        if (isset($this->customMessages[$qualifiedMessageKey])) {
            $customMessage = $this->customMessages[$qualifiedMessageKey];
            return $this->interpolateMessage($customMessage, [
                ...$meta,
                'field' => $fieldDisplayName,
            ]);
        }

        // Strategy 2: Look for wildcard path message
        $wildcardMessageKey = "{$field->getWildcardPath()}.{$rule}";
        if (isset($this->customMessages[$wildcardMessageKey])) {
            $customMessage = $this->customMessages[$wildcardMessageKey];
            return $this->interpolateMessage($customMessage, [
                'field' => $fieldDisplayName,
                ...$meta
            ]);
        }

        // Strategy 3: Look for rule-only message
        if (isset($this->customMessages[$rule])) {
            $customMessage = $this->customMessages[$rule];
            return $this->interpolateMessage($customMessage, [
                'field' => $fieldDisplayName,
                ...$meta
            ]);
        }

        // Strategy 4: Use the default message as fallback
        return $this->interpolateMessage($defaultMessage, [
            'field' => $fieldDisplayName,
            ...$meta
        ]);
    }
}