---
outline: deep
---

# Integer type

Ensure the field's value is an integer. This validator specifically validates and converts values to integers.

```php
use Grape;

$validator = Grape::schema([
    "age" => Grape::integer(),
]);
```

::: info 🔗 Extends Number Validator
The **Integer validator** extends the **[Number validator](./number.md)**, sharing all its validation rules, transformations, and error messages. The key difference is that `Integer` **guarantees an integer output**, while `Number` can return either an `int` or `float` depending on the input value.
:::

By default, the integer validator operates in loose mode, accepting numeric strings and automatically casting them to integers. This ensures that subsequent validations receive a proper integer and the final sanitized output maintains the integer type you expect.

You can enable strict mode using the first argument of the `integer()` method to only accept native integer values:

::: code-group

```php [Loose Mode]
use Grape;

$validator = Grape::schema([
    "age" => Grape::integer(),
]);

// ✅ 123
// ✅ "123" -> cast to 123
// ✅ "123.0" -> cast to 123
// ✅ 123.0 -> cast to 123
// ❌ "123.5" (not an integer)
// ❌ 123.5 (not an integer)
// ❌ "abc" (not numeric)

```

```php [Strict Mode]
use Grape;

$validator = Grape::schema([
    "age" => Grape::integer(strict: true),
]);

// ✅ 123
// ❌ "123" (not an integer)
// ❌ "123.0" (not an integer)
// ❌ 123.0 (not an integer)
// ❌ "abc" (not numeric)
```

:::

You can use the following modifiers to mark the integer as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "age" => Grape::integer()->required(),
    "score" => Grape::integer()->nullable(),
]);
```

## Error Messages

Here are the default error messages for the integer type sorted by rule:

```php
[
    "integer" => "The {field} must be an integer.",
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

Following are the built-in validations available for the integer type.

### `min`

Enforce the integer to be greater than or equal to a minimum value.

```php
$validator = Grape::schema([
    "age" => Grape::integer()->min(18),
    "quantity" => Grape::integer()->min(1),
]);
```

### `max`

Enforce the integer to be less than or equal to a maximum value.

```php
$validator = Grape::schema([
    "percentage" => Grape::integer()->max(100),
    "retries" => Grape::integer()->max(3),
]);
```

### `range`

Enforce the integer to be within a specific range (inclusive).

```php
$validator = Grape::schema([
    "percentage" => Grape::integer()->range(0, 100),
    "rating" => Grape::integer()->range(1, 5),
]);
```

### `positive`

Enforce the integer to be positive (greater than or equal to 0).

```php
$validator = Grape::schema([
    "quantity" => Grape::integer()->positive(),
    "score" => Grape::integer()->positive(),
]);
```

### `negative`

Enforce the integer to be negative (less than 0).

```php
$validator = Grape::schema([
    "debt" => Grape::integer()->negative(),
    "offset" => Grape::integer()->negative(),
]);
```

### `zero`

Enforce the integer to be exactly zero.

```php
$validator = Grape::schema([
    "balance" => Grape::integer()->zero(),
    "reset_value" => Grape::integer()->zero(),
]);
```

### `nonZero`

Enforce the integer to not be zero.

```php
$validator = Grape::schema([
    "divisor" => Grape::integer()->nonZero(),
    "multiplier" => Grape::integer()->nonZero(),
]);
```

### `odd`

Enforce the integer to be odd.

```php
$validator = Grape::schema([
    "odd_number" => Grape::integer()->odd(),
    "page_number" => Grape::integer()->odd(),
]);
```

### `even`

Enforce the integer to be even.

```php
$validator = Grape::schema([
    "even_number" => Grape::integer()->even(),
    "pair_count" => Grape::integer()->even(),
]);
```

### `multipleOf`

Enforce the integer to be a multiple of a specific factor.

```php
$validator = Grape::schema([
    "step_size" => Grape::integer()->multipleOf(5),
    "batch_size" => Grape::integer()->multipleOf(10),
]);
```

## Transformations

The following methods transform the integer value during validation.

### `abs`

Convert the integer to its absolute value.

```php
$validator = Grape::schema([
    "distance" => Grape::integer()->abs(),
    "count" => Grape::integer()->abs(),
]);
```

### `clamp`

Clamp the integer to be within a specific range, adjusting values that fall outside the bounds.

```php
$validator = Grape::schema([
    "percentage" => Grape::integer()->clamp(0, 100),
    "priority" => Grape::integer()->clamp(1, 10),
]);
```
