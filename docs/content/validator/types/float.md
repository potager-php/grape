---
outline: deep
---

# Float type

Ensure the field's value is a float. This validator specifically validates and converts values to floats.

```php
use Grape;

$validator = Grape::schema([
    "price" => Grape::float(),
]);
```

::: info 🔗 Extends Number Validator
The **Float validator** extends the **[Number validator](./number.md)**, sharing all its validation rules, transformations, and error messages. The key difference is that `Float` **guarantees a float output**, while `Number` can return either an `int` or `float` depending on the input value.
:::

By default, the float validator operates in loose mode, accepting numeric strings and automatically casting them to floats. This ensures that subsequent validations receive a proper float and the final sanitized output maintains the float type you expect.

You can enable strict mode using the first argument of the `float()` method to only accept native float values:

::: code-group

```php [Loose Mode]
use Grape;

$validator = Grape::schema([
    "price" => Grape::float(),
]);

// ✅ 12.34
// ✅ 123 -> cast to 123.0
// ✅ "12.34" -> cast to 12.34
// ✅ "123" -> cast to 123.0
// ❌ "abc" (not numeric)
// ❌ [1, 2] (not numeric)

```

```php [Strict Mode]
use Grape;

$validator = Grape::schema([
    "price" => Grape::float(strict: true),
]);

// ✅ 12.34
// ❌ 123 (not a float)
// ❌ "12.34" (not a float)
// ❌ "123" (not a float)
// ❌ "abc" (not numeric)
```

:::

You can use the following modifiers to mark the float as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "price" => Grape::float()->required(),
    "discount" => Grape::float()->nullable(),
]);
```

## Error Messages

Here are the default error messages for the float type sorted by rule:

```php
[
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
]
```

## Validations

Following are the built-in validations available for the float type.

### `min`

Enforce the float to be greater than or equal to a minimum value.

```php
$validator = Grape::schema([
    "price" => Grape::float()->min(0.01),
    "temperature" => Grape::float()->min(-273.15),
]);
```

### `max`

Enforce the float to be less than or equal to a maximum value.

```php
$validator = Grape::schema([
    "percentage" => Grape::float()->max(100.0),
    "discount" => Grape::float()->max(50.5),
]);
```

### `range`

Enforce the float to be within a specific range (inclusive).

```php
$validator = Grape::schema([
    "percentage" => Grape::float()->range(0.0, 100.0),
    "temperature" => Grape::float()->range(-273.15, 1000.0),
]);
```

### `positive`

Enforce the float to be positive (greater than or equal to 0).

```php
$validator = Grape::schema([
    "price" => Grape::float()->positive(),
    "weight" => Grape::float()->positive(),
]);
```

### `negative`

Enforce the float to be negative (less than 0).

```php
$validator = Grape::schema([
    "debt" => Grape::float()->negative(),
    "loss" => Grape::float()->negative(),
]);
```

### `zero`

Enforce the float to be exactly zero.

```php
$validator = Grape::schema([
    "balance" => Grape::float()->zero(),
    "offset" => Grape::float()->zero(),
]);
```

### `nonZero`

Enforce the float to not be zero.

```php
$validator = Grape::schema([
    "divisor" => Grape::float()->nonZero(),
    "coefficient" => Grape::float()->nonZero(),
]);
```

### `odd`

Enforce the float to be odd (must be a whole number).

```php
$validator = Grape::schema([
    "odd_value" => Grape::float()->odd(),
]);
```

### `even`

Enforce the float to be even (must be a whole number).

```php
$validator = Grape::schema([
    "even_value" => Grape::float()->even(),
]);
```

### `multipleOf`

Enforce the float to be a multiple of a specific factor.

```php
$validator = Grape::schema([
    "step_size" => Grape::float()->multipleOf(0.25),
    "price_increment" => Grape::float()->multipleOf(0.01),
]);
```

### `NaN`

Enforce the float to be NaN (Not a Number).

::: info 💡 Why NaN is Float-Specific
**NaN (Not a Number)** is a special IEEE 754 floating-point value that represents an undefined or unrepresentable mathematical result. In PHP, NaN only exists as a float type and is typically the result of invalid mathematical operations like `sqrt(-1)`, `0/0`, or `log(-1)`. Since NaN is inherently a floating-point concept, this validation is only available in the Float validator.

Common scenarios that produce NaN:

-   Mathematical errors: `sqrt(-1)`, `acos(2)`, `log(-1)`
-   Indeterminate forms: `0/0`, `INF - INF`, `INF / INF`
-   Operations with existing NaN values: `NaN + 5`, `NaN * 2`

**Important:** NaN has unique comparison behavior - `NaN !== NaN` in PHP, so you cannot use regular equality checks to detect it. Use `is_nan()` function instead.
:::

```php
$validator = Grape::schema([
    "invalid_calculation" => Grape::float()->NaN(),
    "math_error_result" => Grape::float()->NaN(),
]);

// Examples that would pass NaN validation:
// sqrt(-1) -> NaN
// 0/0 -> NaN
// acos(2) -> NaN
```

### `notNaN`

Enforce the float to not be NaN (Not a Number).

This validation is commonly used to ensure that mathematical calculations have produced valid results and haven't resulted in undefined operations. It's particularly useful when validating the output of complex mathematical computations where NaN could indicate an error condition.

```php
$validator = Grape::schema([
    "valid_number" => Grape::float()->notNaN(),
    "calculation_result" => Grape::float()->notNaN(),
    "user_input" => Grape::float()->notNaN(), // Ensure user didn't somehow input NaN
]);
```

### `withoutDecimal`

Enforce the float to have no decimal places (be a whole number).

```php
$validator = Grape::schema([
    "whole_number" => Grape::float()->withoutDecimal(),
]);
```

## Transformations

The following methods transform the float value during validation.

### `abs`

Convert the float to its absolute value.

```php
$validator = Grape::schema([
    "distance" => Grape::float()->abs(),
    "magnitude" => Grape::float()->abs(),
]);
```

### `clamp`

Clamp the float to be within a specific range, adjusting values that fall outside the bounds.

```php
$validator = Grape::schema([
    "percentage" => Grape::float()->clamp(0.0, 100.0),
    "opacity" => Grape::float()->clamp(0.0, 1.0),
]);
```

### `round`

Round the float to a specified precision, but still returning a float.

**Parameters:**

-   `precision` (int, default: `0`) - Number of decimal places to round to
-   `mode` (int, default: `PHP_ROUND_HALF_UP`) - Rounding mode constant

**Rounding modes:** `PHP_ROUND_HALF_UP`, `PHP_ROUND_HALF_DOWN`, `PHP_ROUND_HALF_EVEN`, `PHP_ROUND_HALF_ODD`

```php
$validator = Grape::schema([
    "price" => Grape::float()->round(2),
    "percentage" => Grape::float()->round(1, PHP_ROUND_HALF_EVEN),
    "whole_number" => Grape::float()->round(0),
]);
```

### `floor`

Round the float down to the nearest integer, but still returning a float.

```php
$validator = Grape::schema([
    "floored_value" => Grape::float()->floor(),
]);
```
