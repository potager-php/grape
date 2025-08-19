<?php

namespace Potager\Grape\Validators;

use Potager\Grape\DefaultMessages;
use Potager\Grape\FieldContext;

/**
 * Validator for accepted values.
 * 
 * This class ensures that the field value is one of the accepted values,
 * such as true, 1, 'true', '1', or 'on'.
 * 
 * @package Potager\Grape\Validators
 */
class AcceptedValidator extends AbstractValidator
{
    /**
     * Create a new accepted validator instance.
     * 
     * This constructor adds a rule to ensure that the field value is one of the accepted values.
     */
    public function __construct()
    {
        $this->rules[] = function (FieldContext $ctx) {
            $value = $ctx->getValue();
            if (!in_array($value, [true, 1, 'true', '1', 'on'], true))
                $ctx->fatal(DefaultMessages::$messages['accepted'], 'accepted');
            return true;
        };
    }
}