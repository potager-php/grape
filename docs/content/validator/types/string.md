---
outline: deep
---

# String type

Ensure the field's value is a string. Empty strings are considered valid, so you may want to handle them using `notEmpty()`.

```php
use Grape;

$validator = Grape::schema([
    "name" => Grape::string(),
]);
```

By default, the string validator operates in loose mode, accepting any scalar value (integers, floats, booleans) and automatically casting them to strings. This ensures that subsequent validations receive a proper string and the final sanitized output maintains the string type you expect.

You can enable strict mode using the first argument of the `string()` method to only accept native string values:

::: code-group

```php [Loose Mode]
use Grape;

$validator = Grape::schema([
    "name" => Grape::string(),
]);

// ✅ "hello"
// ✅ 123 -> cast to "123"
// ✅ 12.34 -> cast to "12.34"
// ✅ true -> cast to "true"
// ❌ [1, 2] (not a scalar)

```

```php [Strict Mode]
use Grape;

$validator = Grape::schema([
    "name" => Grape::string(strict: true),
]);

// ✅ "hello"
// ❌ 123 (not a string)
// ❌ 12.34 (not a string)
// ❌ true (not a string)
// ❌ [1, 2] (not a string)
```

:::

You can use the following modifiers to mark the string as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "name" => Grape::string()->required(),
    "email" => Grape::string()->nullable(),
]);
```

## Error Messages

Here are the default error messages for the string type sorted by rule:

```php
[
    "string" => "The {field} must be a string.",
    "email" => "The {field} must be a valid email address.",
    "mobile" => "The {field} must be a valid mobile phone number.",
    "creditCard" => "The {field} must be a valid {providersList} card number.",
    "pattern" => "The {field} format is invalid.",
    "url" => "The {field} must be a valid URL.",
    "activeUrl" => "The {field} must be a valid active URL.",
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
    "noWhitespace" => "The {field} must not contain whitespace.",
    "json" => "The {field} must be valid JSON.",
    "empty" => "The {field} must be empty.",
    "notEmpty" => "The {field} must not be empty."
]
```

## Validations

Following are the built-in validations available for the string type.

### `minLength`

Enforce the string to have a minimum pre-defined length.

```php
$validator = Grape::schema([
    "username" => Grape::string()->minLength(3),
]);
```

### `maxLength`

Enforce the string to have a maximum pre-defined length.

```php
$validator = Grape::schema([
    "username" => Grape::string()->maxLength(20),
]);
```

### `fixedLength`

Enforce the string to have an exact pre-defined length.

```php
$validator = Grape::schema([
    "code" => Grape::string()->fixedLength(6),
]);
```

### `prefix`

Enforce the string to start with a specific prefix.

```php
$validator = Grape::schema([
    "username" => Grape::string()->prefix("user_"),
]);
```

### `suffix`

Enforce the string to end with a specific suffix.

```php
$validator = Grape::schema([
    "filename" => Grape::string()->suffix(".txt"),
]);
```

### `contains`

Enforce the string to contain a specific substring.

```php
$validator = Grape::schema([
    "description" => Grape::string()->contains("important"),
]);
```

### `alphabetic`

Enforce the string to contain only alphabetic characters (a-z, A-Z).

**Parameters:**

-   `allowWhitespaces` (bool, default: `true`) - Allow whitespace characters (spaces, tabs, newlines)
-   `allowDashes` (bool, default: `false`) - Allow dash/hyphen characters (-)
-   `allowUnderscores` (bool, default: `false`) - Allow underscore characters (\_)

```php
$validator = Grape::schema([
    "name" => Grape::string()->alphabetic(),
    "name_with_spaces" => Grape::string()->alphabetic(allowWhitespaces: true),
    "name_no_spaces" => Grape::string()->alphabetic(allowWhitespaces: false),
    "name_with_dashes" => Grape::string()->alphabetic(allowWhitespaces: false, allowDashes: true),
    "name_with_underscores" => Grape::string()->alphabetic(allowWhitespaces: false, allowUnderscores: true),
    "slug_format" => Grape::string()->alphabetic(allowWhitespaces: false, allowDashes: true, allowUnderscores: true),
]);
```

### `numeric`

Enforce the string to contain only numeric characters (0-9).

**Parameters:**

-   `allowWhitespaces` (bool, default: `true`) - Allow whitespace characters (spaces, tabs, newlines)
-   `allowDashes` (bool, default: `false`) - Allow dash/hyphen characters (-)
-   `allowUnderscores` (bool, default: `false`) - Allow underscore characters (\_)

```php
$validator = Grape::schema([
    "code" => Grape::string()->numeric(),
    "code_with_spaces" => Grape::string()->numeric(allowWhitespaces: true),
    "code_no_spaces" => Grape::string()->numeric(allowWhitespaces: false),
    "code_with_dashes" => Grape::string()->numeric(allowWhitespaces: false, allowDashes: true),
]);
```

### `alphanumeric`

Enforce the string to contain only alphanumeric characters (a-z, A-Z, 0-9).

**Parameters:**

-   `allowWhitespaces` (bool, default: `true`) - Allow whitespace characters (spaces, tabs, newlines)
-   `allowDashes` (bool, default: `false`) - Allow dash/hyphen characters (-)
-   `allowUnderscores` (bool, default: `false`) - Allow underscore characters (\_)

```php
$validator = Grape::schema([
    "username" => Grape::string()->alphanumeric(),
    "display_name" => Grape::string()->alphanumeric(allowWhitespaces: true),
    "slug" => Grape::string()->alphanumeric(allowWhitespaces: false, allowDashes: true, allowUnderscores: true),
]);
```

### `noWhitespace`

Enforce the string to contain no whitespace characters.

```php
$validator = Grape::schema([
    "token" => Grape::string()->noWhitespace(),
]);
```

### `email`

Enforce the string to be a valid email address.

```php
$validator = Grape::schema([
    "email" => Grape::string()->email(),
]);
```

### `phone`

Enforce the string to be a valid mobile phone number.

```php
$validator = Grape::schema([
    "phone" => Grape::string()->phone(),
]);
```

### `json`

Enforce the string to be valid JSON.

```php
$validator = Grape::schema([
    "config" => Grape::string()->json(),
]);
```

### `url`

Enforce the string to be a valid URL.

```php
$validator = Grape::schema([
    "website" => Grape::string()->url(),
]);
```

### `activeUrl`

Enforce the string to be an active (reachable) URL.

```php
$validator = Grape::schema([
    "website" => Grape::string()->activeUrl(),
]);
```

### `creditCard`

Enforce the string to be a valid credit card number.

**Parameters:**

-   `providers` (array|null, default: `null`) - Array of specific credit card providers to validate against. If not provided, validates against all supported providers.

**Supported providers:** `amex` (American Express), `bcglobal` (BC Global), `carte_blanche` (Carte Blanche), `diners_club` (Diners Club), `discover` (Discover), `insta_payment` (Insta Payment), `jcb` (JCB), `koreanloca` (Korean Local), `laser` (Laser), `maestro` (Maestro), `mastercard` (Mastercard), `solo` (Solo), `switch` (Switch), `union_pay` (Union Pay), `visa` (Visa), `visa_master` (Visa or Mastercard)

```php
$validator = Grape::schema([
    "card_number" => Grape::string()->creditCard(),
    "visa_card" => Grape::string()->creditCard(['visa']),
    "visa_or_mastercard" => Grape::string()->creditCard(['visa', 'mastercard']),
    "amex_only" => Grape::string()->creditCard(['amex']),
]);
```

### `ip`

Enforce the string to be a valid IP address.

**Parameters:**

-   `version` (string|null, default: `null`) - IP version to validate. Use `'4'` for IPv4, `'6'` for IPv6, or `null` to allow both.

```php
$validator = Grape::schema([
    "ip_address" => Grape::string()->ip(),
    "ipv4_address" => Grape::string()->ip('4'),
    "ipv6_address" => Grape::string()->ip('6'),
]);
```

### `empty`

Enforce the string to be empty.

**Parameters:**

-   `ignoreWhitespaces` (bool, default: `true`) - Whether to ignore whitespace when checking emptiness. If `true`, strings containing only whitespace characters (spaces, tabs, newlines) are considered empty.

```php
$validator = Grape::schema([
    "placeholder" => Grape::string()->empty(),
    "truly_empty" => Grape::string()->empty(ignoreWhitespaces: false),
    "whitespace_counts_as_empty" => Grape::string()->empty(ignoreWhitespaces: true),
]);
```

### `notEmpty`

Enforce the string to not be empty.

```php
$validator = Grape::schema([
    "name" => Grape::string()->notEmpty(),
]);
```

### `pattern`

Enforce the string to match a regular expression pattern.

```php
$validator = Grape::schema([
    "postal_code" => Grape::string()->pattern('/^[0-9]{5}$/'),
    "custom_format" => Grape::string()->pattern('/^[A-Z]{2}-[0-9]{4}$/'),
]);
```

## Transformations

The following methods transform the string value during validation.

### `trim`

Remove whitespace from the beginning and end of the string.

```php
$validator = Grape::schema([
    "name" => Grape::string()->trim(),
]);
```

### `lowercase`

Convert the string to lowercase.

```php
$validator = Grape::schema([
    "email" => Grape::string()->lowercase(),
]);
```

### `uppercase`

Convert the string to uppercase.

```php
$validator = Grape::schema([
    "code" => Grape::string()->uppercase(),
]);
```
