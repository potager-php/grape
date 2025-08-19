---
outline: deep
---

# Accepted type

Ensure the field's value is considered "accepted" - typically used for checkboxes, terms of service, privacy policy confirmations, and other consent mechanisms where you need to ensure the user has explicitly agreed to something.

```php
use Grape;

$validator = Grape::schema([
    "terms_accepted" => Grape::accepted(),
]);
```

The accepted validator is specifically designed for confirmation fields where only certain values should be considered as "acceptance". Unlike boolean validation which can accept many different truthy/falsy values, the accepted validator has a strict list of values that are considered valid acceptance.

This is particularly useful for legal compliance scenarios where you need to ensure explicit consent has been given.

## Accepted Values

The accepted validator only considers these specific values as valid:

-   `true` (boolean)
-   `1` (integer)
-   `"true"` (string)
-   `"1"` (string)
-   `"on"` (string)

All other values, including `false`, `0`, `"false"`, `"0"`, `"off"`, `null`, empty strings, and any other values will be rejected.

::: code-group

```php [Valid Values]
use Grape;

$validator = Grape::accepted();

// ✅ All of these will pass validation
$validator->validate(true);     // boolean true
$validator->validate(1);        // integer 1
$validator->validate("true");   // string "true"
$validator->validate("1");      // string "1"
$validator->validate("on");     // string "on"
```

```php [Invalid Values]
use Grape;

$validator = Grape::accepted();

// ❌ All of these will fail validation
$validator->validate(false);    // boolean false
$validator->validate(0);        // integer 0
$validator->validate("false");  // string "false"
$validator->validate("0");      // string "0"
$validator->validate("off");    // string "off"
$validator->validate(null);     // null
$validator->validate("");       // empty string
$validator->validate("yes");    // any other string
$validator->validate([]);       // arrays
$validator->validate(2);        // other numbers
```

:::

You can use the following modifiers to mark the accepted field as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "terms_accepted" => Grape::accepted()->required(),
    "newsletter_signup" => Grape::accepted()->nullable(),
]);
```

## Error Messages

Here are the default error messages for the accepted type:

```php
[
    "accepted" => "The {field} must be accepted.",
]
```
