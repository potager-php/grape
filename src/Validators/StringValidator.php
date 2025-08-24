<?php

namespace Potager\Grape\Validators;

use Potager\Grape\Messages\DefaultMessages;
use Potager\Grape\FieldContext;
use Potager\Grape\Grape;
use Potager\Grape\Traits\CanBeUnique;

/**
 * String validator for validating and manipulating string values.
 * 
 * This validator provides comprehensive string validation including format validation,
 * content validation, length checks, pattern matching, and string transformations.
 * It supports both strict mode (only accepts string types) and non-strict mode
 * (accepts scalar values that can be cast to strings).
 */
class StringValidator extends AbstractValidator
{
    use CanBeUnique;

    /**
     * Whether to convert empty strings to null values.
     * 
     * @var bool
     */
    private bool $convertEmptyStringToNull = false;

    /**
     * Create a new string validator instance.
     * 
     * @param bool $strict Whether to enforce strict string type checking.
     *                     In strict mode, only string values are accepted.
     *                     In non-strict mode, scalar values are cast to strings.
     */
    public function __construct(bool $strict)
    {
        $convertEmptyStringToNull = $this->convertEmptyStringToNull;
        $this->rules[] = function (FieldContext $ctx) use ($strict, $convertEmptyStringToNull): void {
            $value = $ctx->getValue();
            if ($strict && !is_string($value))
                $ctx->fatal(DefaultMessages::$messages['string'], 'string');
            else if (!$strict && !is_scalar($value))
                $ctx->fatal(DefaultMessages::$messages['string'], 'string');
            else
                $ctx->mutate((string) $value);
        };
    }

    /**
     * Trim whitespace from the beginning and end of the string.
     * 
     * Removes whitespace (or other characters) from the beginning and end
     * of the string value and mutates the field context with the trimmed result.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function trim(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            $value = trim($value);
            $ctx->mutate($value);
        };

        return $this;
    }

    /**
     * Convert the string to lowercase.
     * 
     * Transforms the string value to lowercase using strtolower() and
     * mutates the field context with the transformed result.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function lowercase(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            $value = strtolower($value);
            $ctx->mutate($value);
        };

        return $this;
    }

    /**
     * Convert the string to uppercase.
     * 
     * Transforms the string value to uppercase using strtoupper() and
     * mutates the field context with the transformed result.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function uppercase(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            $value = strtoupper($value);
            $ctx->mutate($value);
        };

        return $this;
    }

    /**
     * Validate that the string has a minimum length.
     * 
     * Ensures the string contains at least the specified number of characters.
     * Reports a validation error if the string is shorter than the minimum.
     * 
     * @param int $min The minimum number of characters required.
     * @return static Returns the validator instance for method chaining.
     */
    public function minLength(int $min): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($min): void {
            $value = $ctx->getValue();
            if (strlen($value) < $min)
                $ctx->report(DefaultMessages::$messages['minLength'], 'minLength', ['length' => $min]);
        };

        return $this;
    }

    /**
     * Validate that the string does not exceed a maximum length.
     * 
     * Ensures the string contains no more than the specified number of characters.
     * Reports a validation error if the string is longer than the maximum.
     * 
     * @param int $max The maximum number of characters allowed.
     * @return static Returns the validator instance for method chaining.
     */
    public function maxLength(int $max): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($max): void {
            $value = $ctx->getValue();
            if (strlen($value) > $max)
                $ctx->report(DefaultMessages::$messages['maxLength'], 'maxLength', ['length' => $max]);
        };

        return $this;
    }

    /**
     * Validate that the string starts with a specific prefix.
     * 
     * Checks if the string begins with the specified prefix string.
     * Case sensitivity can be controlled via the second parameter.
     * 
     * @param string $prefix The prefix string to check for.
     * @param bool $caseSensitive Whether the comparison should be case-sensitive. Default is true.
     * @return static Returns the validator instance for method chaining.
     */
    public function prefix(string $prefix, bool $caseSensitive = true): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($prefix, $caseSensitive): void {
            $value = $ctx->getValue();
            $haystack = $caseSensitive
                ? $value
                : strtolower($value);
            $needle = $caseSensitive
                ? $prefix
                : strtolower($prefix);
            if (!str_starts_with($haystack, $needle))
                $ctx->report(DefaultMessages::$messages['prefix'], 'prefix', ['prefix' => $prefix]);
        };

        return $this;
    }

    /**
     * Validate that the string ends with a specific suffix.
     * 
     * Checks if the string ends with the specified suffix string.
     * Case sensitivity can be controlled via the second parameter.
     * 
     * @param string $suffix The suffix string to check for.
     * @param bool $caseSensitive Whether the comparison should be case-sensitive. Default is true.
     * @return static Returns the validator instance for method chaining.
     */
    public function suffix(string $suffix, bool $caseSensitive = true): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($suffix, $caseSensitive): void {
            $value = $ctx->getValue();
            $haystack = $caseSensitive
                ? $value
                : strtolower($value);
            $needle = $caseSensitive
                ? $suffix
                : strtolower($suffix);
            if (!str_ends_with($haystack, $needle))
                $ctx->report(DefaultMessages::$messages['suffix'], 'suffix', ['suffix' => $suffix]);
        };

        return $this;
    }

    /**
     * Validate that the string contains a specific substring.
     * 
     * Checks if the string contains the specified substring anywhere within it.
     * Case sensitivity can be controlled via the second parameter.
     * 
     * @param string $substring The substring to search for.
     * @param bool $caseSensitive Whether the search should be case-sensitive. Default is true.
     * @return static Returns the validator instance for method chaining.
     */
    public function contains(string $substring, bool $caseSensitive = true): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($substring, $caseSensitive): void {
            $value = $ctx->getValue();
            $haystack = $caseSensitive
                ? $value
                : strtolower($value);
            $needle = $caseSensitive
                ? $substring
                : strtolower($substring);
            if (!str_contains($haystack, $needle))
                $ctx->report(DefaultMessages::$messages['contains'], 'contains', ['substring' => $substring]);
        };

        return $this;
    }

    /**
     * Validate that the string has an exact length.
     * 
     * Ensures the string contains exactly the specified number of characters.
     * Reports a validation error if the string length doesn't match exactly.
     * 
     * @param int $length The exact number of characters required.
     * @return static Returns the validator instance for method chaining.
     */
    public function fixedLength(int $length): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($length): void {
            $value = $ctx->getValue();
            if (strlen($value) !== $length)
                $ctx->report(DefaultMessages::$messages['fixedLength'], 'fixedLength', ['length' => $length]);
        };

        return $this;
    }

    /**
     * Validate that the string contains only alphabetic characters.
     * 
     * Checks if the string consists only of alphabetic characters (a-z, A-Z).
     * Optionally allows whitespaces, dashes, and underscores based on parameters.
     * 
     * @param bool $allowWhitespaces Whether to allow whitespace characters. Default is true.
     * @param bool $allowDashes Whether to allow dash characters (-). Default is false.
     * @param bool $allowUnderscores Whether to allow underscore characters (_). Default is false.
     * @return static Returns the validator instance for method chaining.
     */
    public function alphabetic(bool $allowWhitespaces = true, bool $allowDashes = false, bool $allowUnderscores = false): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($allowWhitespaces, $allowDashes, $allowUnderscores): void {
            $value = $ctx->getValue();
            $str = $allowWhitespaces
                ? preg_replace('/\s+/u', '', $value)
                : $value;
            $str = $allowDashes
                ? str_replace('-', '', $str)
                : $str;
            $str = $allowUnderscores
                ? str_replace('_', '', $str)
                : $str;
            if (!ctype_alpha($str))
                $ctx->report(DefaultMessages::$messages['alphabetic'], 'alphabetic');
        };

        return $this;
    }

    /**
     * Validate that the string contains only numeric characters.
     * 
     * Checks if the string consists only of numeric characters (0-9).
     * Optionally allows whitespaces, dashes, and underscores based on parameters.
     * 
     * @param bool $allowWhitespaces Whether to allow whitespace characters. Default is true.
     * @param bool $allowDashes Whether to allow dash characters (-). Default is false.
     * @param bool $allowUnderscores Whether to allow underscore characters (_). Default is false.
     * @return static Returns the validator instance for method chaining.
     */
    public function numeric(bool $allowWhitespaces = true, bool $allowDashes = false, bool $allowUnderscores = false): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($allowWhitespaces, $allowDashes, $allowUnderscores): void {
            $value = $ctx->getValue();
            $str = $allowWhitespaces
                ? preg_replace('/\s+/u', '', $value)
                : $value;
            $str = $allowDashes
                ? str_replace('-', '', $str)
                : $str;
            $str = $allowUnderscores
                ? str_replace('_', '', $str)
                : $str;
            if (!ctype_digit($str))
                $ctx->report(DefaultMessages::$messages['numeric'], 'numeric');
        };

        return $this;
    }

    /**
     * Validate that the string contains only alphanumeric characters.
     * 
     * Checks if the string consists only of alphabetic and numeric characters (a-z, A-Z, 0-9).
     * Optionally allows whitespaces, dashes, and underscores based on parameters.
     * 
     * @param bool $allowWhitespaces Whether to allow whitespace characters. Default is true.
     * @param bool $allowDashes Whether to allow dash characters (-). Default is false.
     * @param bool $allowUnderscores Whether to allow underscore characters (_). Default is false.
     * @return static Returns the validator instance for method chaining.
     */
    public function alphaNumeric(bool $allowWhitespaces = true, bool $allowDashes = false, bool $allowUnderscores = false): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($allowWhitespaces, $allowDashes, $allowUnderscores): void {
            $value = $ctx->getValue();
            $str = $allowWhitespaces
                ? preg_replace('/\s+/u', '', $value)
                : $value;
            $str = $allowDashes
                ? str_replace('-', '', $str)
                : $str;
            $str = $allowUnderscores
                ? str_replace('_', '', $str)
                : $str;
            if (!ctype_alnum($str))
                $ctx->report(DefaultMessages::$messages['alphaNumeric'], 'alphaNumeric');
        };

        return $this;
    }

    /**
     * Validate that the string contains no whitespace characters.
     * 
     * Ensures the string does not contain any whitespace characters including
     * spaces, tabs, newlines, etc. Uses regex pattern matching for detection.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function noWhitespace(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (preg_match('/\s/', $value))
                $ctx->report(DefaultMessages::$messages['noWhitespace'], 'noWhitespace');
        };

        return $this;
    }

    /**
     * Validate that the string is a valid email address.
     * 
     * Uses PHP's built-in FILTER_VALIDATE_EMAIL filter to check if the string
     * conforms to a valid email address format according to RFC standards.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function email(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!filter_var($value, FILTER_VALIDATE_EMAIL))
                $ctx->report(DefaultMessages::$messages['email'], 'email');
        };

        return $this;
    }

    /**
     * Validate that the string is a valid mobile phone number.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function phone(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!Grape::helpers()->isMobilePhone($value)) {
                $ctx->report(DefaultMessages::$messages['mobile'], 'mobile');
            }
        };

        return $this;
    }

    /**
     * Validate that the string is valid JSON.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function json(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!Grape::helpers()->isJson($value)) {
                $ctx->report(DefaultMessages::$messages['json'], 'json');
            }
        };

        return $this;
    }

    /**
     * Validate that the string is a valid URL.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function url(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!Grape::helpers()->isUrl($value)) {
                $ctx->report(DefaultMessages::$messages['url'], 'url');
            }
        };

        return $this;
    }

    /**
     * Validate that the string is an active (reachable) URL.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function activeUrl(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (!Grape::helpers()->isActiveUrl($value)) {
                $ctx->report(DefaultMessages::$messages['activeUrl'], 'activeUrl');
            }
        };

        return $this;
    }

    /**
     * Validate that the string is a valid credit card number.
     * 
     * @param array|null $providers Optional array of credit card providers to validate against.
     * @return static Returns the validator instance for method chaining.
     */
    public function creditCard(?array $providers = null): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($providers): void {
            $value = $ctx->getValue();
            if (!Grape::helpers()->isCreditCard($value, $providers)) {
                $providersList = $providers ? implode(', ', $providers) : 'credit';
                $ctx->report(DefaultMessages::$messages['creditCard'], 'creditCard', ['providersList' => $providersList]);
            }
        };

        return $this;
    }

    /**
     * Validate that the string is a valid IP address.
     * 
     * @param string|null $version Optional IP version to validate ('4' for IPv4, '6' for IPv6).
     * @return static Returns the validator instance for method chaining.
     */
    public function ip(?string $version = null): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($version): void {
            $value = $ctx->getValue();
            if (!Grape::helpers()->isIp($value, $version)) {
                $ctx->report(DefaultMessages::$messages['ip'], 'ip');
            }
        };

        return $this;
    }

    /**
     * Validate that the string is empty.
     * 
     * Ensures the string has zero length. Optionally ignores whitespace
     * characters when determining if the string is empty.
     * 
     * @param bool $ignoreWhitespaces Whether to ignore whitespace when checking emptiness.
     *                                If true, strings containing only whitespace are considered empty.
     * @return static Returns the validator instance for method chaining.
     */
    public function empty(bool $ignoreWhitespaces = true): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($ignoreWhitespaces): void {
            $value = $ctx->getValue();
            $string = $ignoreWhitespaces
                ? trim($value)
                : $value;
            if (strlen($string) > 0)
                $ctx->report(DefaultMessages::$messages['empty'], 'empty');
        };

        return $this;
    }

    /**
     * Validate that the string is not empty.
     * 
     * Ensures the string has at least one character. Reports a validation
     * error if the string length is zero.
     * 
     * @return static Returns the validator instance for method chaining.
     */
    public function notEmpty(): static
    {
        $this->rules[] = function (FieldContext $ctx): void {
            $value = $ctx->getValue();
            if (strlen($value) === 0)
                $ctx->report(DefaultMessages::$messages['notEmpty'], 'notEmpty');
        };

        return $this;
    }

    /**
     * Validate that the string matches a regular expression pattern.
     * 
     * Uses preg_match() to test if the string matches the provided
     * regular expression pattern. The pattern should be a valid PHP regex.
     * 
     * @param string $pattern The regular expression pattern to match against.
     *                        Should include delimiters and any regex flags.
     * @return static Returns the validator instance for method chaining.
     */
    public function pattern(string $pattern): static
    {
        $this->rules[] = function (FieldContext $ctx) use ($pattern): void {
            $value = $ctx->getValue();
            if (!preg_match($pattern, $value))
                $ctx->report(DefaultMessages::$messages['pattern'], 'pattern');
        };

        return $this;
    }
}