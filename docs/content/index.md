---
layout: home

hero:
    name: 'Grape'
    text: 'An Open-Source PHP Validation Package'
    tagline: Inspired by Vine.js
    image:
        src: '/assets/logo.png'
        alt: 'Grape Icon'
    actions:
        - theme: brand
          text: Getting Started
          link: /guide/getting-started
        - theme: alt
          text: Validators & Rules
          link: /validator/types/string

features:
    - title: Intuitive Definitions
      icon: 📚
      details: Easily define validation rules with clear and concise syntax.

    - title: Secure Output
      icon: 🔒
      details: Ensure data integrity with sanitized and validated outputs.

    - title: Strict or Loose Typing
      icon: 🔍
      details: Choose between strict and loose typing to enforce data type constraints as needed.

    - title: Custom Error Messages
      icon: 📣
      details: Craft personalized error messages to guide users effectively.

    # - title: Database Integration
    # icon: 🗄️
    # details: Seamlessly connect and validate data against your database schema.

    - title: Reusable Validators
      icon: ♻️
      details: Write validation logic once and reuse it across your application.

    # - title: Custom Rules
    # icon: 🔌
    # details: Extend functionality with custom functions tailored to your needs.

    - title: Comprehensive Documentation
      icon: 📜
      <!-- details: Access detailed documentation and examples to quickly get started. -->
---

## Usage

Example of how to use Grape

::: code-group

```php [Create a Validator]
use Grape\Grape;
use Grape\Exceptions\ValidationException;

// Define a validation schema
$validator = Grape::schema([
    "id" => Grape::integer()->positive(), // Ensure 'id' is a positive integer
    "email" => Grape::string()->email()->max(255), // Validate and sanitize email
    "username" => Grape::string()->noWhitespace(), // Ensure 'username' has no whitespace
    "phone" => Grape::string()->phone(), // Validate phone number format
    "eula" => Grape::boolean()->true(), // Ensure 'eula' is true
]);
```

```php [Use the Validator]
use Grape\Exceptions\ValidationException;

// Sample data to validate
$data = [
    "id" => 123,
    "email" => "example@example.com",
    "username" => "user123",
    "phone" => "+1234567890",
    "eula" => true,
];

try {
    // Validate the data
    $sanitized = $validator->validate($data);
} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->getMessages();
}
```

:::
