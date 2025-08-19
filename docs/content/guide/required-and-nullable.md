# Working with Required and Nullable

Grape's default behavioris to consider each field as optional but non-nullable. This means that if a field is not present in the data being validated, it will not trigger a validation error and will simply be ignored. However, if a field is present with a null value, it will fail validation.

You can modify this behavior by marking fields as required or nullable, depending on your use case.

## Marking Fields as Required (Schema-Based Only)

The `required()` method is only applicable in schema-based validation. It ensures that a field must be present in the data being validated. If the field is missing, a validation error will be triggered.

```php
$validator = Grape::schema([
    "name" => Grape::string()->minLength(3)->maxLength(50)->required(),
    "email" => Grape::string()->email()->required(),
    "age" => Grape::integer()->min(0)->required(),
]);
```

With this configuration, if any of these fields are missing from the data being validated, a validation error will occur. Note that `required()` is not applicable when validating individual fields (e.g., a standalone string or integer) because such fields cannot be "missing."

## Marking Fields as Nullable (Universal)

The `nullable()` method can be used universally, whether in schema-based validation or when validating individual fields. It allows a field to have a null value without causing a validation error.

```php
$validator = Grape::schema([
    "name" => Grape::string()->minLength(3)->maxLength(50)->nullable(),
    "email" => Grape::string()->email()->nullable(),
    "age" => Grape::integer()->min(0)->nullable(),
]);
```

In this case, the `name`, `email`, and `age` fields can be present with a null value, preventing other rules from being applied to them and avoiding validation errors.

For individual field validation, `nullable()` works as follows:

```php
$validator = Grape::string()->nullable();

$validator->validate(null); // Passes validation
$validator->validate("example"); // Passes validation
$validator->validate(123); // Fails validation (not a string)
```

## Combining Required and Nullable

Combining `required()` and `nullable()` on the same field ensures that the field itself must exist in the given data while allowing its value to be null. This is useful in scenarios where you want to enforce the presence of a field to match a specific structure but still allow null values.

```php
$validator = Grape::schema([
    "name" => Grape::string()->minLength(3)->maxLength(50)->required()->nullable(),
    "email" => Grape::string()->email()->required()->nullable(),
    "age" => Grape::integer()->min(0)->required()->nullable(),
]);
```

In this case, the `name`, `email`, and `age` fields must be present in the data being validated, but they can also be set to null without causing a validation error.
