---
outline: deep
---

# Literal type

Ensure the field's value matches exactly a specified literal value. The literal can be a static value or a callable that returns the expected value based on the validation context.

```php
use Grape;

$validator = Grape::schema([
    "api_version" => Grape::literal('v1.0'),
]);
```

The literal validator uses strict comparison (`===`), meaning no type coercion occurs. This makes it perfect for exact value matching like API versions, enum values, or security tokens.

```php
use Grape;

$validator = Grape::schema([
    "status" => Grape::literal('published'),
]);

// ✅ 'published'
// ❌ 'draft' (not exact match)
// ❌ 'Published' (case sensitive)
```

You can use the following modifiers to mark the literal as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "api_version" => Grape::literal('v1.0')->required(),
    "optional_token" => Grape::literal('secret')->nullable(),
]);
```

## Error Messages

Here are the default error messages for the literal type:

```php
[
    "literal" => "The {field} must be equal to {expected}.",
]
```

## Dynamic Literals

You can use callable literals for context-aware validation:

```php
$validator = Grape::schema([
    "status" => Grape::literal(function (FieldContext $ctx) {
        $data = $ctx->getParent()->getValue();
        return $data['user_role'] === 'admin' ? 'approved' : 'pending';
    }),
]);
```
