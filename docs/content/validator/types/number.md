---
outline: deep
---

# Number type

Ensure the field's value is a number (integer or float). This validator accepts both integers and floats as valid numeric values.

```php
use Grape;

$validator = Grape::schema([
    "price" => Grape::number(),
]);
```

::: info 📝 Number vs Specific Types
**Number is not a native PHP type** - it's a convenient abstraction that accepts both integers and floats, making validation more permissive when you don't need to enforce a specific numeric type.

For stricter type requirements, consider using:

-   **[Integer validator](./integer.md)** - Guarantees integer output and provides integer-specific validations
-   **[Float validator](./float.md)** - Guarantees float output and includes float-specific features like NaN handling

Use Number when you want flexibility, and use Integer/Float when you need precise type control.
:::

By default, the number validator operates in loose mode, accepting numeric strings and automatically casting them to the appropriate numeric type (integer or float). This ensures that subsequent validations receive a proper number and the final sanitized output maintains the numeric type you expect.

You can enable strict mode using the first argument of the `number()` method to only accept native integer and float values:

::: code-group

```php [Loose Mode]
use Grape;

$validator = Grape::schema([
    "price" => Grape::number(),
]);

// ✅ 123
// ✅ 12.34
// ✅ "123" -> cast to 123
// ✅ "12.34" -> cast to 12.34
// ❌ "abc" (not numeric)
// ❌ [1, 2] (not numeric)

```

```php [Strict Mode]
use Grape;

$validator = Grape::schema([
    "price" => Grape::number(strict: true),
]);

// ✅ 123
// ✅ 12.34
// ❌ "123" (not a number)
// ❌ "12.34" (not a number)
// ❌ "abc" (not a number)
// ❌ [1, 2] (not a number)
```

:::

You can use the following modifiers to mark the number as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "price" => Grape::number()->required(),
    "discount" => Grape::number()->nullable(),
]);
```

## Error Messages

Here are the default error messages for the number type sorted by rule:

```php
[
    "number" => "The {field} must be a number.",
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
]
```

## Validations

Following are the built-in validations available for the number type.

### `min`

Enforce the number to be greater than or equal to a minimum value.

```php
$validator = Grape::schema([
    "age" => Grape::number()->min(18),
    "price" => Grape::number()->min(0.01),
]);
```

### `max`

Enforce the number to be less than or equal to a maximum value.

```php
$validator = Grape::schema([
    "percentage" => Grape::number()->max(100),
    "discount" => Grape::number()->max(50.5),
]);
```

### `range`

Enforce the number to be within a specific range (inclusive).

```php
$validator = Grape::schema([
    "percentage" => Grape::number()->range(0, 100),
    "temperature" => Grape::number()->range(-273.15, 1000),
]);
```

### `positive`

Enforce the number to be positive (greater than or equal to 0).

```php
$validator = Grape::schema([
    "price" => Grape::number()->positive(),
    "quantity" => Grape::number()->positive(),
]);
```

### `negative`

Enforce the number to be negative (less than 0).

```php
$validator = Grape::schema([
    "debt" => Grape::number()->negative(),
    "loss" => Grape::number()->negative(),
]);
```

### `zero`

Enforce the number to be exactly zero.

```php
$validator = Grape::schema([
    "balance" => Grape::number()->zero(),
    "offset" => Grape::number()->zero(),
]);
```

### `nonZero`

Enforce the number to not be zero.

```php
$validator = Grape::schema([
    "divisor" => Grape::number()->nonZero(),
    "coefficient" => Grape::number()->nonZero(),
]);
```

### `odd`

Enforce the number to be odd.

```php
$validator = Grape::schema([
    "odd_number" => Grape::number()->odd(),
]);
```

### `even`

Enforce the number to be even.

```php
$validator = Grape::schema([
    "even_number" => Grape::number()->even(),
]);
```

### `multipleOf`

Enforce the number to be a multiple of a specific factor.

```php
$validator = Grape::schema([
    "step_size" => Grape::number()->multipleOf(5),
    "decimal_step" => Grape::number()->multipleOf(0.25),
]);
```

## Transformations

The following methods transform the number value during validation.

### `abs`

Convert the number to its absolute value.

```php
$validator = Grape::schema([
    "distance" => Grape::number()->abs(),
    "magnitude" => Grape::number()->abs(),
]);
```

### `clamp`

Clamp the number to be within a specific range, adjusting values that fall outside the bounds.

```php
$validator = Grape::schema([
    "percentage" => Grape::number()->clamp(0, 100),
    "volume" => Grape::number()->clamp(0, 10),
]);
```
