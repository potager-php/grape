---
outline: deep
---

# Null type

Ensure the field's value is null. This validator is useful for enforcing that certain fields must be explicitly null.

```php
use Grape;

$validator = Grape::schema([
    "deleted_at" => Grape::null(),
]);
```

The null validator strictly enforces that a value must be `null`. This is different from simply being nullable - it requires the value to be explicitly null rather than just allowing null values.

You can use the following modifiers to mark the null field as required. Note that `nullable()` is not applicable since this validator inherently expects null values. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "deleted_at" => Grape::null()->required(),
]);
```

## Error Messages

Here are the default error messages for the null type:

```php
[
    "null" => "The {field} must be null.",
]
```
