<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\FieldContext;

/**
 * Validator for null values.
 * 
 * This class ensures that the field value is null. If the value is not null,
 * a validation error is triggered.
 * 
 * @package Potager\Grape\Validators
 */
class NullValidator extends AbstractValidator
{
    /**
     * Indicates whether the field is nullable.
     * 
     * @var bool
     */
    protected $nullable = true;

    /**
     * Create a new null validator instance.
     * 
     * This constructor adds a rule to ensure that the field value is null.
     */
    public function __construct()
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if ($value !== null)
                $ctx->fatal(DefaultMessages::$messages['null'], 'null');
        };
    }
}
