---
outline: deep
---

# Boolean type

Ensure the field's value is a boolean. This validator provides comprehensive boolean validation with support for both strict and loose modes, along with customizable boolean-like values.

```php
use Grape;

$validator = Grape::schema([
    "active" => Grape::boolean(),
]);
```

By default, the boolean validator operates in loose mode, accepting various scalar values that can be interpreted as boolean (strings like "true", "1", numbers like 1, 0, etc.) and automatically casting them to proper boolean values. This ensures that subsequent validations receive a proper boolean and the final sanitized output maintains the boolean type you expect.

You can enable strict mode using the first argument of the `boolean()` method to only accept native boolean values:

::: code-group

```php [Loose Mode]
use Grape;

$validator = Grape::schema([
    "active" => Grape::boolean(),
]);

// ✅ true
// ✅ false
// ✅ "true" -> cast to true
// ✅ "false" -> cast to false
// ✅ "1" -> cast to true
// ✅ "0" -> cast to false
// ✅ 1 -> cast to true
// ✅ 0 -> cast to false
// ✅ "yes" -> cast to true
// ✅ "no" -> cast to false
// ❌ "maybe" (not a boolean-like value)
// ❌ [1, 2] (not a scalar)

```

```php [Strict Mode]
use Grape;

$validator = Grape::schema([
    "active" => Grape::boolean(strict: true),
]);

// ✅ true
// ✅ false
// ❌ "true" (not a boolean)
// ❌ "1" (not a boolean)
// ❌ 1 (not a boolean)
// ❌ "yes" (not a boolean)
```

:::

You can use the following modifiers to mark the boolean as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "newsletter" => Grape::boolean()->nullable(),
]);
```

## Error Messages

Here are the default error messages for the boolean type sorted by rule:

```php
[
    "boolean" => "The {field} must be a boolean.",
    "true" => "The {field} must be true.",
    "false" => "The {field} must be false.",
]
```

## Validations

Following are the built-in validations available for the boolean type.

### `true`

Enforce the boolean value to be exactly `true`.

```php
$validator = Grape::schema([
    "terms_accepted" => Grape::boolean()->true(),
]);
```

### `false`

Enforce the boolean value to be exactly `false`.

```php
$validator = Grape::schema([
    "disabled" => Grape::boolean()->false(),
]);
```

## Boolean-like Values

Grape provides a flexible system for defining which values should be considered "truthy" or "falsy" beyond PHP's default boolean evaluation. This is particularly useful when working with form data, configuration values, and user input where various strings and numbers should be interpreted as boolean values.

### Default Values

By default, Grape recognizes these values as boolean-like:

**Truthy values** (interpreted as `true`):

-   `true` (boolean)
-   `"true"` (string)
-   `"1"` (string)
-   `1` (integer)
-   `"on"` (string)
-   `"yes"` (string)
-   `"y"` (string)
-   `"enable"` (string)

**Falsy values** (interpreted as `false`):

-   `false` (boolean)
-   `"false"` (string)
-   `"0"` (string)
-   `0` (integer)
-   `"off"` (string)
-   `"no"` (string)
-   `"n"` (string)
-   `"disable"` (string)

### Customizing Boolean-like Values

You can customize which values are considered truthy or falsy using the static methods on the `Grape` class:

#### Adding Values

```php
use Grape;

// Add custom truthy values
Grape::addTruthy(['oui', 'si', 'enabled', 'active']);

// Add custom falsy values
Grape::addFalsy(['non', 'nein', 'disabled', 'inactive']);

$validator = Grape::boolean();
$result = $validator->validate('oui'); // true
$result = $validator->validate('non'); // false
```

#### Replacing Values

```php
use Grape;

// Replace all truthy values with custom ones
Grape::setTruthy(['confirmed', 'approved', 'accepted']);

// Replace all falsy values with custom ones
Grape::setFalsy(['denied', 'rejected', 'cancelled']);
```

#### Removing Values

```php
use Grape;

// Remove specific values from truthy list
Grape::removeTruthy(['y', 'enable']);

// Remove specific values from falsy list
Grape::removeFalsy(['n', 'disable']);
```

#### Getting Current Values

```php
use Grape;

// Get all current truthy values
$truthyValues = Grape::getTruthy();

// Get all current falsy values
$falsyValues = Grape::getFalsy();
```

#### Resetting to Defaults

```php
use Grape;

// Reset both truthy and falsy values to their defaults
Grape::resetBooleanValues();
```
