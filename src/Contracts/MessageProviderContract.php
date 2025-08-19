<?php

namespace Potager\Grape\Contracts;

use Potager\Grape\FieldContext;

interface MessageProviderContract
{
    /**
     * Get a message for a specific validation rule.
     *
     * @param string $defaultMessage The default message to return if no custom message is defined.
     * @param string $rule The validation rule for which the message is requested.
     * @param FieldContext $field The field context containing information about the field being validated.
     * @param array $meta Additional metadata that may be used to customize the message.
     *
     * @return string The formatted message for the validation rule.
     */
    public function getMessage(string $defaultMessage, string $rule, FieldContext $field, array $meta = []): string;
}
