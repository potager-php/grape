<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\FieldContext;

/**
 * Validator that checks if a value is strictly equal to a given literal.
 *
 * The literal can be a static value or a callable that returns the expected value
 * based on the current validation context.
 */
class LiteralValidator extends AbstractValidator
{
    /**
     * Constructs a validator that checks for strict equality to a literal value.
     *
     * @param mixed $literal The expected value, or a callable returning the expected value for the context.
     */
    public function __construct(mixed $literal)
    {
        $this->rules[] = function (FieldContext $ctx) use ($literal): void {
            $value = $ctx->getValue();
            $expected = is_callable($literal) ? $literal($ctx) : $literal;
            if ($value !== $expected) {
                $expected = is_scalar($expected) ? var_export($expected, true) : json_encode($expected);
                $ctx->report(DefaultMessages::$messages['literal'], 'literal', ['expected' => $expected]);
            }
        };
    }
}