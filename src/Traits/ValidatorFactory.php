<?php

namespace Potager\Grape\Traits;

use Potager\Grape\Validators\AbstractValidator;
use Potager\Grape\Validators\AcceptedValidator;
use Potager\Grape\Validators\BooleanValidator;
use Potager\Grape\Validators\CollectionValidator;
use Potager\Grape\Validators\FloatValidator;
use Potager\Grape\Validators\IntegerValidator;
use Potager\Grape\Validators\LiteralValidator;
use Potager\Grape\Validators\MixedValidator;
use Potager\Grape\Validators\NullValidator;
use Potager\Grape\Validators\NumberValidator;
use Potager\Grape\Validators\SchemaValidator;
use Potager\Grape\Validators\StringValidator;
use Potager\Grape\Validators\TupleValidator;

/**
 * Trait ValidatorFactory
 * 
 * Provides factory methods for creating validator instances.
 * 
 * This trait contains all the static factory methods that create validators
 * for different data types and validation scenarios. Each method returns
 * a configured validator instance ready for use.
 */
trait ValidatorFactory
{
    /**
     * Create a number validator that accepts both integers and floats.
     * 
     * @param bool $strict Whether to use strict type checking
     * @return NumberValidator
     */
    public static function number(bool $strict = false): NumberValidator
    {
        return new NumberValidator($strict);
    }

    /**
     * Create an integer validator for whole numbers.
     * 
     * @param bool $strict Whether to use strict type checking
     * @return IntegerValidator
     */
    public static function integer(bool $strict = false): IntegerValidator
    {
        return new IntegerValidator($strict);
    }

    /**
     * Create a float validator for decimal numbers.
     * 
     * @param bool $strict Whether to use strict type checking
     * @return FloatValidator
     */
    public static function float(bool $strict = false): FloatValidator
    {
        return new FloatValidator($strict);
    }

    /**
     * Create a string validator for text values.
     * 
     * @param bool $strict Whether to use strict type checking
     * @return StringValidator
     */
    public static function string(bool $strict = false): StringValidator
    {
        return new StringValidator($strict);
    }

    /**
     * Create a boolean validator for true/false values.
     * 
     * @param bool $strict Whether to use strict type checking
     * @return BooleanValidator
     */
    public static function boolean(bool $strict = false): BooleanValidator
    {
        return new BooleanValidator($strict);
    }

    /**
     * Create an accepted validator for confirmation fields (checkboxes, terms of service, etc.).
     * 
     * @return AcceptedValidator
     */
    public static function accepted(): AcceptedValidator
    {
        return new AcceptedValidator();
    }

    /**
     * Create a collection validator for arrays of items.
     * 
     * @param AbstractValidator|null $itemValidator Optional validator for each item in the collection
     * @return CollectionValidator
     */
    public static function collection(?AbstractValidator $itemValidator = null): CollectionValidator
    {
        return new CollectionValidator($itemValidator);
    }

    /**
     * Create a schema validator for validating object structures.
     * 
     * @param array|null $schema Optional schema definition for the object
     * @return SchemaValidator
     */
    public static function schema(?array $schema = null): SchemaValidator
    {
        return new SchemaValidator($schema);
    }

    /**
     * Create a tuple validator for arrays with specific validators for each position.
     * 
     * @param array $itemsValidators Array of validators, one for each position in the tuple
     * @return TupleValidator
     */
    public static function tuple(array $itemsValidators): TupleValidator
    {
        return new TupleValidator($itemsValidators);
    }

    /**
     * Create a null validator that only accepts null values.
     * 
     * @return NullValidator
     */
    public static function null(): NullValidator
    {
        return new NullValidator();
    }

    /**
     * Create a mixed validator that accepts any type of value.
     * 
     * @return MixedValidator
     */
    public static function mixed(): MixedValidator
    {
        return new MixedValidator();
    }

    /**
     * Create a literal validator that only accepts a specific value.
     * 
     * @param mixed $literal The exact value that should be accepted
     * @return LiteralValidator
     */
    public static function literal(mixed $literal): LiteralValidator
    {
        return new LiteralValidator($literal);
    }
}