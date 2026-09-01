---
outline: deep
---

# Collection type

Ensure the field's value is an array. By default, all array items are considered valid, but you can also pass an item validator to validate each item individually.

```php
use Grape;

$validator = Grape::schema([
    "items" => Grape::collection(),
]);
```

```php
use Grape;

$validator = Grape::schema([
    "items" => Grape::collection(Grape::string()),
]);
```

::: info Why use `collection()` instead of `array()`?
PHP has some reserved keywords that cannot be used as method names, such as `array()`.
To avoid conflicts with these keywords, Grape chose to use `collection()` as the method name for array validation.
This is purely a naming choice and does not affect functionality. It might change in the future if a better solution is found.
:::

You can use the following modifiers to mark the array as required or nullable. <br>
See [Working with required and nullable](../../guide/required-and-nullable.md) for more details.

```php
use Grape;

$validator = Grape::schema([
    "items" => Grape::collection()->required(),
    "optional_items" => Grape::collection()->nullable(),
]);
```

## Creating an Collection of Complex items

Since Grape collections accept any valid Grape type, you can create collections of schemas, or even collections of collections, which allows you to build complex nested structures as shown in the following example:

```php
use Grape;

$validator = Grape::schema([
    "users" => Grape::collection(
        Grape::schema([
            "name" => Grape::string()->required(),
            "email" => Grape::string()->email()->required(),
            "roles" => Grape::collection(Grape::string()),
        ])
    ),
]);
```

## Handling Invalid Items

By default, when any item in a collection fails validation, the entire collection (and consequently the whole dataset) is considered invalid. However, you can configure the validator to skip invalid items using the `skipInvalids()` modifier, which allows the collection to remain valid by including only the items that pass validation in the final output.

```php
$validator = Grape::schema([
    "items" => Grape::collection(Grape::string())->skipInvalids(),
]);
```

The `skipInvalids()` filter is applied early in the validation process, which means that subsequent validation rules like `minLength()`, `maxLength()`, or `distinct()` will only evaluate the valid items that remain after filtering.

### Preventing Empty Collections

To ensure that filtering invalid items doesn't result in an empty collection, combine `skipInvalids()` with the `notEmpty()` rule:

```php
$validator = Grape::schema([
    "items" => Grape::collection(Grape::string())->skipInvalids()->notEmpty(),
]);
```

### Normalizing Array Keys

When invalid items are removed, the resulting array may have inconsistent keys since the original indices of invalid items are not preserved. You can reindex the collection to ensure sequential numeric keys using the `normalize()` mutation:

```php
$validator = Grape::schema([
    "items" => Grape::collection(Grape::string())->skipInvalids()->normalize(),
]);
```

## Validate the keys

Grape collections do not support built-in validation for the keys themselves. However, if you need to perform validation on the keys, we provide the `validateKeys()` API, which allows you to pass a callable method that will be called for each key in the collection. This method receives the key and the `FieldContext` of the item it relies on, allowing you to perform any validation you need.

```php
$validator = Grape::schema([
    "items" => Grape::collection(Grape::string())->validateKeys(function ($key, FieldContext $ctx) {
        if (!is_string($key) || strlen($key) < 3) {
            $ctx->report("Key must be a string with at least 3 characters", 'keyInvalid');
        }
    }),
]);
```

## Mutate the keys

You can also transform the keys of a collection using the `mutateKeys()` API. This method accepts a callable that will be applied to each key in the collection, allowing you to modify the keys as needed. The method receives the key and should return the transformed key. This is useful for normalizing or formatting keys before further processing.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->mutateKeys(function ($key) {
        return strtoupper($key);
    }),
]);
```

::: warning Order is Important
If you use `normalize()` after `mutateKeys()`, the keys will be reindexed to sequential numeric keys, which means the transformations will not be applied to the final output. Use `mutateKeys()` **after** `normalize()` if you want to ensure your transformations are applied to the final output.
:::

## Error Messages

Here are the default error messages for the collection type:

```php
[
    "collection" => "The {field} must be an array.",
    "collectionEmpty" => "The {field} must be empty.",
    "collectionNotEmpty" => "The {field} must not be empty.",
    "collectionMinLength" => "The {field} must have at least {length} items.",
    "collectionMaxLength" => "The {field} must not exceed {length} items.",
    "collectionFixedLength" => "The {field} must have exactly {length} items.",
    "distinct" => "The {field} must have distinct items.",
]
```

## Validations

Following are the validations that can be applied to the collection itself.

### `minLength`

Ensure the collection has at least a minimum number of items.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->minLength(2),
]);
```

### `maxLength`

Ensure the collection has at most a maximum number of items.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->maxLength(10),
]);
```

### `fixedLength`

Ensure the collection has an exact number of items.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->fixedLength(5),
]);
```

### `empty`

Ensure the collection is empty.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->empty(),
]);
```

### `notEmpty`

Ensure the collection is not empty.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->notEmpty(),
]);
```

### `distinct`

Ensure all items in the collection are distinct (no duplicates).

```php
$validator = Grape::schema([
    "items" => Grape::collection()->distinct(),
]);
```

You may want to use a custom key for distinct validation, especially when dealing with complex items. `distinct()` accepts a callback to specify the key to use for comparison. That callback receives the item and its index, and should return a value that uniquely identifies the item.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->distinct(function ($item, $index) {
        return $item['id'];
    }),
]);
```

## Transformations

The following methods transform the array value during validation.

### `normalize`

Reindex the collection to ensure sequential numeric keys.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->normalize(),
]);
```

### `compact`

Remove all `null` or empty string items from the collection.

```php
$validator = Grape::schema([
    "items" => Grape::collection()->compact(),
]);
```
