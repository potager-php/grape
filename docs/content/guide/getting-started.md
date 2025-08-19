---
outline: deep
---

# Getting Started

To install Grape on an existing PHP project, you can use Composer, which is the recommended way to manage dependencies in PHP projects.

```bash
composer require potagerphp/grape
```

Once installed, you can import and use Grape in your PHP files:

```php
// Import the Grape library
require 'vendor/autoload.php';
use Potager\Grape\Grape;

// Create a simple schema validator
$validator = Grape::schema([
    "name" => Grape::string()->minLength(3)->maxLength(50)->required(),
    "email" => Grape::string()->email()->required(),
    "age" => Grape::integer()->min(0)->required(),
]);

// Create a sample data array to validate
$data = [
    "name" => "John Doe",
    "email" => "john.doe@example.com",
    "age" => 30,
];

// Validate the data against the schema
$output = $validator->validate($data);
var_dump($output);

```

-   The `Grape::schema()` method is used to define a schema for validation.
-   The `Grape::string()`, `Grape::integer()`, etc., methods are used to define the types of the fields in the schema.
-   The `minLength()`, `maxLength()`, `email()`, and `min()` methods are used to set validation rules for the fields.
-   The `required()` method indicates that a field must be present in the data being validated.
-   The `validate()` method checks the provided data against the defined schema and returns the validation result.

## Handle Validation Errors

When validation fails, Grape throws a `ValidationException` with comprehensive error information:

```php
try {
    $output = $validator->validate($data);
} catch (ValidationException $e) {
    var_dump($e);
}
```

The `ValidationException->getMessages()` methods returns an array of validation error messages, which can be used to inform the user about what went wrong.
Messages are grouped by field and include details about the validation rule that failed.

### Error Array Structure

Here's what error arrays actually look like with the default `SimpleErrorCollector`, this is likely to change if you register another error collector:

```php
// Example of getMessages() - grouped by field:
[
    'name' => [
        [
            'message' => 'The name field must be at least 2 characters long',
            'rule' => 'minLength',
            'path' => 'name'
        ]
    ],
    'email' => [
        [
            'message' => 'The email field must be a valid string',
            'rule' => 'string',
            'path' => 'email'
        ],
        [
            'message' => 'The email field must be a valid email address',
            'rule' => 'email',
            'path' => 'email'
        ]
    ],
    'user.profile.age' => [
        [
            'message' => 'The age field must be at least 13',
            'rule' => 'min',
            'path' => 'user.profile.age'
        ]
    ]
]
```

## Customize error messages

Grape uses a `MessageProvider` to manage error messages. Grape ships with a `SimpleMessageProvider` that allows you to customize error messages per rule or per field + rule combination.

[Learn more about custom error messages.](./custom-messages.md)

## Error Formatting

Errors are reported to an `ErrorCollector` that handles formatting the errors into an array that is provided with the thrown exception. Grape allows you to use a custom error collector to change the error formatting, which can be useful for matching specific API specifications.

[Learn more about error collectors.](./error-collectors.md)

## Understanding Field Paths

Field paths represent the location of data within your validation schema using dot notation:

```php
// Simple fields
"name", "email"

// Nested objects
"user.profile.firstName", "user.settings.theme"

// Arrays with indices
"users.0.name", "users.1.email"

// Root-level errors (empty string)
""
```

### When Paths Are Empty

Root-level validation errors have an empty string path `""` in two cases:

1. **Schema-level validation** - Custom rules applied to the entire object:

```php
$validator = Grape::schema([...])->custom(function($ctx) {
    // ...
    return $ctx->report("Passwords must match", 'password_confirmation');
});
```

2. **Simple type validation** - Direct validation without a schema:

```php
$validator = Grape::string()->email();
$validator->validate("invalid-email"); // Path: ""
```

Field paths help you map validation errors back to specific form fields or API parameters in your application.
