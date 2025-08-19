---
outline: deep
---

# Schema type

Ensure the field's value is an object (associative array) that matches a specific structure. Schema validators allow you to define validation rules for multiple fields within an object, where each field can have its own validator and rules.

```php
use Grape;

$validator = Grape::schema([
    "name" => Grape::string(),
    "age" => Grape::integer(),
    "email" => Grape::string()->email(),
]);
```

```php
use Grape;

$validator = Grape::schema([
    "user" => Grape::schema([
        "name" => Grape::string()->required(),
        "age" => Grape::integer()->min(18),
    ]),
    "active" => Grape::boolean(),
]);
```

Schema validators are ideal for validating structured data like API payloads, configuration objects, user profiles, or any nested data structure where you need to validate multiple fields with different rules.

You can use the following modifiers to mark the schema as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "user_data" => Grape::schema([
        "name" => Grape::string(),
        "email" => Grape::string()->email(),
    ])->required(),
    "optional_metadata" => Grape::schema([
        "tags" => Grape::collection(Grape::string()),
    ])->nullable(),
]);
```

## Field Validation

Each field in the schema is validated according to its assigned validator. Fields can be marked as required or optional, and can have their own validation rules:

```php
use Grape;

$validator = Grape::schema([
    "name" => Grape::string()->required()->minLength(2),
    "age" => Grape::integer()->min(0)->max(150),
    "email" => Grape::string()->email()->required(),
    "bio" => Grape::string()->maxLength(500), // Optional field
]);
```

## Handling Unknown Properties

By default, schemas discard any properties that are not defined in the schema. However, you can configure different strategies for handling unknown properties:

### Discard Unknown Properties (Default)

```php
$validator = Grape::schema([
    "name" => Grape::string(),
    "age" => Grape::integer(),
]);

// Input: {"name": "John", "age": 30, "extra": "data"}
// Output: {"name": "John", "age": 30}
```

### Allow Unknown Properties

Keep all properties, including those not defined in the schema:

```php
$validator = Grape::schema([
    "name" => Grape::string(),
    "age" => Grape::integer(),
])->allowUnknownProperties();

// Input: {"name": "John", "age": 30, "extra": "data"}
// Output: {"name": "John", "age": 30, "extra": "data"}
```

### Reject Unknown Properties

Throw a validation error if there are properties not defined in the schema:

```php
$validator = Grape::schema([
    "name" => Grape::string(),
    "age" => Grape::integer(),
])->rejectUnknownProperties();

// Input: {"name": "John", "age": 30, "extra": "data"}
// Throws ValidationException
```

## Nested Schemas

Schemas can contain other schemas, allowing you to validate complex nested structures:

```php
use Grape;

$validator = Grape::schema([
    "user" => Grape::schema([
        "profile" => Grape::schema([
            "name" => Grape::string()->required(),
            "email" => Grape::string()->email()->required(),
            "avatar" => Grape::string()->url(),
        ]),
        "settings" => Grape::schema([
            "notifications" => Grape::boolean(),
            "theme" => Grape::string(),
        ]),
    ]),
    "created_at" => Grape::string()->required(),
]);
```

## Complex Field Types

Since Grape schemas accept any valid Grape validator, you can use collections, tuples, and other complex types as field validators:

```php
use Grape;

$validator = Grape::schema([
    "basic_info" => Grape::schema([
        "name" => Grape::string()->required(),
        "age" => Grape::integer()->min(0),
    ]),
    "tags" => Grape::collection(Grape::string()), // Array of strings
    "coordinates" => Grape::tuple([               // Fixed-size array
        Grape::float(), // latitude
        Grape::float(), // longitude
    ]),
    "metadata" => Grape::schema([                 // Nested object
        "version" => Grape::string(),
        "flags" => Grape::collection(Grape::boolean()),
    ])->nullable(),
]);
```

## Error Messages

Here are the default error messages for the schema type:

```php
[
    "schema" => "The {field} must be an array.",
    "required" => "The {field} is required.",
    "unknown" => "The {field} is unknown.",
]
```
