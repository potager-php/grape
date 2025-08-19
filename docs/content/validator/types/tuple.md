---
outline: deep
---

# Tuple type

Ensure the field's value is a fixed-size array with specific types for each index position. Unlike collections where all items have the same type, tuples allow you to define different validators for each position in the array.

```php
use Grape;

$validator = Grape::schema([
    "coordinates" => Grape::tuple([
        Grape::float(), // x coordinate
        Grape::float(), // y coordinate
    ]),
]);
```

```php
use Grape;

$validator = Grape::schema([
    "person_info" => Grape::tuple([
        Grape::string(), // name
        Grape::integer(), // age
        Grape::boolean(), // is_active
    ]),
]);
```

Tuples are particularly useful when you need to validate arrays with a known structure where each position has a specific meaning and type, such as coordinates, RGB values, or database records with fixed column types.

You can use the following modifiers to mark the tuple as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "coordinates" => Grape::tuple([Grape::float(), Grape::float()])->required(),
    "optional_data" => Grape::tuple([Grape::string(), Grape::integer()])->nullable(),
]);
```

## Creating Complex Tuples

Since Grape tuples accept any valid Grape type, you can create tuples containing schemas, collections, or even nested tuples, allowing you to build complex nested structures:

```php
use Grape;

$validator = Grape::schema([
    "complex_data" => Grape::tuple([
        Grape::string(), // identifier
        Grape::collection(Grape::integer()), // list of numbers
        Grape::schema([
            "name" => Grape::string()->required(),
            "email" => Grape::string()->email()->required(),
        ]), // nested object
        Grape::tuple([Grape::float(), Grape::float()]), // nested tuple for coordinates
    ]),
]);
```

## Handling Unknown Items

By default, tuples discard any items beyond the defined validators. However, you can configure different strategies for handling unknown items:

### Discard Unknown Items (Default)

This is the default behavior where any items beyond the defined validators are ignored, and unless you've configured another default behavior globally, you don't need to specify it explicitly, but you can for clarity:

```php
$validator = Grape::schema([
    "data" => Grape::tuple([Grape::string(), Grape::integer()])->discardUnknownItems(),
]);

// Input: ["hello", 42, "extra", 99]
// Output: ["hello", 42]
```

### Allow Unknown Items

Keep all items, including those beyond the defined validators:

```php
$validator = Grape::schema([
    "data" => Grape::tuple([Grape::string(), Grape::integer()])->allowUnknownItems(),
]);

// Input: ["hello", 42, "extra", 99]
// Output: ["hello", 42, "extra", 99]
```

### Reject Unknown Items

Throw a validation error if there are items beyond the defined validators:

```php
$validator = Grape::schema([
    "data" => Grape::tuple([Grape::string(), Grape::integer()])->rejectUnknownItems(),
]);

// Input: ["hello", 42, "extra"]
// Throws ValidationException
```

## Error Messages

Here are the default error messages for the tuple type:

```php
[
    "tuple" => "The {field} must be a tuple with at least {length} items.",
    "tupleDistinct" => "The {field} must contain unique items.",
    "unknownItem" => "The {field} has an unknown item at index {index}.",
]
```

## Validations

Following are the validations that can be applied to the tuple.

### `distinct`

Ensure all items in the tuple are unique (no duplicates).

```php
$validator = Grape::schema([
    "values" => Grape::tuple([Grape::string(), Grape::string(), Grape::string()])->distinct(),
]);
```

You can provide a custom resolver function to specify how uniqueness should be determined. The resolver receives the item value and should return a value that uniquely identifies the item:

```php
$validator = Grape::schema([
    "names" => Grape::tuple([
        Grape::string(),
        Grape::string()
    ])->distinct(fn($value) => strtolower($value)),
]);

// This will pass: ["Hello", "WORLD"]
// This will fail: ["Hello", "hello"]
```
