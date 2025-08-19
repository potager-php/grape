<?php

namespace Potager\Grape\Validators;

/**
 * Validator that accepts any value without type restrictions.
 *
 * The MixedValidator is a pass-through validator that accepts values of any type
 * (strings, integers, floats, booleans, arrays, objects, null, etc.) without
 * performing any type validation. It's useful when you need to apply other
 * validation rules without enforcing a specific type constraint.
 */
class MixedValidator extends AbstractValidator
{

}