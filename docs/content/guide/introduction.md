---
outline: deep
---

# Introduction

Grape PHP is a data validation framework for PHP developers. The most common use case is validating HTTP request bodies in controllers.

-   Grape provides both simple type validation and complex nested schema validation.

-   It is built to handle associative arrays.

-   It provides an API that lets you define custom error messages and formatting.

-   It comes with 12 supported types and up to 50 built-in rules.

-   It allows you to create your own rules to extend the framework.

## Examples

::: code-group

```php [Basic Example]
use Potager\Grape\Grape;

$validator = Grape::string()->trim()->lowercase()->email();

$validator->validate("john.doe@mail.com");

```

```php [Schema example]
use Potager\Grape\Grape;

$validator = Grape::schema([
    "name" => Grape::string()->minLength(2)->maxLength(255)->required(),
    "email" => Grape::string()->email()->required(),
    "age" => Grape::integer()->positive()->max(120)->nullable()->required(),
]);

$validator->validate([
    "name" => "John Doe",
    "email" => "john.doe@mail.com",
    "age" => 45
]);

```

```php [Nested Structures]
use Potager\Grape\Grape;

$validator = Grape::schema([
    "id" => Grape::integer()->positive()->required(),
    "content" => Grape::schema([
        "heading" => Grape::string()->notEmpty()->maxLength(255)->required(),
        "body" => Grape::string()->nullable()->required()
    ])->required(),
    "tags" => Grape::collection(
        Grape::string()
    )->distinct()->required()
]);

$validator->validate([
    "id" => 2,
    "content" => [
        "heading" => "Article",
        "body" => null,
    ],
    "tags" => ["validation", "php"]
]);

```

:::

## Inspired by VineJS

Grape PHP draws significant inspiration from [VineJS](https://vinejs.dev/), a popular validation library in the Node.js ecosystem. Developers familiar with VineJS will recognize many similar patterns and concepts in Grape's design philosophy.

While we didn't aim to create a direct port of VineJS for PHP, it served as our primary reference when defining the framework's feature set and API design. This influence is evident in several areas:

-   **Validation Rules**: Many validation rules and their behaviors mirror those found in VineJS
-   **Configuration Options**: Similar approaches to customization and error handling
-   **Method Chaining**: Fluent API design that feels familiar to VineJS users

However, Grape PHP also introduces unique features tailored specifically for the PHP ecosystem:

-   **Standalone Validation**: Unlike VineJS, Grape allows you to validate individual values without requiring a schema wrapper
-   **PHP-Specific Types**: Native support for PHP data types and conventions

We continue to evolve Grape PHP independently, adding new features and improvements based on the needs of the PHP community while maintaining the intuitive developer experience that made VineJS popular.

## Tailored to Handle HTML Form Quirks

Grape was originally built to answer a fundamental question: "Is the user-provided data valid?" This is typically the question we ask when users submit HTML forms that are processed by our backend systems.

Grape makes it easy to handle form data and the common quirks that come with HTML forms, such as:

-   Numbers and booleans serialized as strings by browsers
-   Checkboxes that aren't true booleans and are missing from the payload when unchecked
-   Empty fields represented as empty strings instead of null values

**How Grape addresses these challenges:**

-   **Smart Type Casting**: Automatically converts "numerical strings" and "boolean-like strings" into proper integers, floats, or booleans when needed
-   **Accepted Type**: Provides a dedicated `accepted()` type that handles checkbox behavior elegantly
-   **Empty String Normalization**: Offers the option to convert empty strings to null values before validation processing

## Limitations

-   Grape does not provide validation support for callables, classes, or objects. While you can convert objects to associative arrays for use with `schema()` validation, the data will remain as an associative array until you manually convert it back, which means you lose the benefits of object-oriented programming structures.

-   Grape has limited support for conditional validation, meaning you cannot specify that a value can be either an integer or a string. You must choose one type and maintain consistency with that choice throughout the validation process.

