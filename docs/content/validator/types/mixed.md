---
outline: deep
---

# Mixed type

Accepts any value without type restrictions. The mixed validator is a pass-through validator that accepts values of any type (strings, integers, floats, booleans, arrays, objects, null, etc.) without performing any type validation.

```php
use Grape;

$validator = Grape::schema([
    "data" => Grape::mixed(),
]);
```

This validator is useful when you just need to ensure a field exists without enforcing a specific type constraint.

You can use the following modifiers to mark the mixed field as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "data" => Grape::mixed()->required(),
    "optional_data" => Grape::mixed()->nullable(),
]);
```
