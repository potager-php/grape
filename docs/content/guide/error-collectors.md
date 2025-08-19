# Error Collector

Grape relies on an error collector to manage validation errors and build the messages array returned with the `ValidationException`. This collector is responsible for formatting the errors you receive after a validation call.

Grape includes a default `SimpleErrorCollector`, which is used by default. However, you can implement your own collector to suit your specific needs.

## The SimpleErrorCollector

The `SimpleErrorCollector` is automatically registered as Grape's default error collector. It provides a straightforward implementation for collecting and formatting validation errors. The error collector returns an associative array of messages, where the keys are field names and the values are arrays of error messages. Each error message contains three keys:

-   `message`: The validation error message.
-   `rule`: The name of the validation rule that failed.
-   `path`: The field's path in the data structure.

### Example of the Error Array

Here is an example of what the error array might look like:

```php
[
    'username' => [
        [
            'message' => 'The username is required.',
            'rule' => 'required',
            'path' => 'username',
        ],
    ],
    'email' => [
        [
            'message' => 'The email must be a valid email address.',
            'rule' => 'email',
            'path' => 'email',
        ],
    ],
]
```

### Customizing the ValidationException Message

The default message for `ValidationException` is "Validation failed." You can customize this message by setting up a custom instance of `SimpleErrorCollector`:

```php
use Potager\Grape\SimpleErrorCollector;

$errorCollectorFactory = fn () => new SimpleErrorCollector("Your custom exception message");
Grape::setErrorCollector($errorCollectorFactory);
```

## Creating a Custom Error Collector

An error collector is a class that must implement the `ErrorCollectorContract`. This interface defines the methods Grape uses to collect and format validation errors.

-   The `hasError()` method checks if any errors have been collected and must return a boolean value.

-   The `report()` method is called by Grape whenever a validation error occurs. This method receives the following parameters:

    -   The current `FieldContext`
    -   The name of the rule that failed
    -   The validation error message

-   The `createError()` method is used to generate an instance of `ValidationException` containing the collected error messages. This exception is thrown when validation fails.

```php
use Potager\Grape\Contracts\ErrorCollectorContract;

class CustomErrorCollector implements ErrorCollectorContract
{
    private array $errors = [];

    public function hasError(): bool
    {
        return !empty($this->errors);
    }

    public function report(FieldContext $context, string $rule, string $message): void
    {
        // Define how to store the reported errors
        $this->errors[] = [
            'field' => $context->getField(),
            'rule' => $rule,
            'message' => $message,
            'messageAndRule' => $message . ' (' . $rule . ')',
        ];
    }

    public function createError(): ValidationException
    {
        return new ValidationException($this->errors, "Custom validation error");
    }
}
```

## Registering an Error Collector

You can register an error collector globally, for a specific validator, or for a single validation call.

Since a fresh instance of the collector is required for each validation, you need to provide a factory that returns a new instance of your error collector.

### Register Globally

```php
$errorCollectorFactory = fn () => new CustomErrorCollector();
Grape::setErrorCollector($errorCollectorFactory);
```

### Register for a Validator

```php
$errorCollectorFactory = fn () => new CustomErrorCollector();

$validator = Grape::string();

$validator->setErrorCollector($errorCollectorFactory);
```

### Register for a Single Validation Call

```php
$errorCollectorFactory = fn () => new CustomErrorCollector();

$validator = Grape::string();

$validator->validate($payload, errorCollector: $errorCollectorFactory);
```
