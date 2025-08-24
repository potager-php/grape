<?php

namespace Potager\Grape\Messages;

class DefaultMessages
{
    public static $messages = [
        // General messages
        "required" => "The {field} is required.",

        // String validation messages
        "string" => "The {field} must be a string.",
        "email" => "The {field} must be a valid email address.",
        "mobile" => "The {field} must be a valid mobile phone number.",
        "creditCard" => "The {field} must be a valid {providersList} card number.",
        "pattern" => "The {field} must match the required pattern.",
        "url" => "The {field} must be a valid URL.",
        "activeUrl" => "The {field} must be a valid and reachable URL.",
        "alphabetic" => "The {field} must contain only letters.",
        "alphaNumeric" => "The {field} must contain only letters and numbers.",
        "numeric" => "The {field} must contain only numbers.",
        "minLength" => "The {field} must be at least {length} characters long.",
        "maxLength" => "The {field} must not exceed {length} characters.",
        "fixedLength" => "The {field} must be exactly {length} characters long.",
        "prefix" => "The {field} must start with {prefix}.",
        "suffix" => "The {field} must end with {suffix}.",
        "contains" => "The {field} must contain {substring}.",
        "ip" => "The {field} must be a valid IP address.",
        "noWhitespace" => "The {field} must not contain any spaces.",
        "json" => "The {field} must be valid JSON.",
        "empty" => "The {field} must be empty.",
        "notEmpty" => "The {field} must not be empty.",

        // Accepted validation messages
        "accepted" => "The {field} must be accepted.",

        // Literal validation messages
        "literal" => "The {field} must be equal to {expected}.",

        // Null validation messages
        "null" => "The {field} must be null.",

        // Boolean validation messages
        "boolean" => "The {field} must be a boolean.",
        "true" => "The {field} must be true.",
        "false" => "The {field} must be false.",

        // Number validation messages
        "number" => "The {field} must be a number.",
        "integer" => "The {field} must be an integer.",
        "float" => "The {field} must be a float.",
        "positive" => "The {field} must be a positive number.",
        "negative" => "The {field} must be a negative number.",
        "min" => "The {field} must be greater than or equal to {min}.",
        "max" => "The {field} must be less than or equal to {max}.",
        "range" => "The {field} must be between {min} and {max} (inclusive).",
        "zero" => "The {field} must be zero.",
        "notZero" => "The {field} must not be zero.",
        "even" => "The {field} must be an even number.",
        "odd" => "The {field} must be an odd number.",
        "multipleOf" => "The {field} must be a multiple of {factor}.",
        "NaN" => "The {field} must be NaN (Not a Number).",
        "notNaN" => "The {field} must not be NaN (Not a Number).",
        "withoutDecimal" => "The {field} must be a whole number.",

        // Collection validation messages
        "collection" => "The {field} must be an array.",
        "collectionEmpty" => "The {field} must be empty.",
        "collectionNotEmpty" => "The {field} must not be empty.",
        "distinct" => "The {field} must have distinct items.",
        "collectionMinLength" => "The {field} must have at least {length} items.",
        "collectionMaxLength" => "The {field} must not exceed {length} items.",
        "collectionFixedLength" => "The {field} must have exactly {length} items.",

        // Tuple validation messages
        "tuple" => "The {field} must be a tuple with at least {length} items.",
        "tupleDistinct" => "The {field} must contain unique items.",
        "unknownItem" => "The {field} has an unknown item at index {index}.",
    ];
}

