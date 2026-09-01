---
outline: deep
---

# Customize Error Messages

Grape provides an elegant way to customize the error messages returned by validators. You can use the built-in message provider or create your own to implement advanced formatting that fits your project’s needs.

In this guide, we’ll cover two methods for customizing error messages in your Grape instance.

## Using the `SimpleMessageProvider`

Grape includes the `SimpleMessageProvider` class, which makes it easy to set up custom messages. With this provider, you can define messages for your validators in a straightforward way, as well as create aliases for field names.

Simply pass an array of custom messages to the `SimpleMessageProvider` constructor. Keys can be rule names or a fully qualified field path + rule name, and values are the corresponding custom messages.

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

Grape::setMessageProvider(new SimpleMessageProvider([
    // Applies to all fields
    'required' => 'Please provide a value for the {field} field.',
    'email' => 'Please provide a valid email address.',

    // Targets a specific field
    'user.email.email' => 'Please provide a valid email address for the user.',
]));
```

### Targeting Specific Fields

You may want to define messages for specific fields within a nested structure. Using dot notation, you can specify the full path to the field, followed by the rule name.

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

Grape::setMessageProvider(new SimpleMessageProvider([
    'user.email.email' => 'Please provide a valid email address for the user.',
    'user.name.required' => 'Please provide a name for the user.',
    'user.socials.github.url' => 'Please provide a valid GitHub username for the user.',
    'user.tags.0.color.required' => 'Please provide a color for the first tag of the user.'
]));
```

You can also use wildcards to target all children of an array (useful for validating collections).

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

Grape::setMessageProvider(new SimpleMessageProvider([
    'tags.*.color.required' => 'Please provide a color for each tag of the user.',
    'tags.*.name.string' => 'Please provide a valid name for each tag of the user.'
]));
```

If you want to target the root element directly, note that the root path is `""`. Using just the rule name will apply the message to **all** fields using that rule. To ensure it applies only to the root, prefix the rule with a dot.

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

Grape::setMessageProvider(new SimpleMessageProvider([
    '.required' => 'Please provide a value for the root element.',
    '.yourCustomRule' => 'Please provide a valid value for the root element.'
]));
```

### Interpolation

You can use placeholders such as `{field}` in your messages. These will be dynamically replaced with the corresponding values.

The `{field}` placeholder is always available. Other placeholders depend on the rule—refer to the rule documentation to see which are supported.

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

Grape::setMessageProvider(new SimpleMessageProvider([
    "string" => "The {field} must be a string.",
    "minLength" => "The {field} must be at least {length} characters long.",
    "contains" => "The {field} must contain {substring}."
]));
```

### Field Name Aliases

`SimpleMessageProvider` also allows you to define aliases for field names. These will be used instead of schema field names when interpolating the `{field}` placeholder.

Simply pass a second array to the constructor, mapping original field names to their aliases.

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

Grape::setMessageProvider(new SimpleMessageProvider([
    // Custom messages if needed
], [
    "user" => "The User",
    "user.email" => "Email",
    "user.socials.github" => "GitHub Account",
    "user.tags.0.color" => "First Tag Color"
]));
```

## Creating a Message Provider

For advanced use cases such as I18N, you may want to build a fully custom message provider. To do so, implement the `MessageProviderContract` interface.

Be aware that this will override everything covered in `SimpleMessageProvider`, since you’ll need to handle all message formatting and interpolation yourself.

The only required method is `getMessage`, as defined in `MessageProviderContract`. Grape calls this method to retrieve error messages for a given field and rule.

```php
use Potager\Grape\Contracts\MessageProviderContract;

class CustomMessageProvider implements MessageProviderContract
{
    public function getMessage(string $defaultMessage, string $rule, FieldContext $field, array $meta = []): string
    {
        // Implement your custom message retrieval logic here
    }
}
```

The `getMessage` method is responsible for returning the appropriate error message based on the provided parameters.

It will always receive the default message from the validator, which you can keep as a fallback. You also get the rule name and the `FieldContext`, which contains useful information about the field being validated. The `$meta` array includes additional data such as `maxLength` for string validations.

## Registering the Message Provider

You can register a message provider globally, per validator, or per validation call.

### Register Globally

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

$provider = new SimpleMessageProvider();

Grape::setMessageProvider($provider);
```

### Register on a Validator

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

$provider = new SimpleMessageProvider();

$validator = Grape::string();

$validator->setMessageProvider($provider);
```

### Register Per Call

```php
use Potager\Grape\Messages\SimpleMessageProvider;
use Potager\Grape\Grape;

$provider = new SimpleMessageProvider();

$validator = Grape::string();

$validator->validate($payload, messageProvider: $provider);
```
