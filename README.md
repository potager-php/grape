<div align="center">

# 🍇 potager / grape

**Type-safe schema validation, data sanitization, and structural contracts for PHP.**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/potagerphp/grape.svg?style=flat-square&color=black)](https://packagist.org/packages/potagerphp/grape)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue?style=flat-square&color=black)](https://php.net)
[![Software License](https://img.shields.io/badge/license-MIT-green?style=flat-square&color=black)](LICENSE)
[![Zero Dependencies](https://img.shields.io/badge/dependencies-0-brightgreen?style=flat-square&color=black)](composer.json)
[![Documentation](https://img.shields.io/badge/docs-potager.dev-black?style=flat-square)](https://github.com/potagerphp/grape)

---

</div>

**Grape** is a modern, zero-dependency schema validation and sanitization library for PHP 8.2+. Inspired by declarative schema ecosystems like Zod, Grape allows you to define declarative contracts, validate untrusted inputs, sanitize values in-place, and receive strongly-typed output with precise, nested error trees.

---

## ⚡ Highlights

- **Zero Dependencies** — Pure, lightweight PHP with no third-party runtime overhead.
- **Fluent & Type-Safe** — Full IDE autocompletion with chainable, expressive constraint rules.
- **In-Place Sanitization** — Transform data during parsing (`trim()`, `lowercase()`, `clamp()`, `compact()`).
- **Flexible Flow Control** — Choose between traditional exception throwing or functional non-throwing tuples `[$error, $data]`.
- **Dual Coercion Modes** — Loose mode (default) casts HTTP strings (e.g. `"42"` &rarr; `42`, `"true"` &rarr; `true`), while strict mode enforces exact native types.
- **First-Class Composition** — Nest complex schemas, homogeneous collections, and fixed-size tuples with deduplication and index normalization.
- **Customizable Error Collectors** — Output error trees, flat maps, or fast-fail on the first encountered error.

---

## 📦 Installation

Install Grape via Composer:

```bash
composer require potagerphp/grape
```

---

## 🚀 Quickstart

### 1. Basic Schema & In-Place Sanitization

```php
use Potager\Grape\Grape;

// Define your validation contract
$userSchema = Grape::schema([
    'name'     => Grape::string()->trim()->minLength(2)->maxLength(50)->required(),
    'email'    => Grape::string()->trim()->lowercase()->email()->required(),
    'age'      => Grape::integer()->min(18)->max(120)->optional(),
    'role'     => Grape::literal('admin', 'editor', 'viewer')->default('viewer'),
    'metadata' => Grape::schema([
        'tags'   => Grape::collection(Grape::string())->distinct(),
        'active' => Grape::boolean()->default(true),
    ])->optional(),
]);

// Parse & sanitize untrusted payload
$cleanData = $userSchema->validate($_POST);
```

---

### 2. Functional Non-Throwing Flow (`check()`)

If you prefer to avoid `try / catch` blocks in your controllers or middleware, use the functional `check()` method:

```php
[$error, $data] = $userSchema->check($requestPayload);

if ($error !== null) {
    // $error->getMessages() returns a structured error array
    return response()->json([
        'success' => false,
        'errors'  => $error->getMessages(),
    ], 422);
}

// $data is guaranteed clean, sanitized, and typed
$userService->register($data);
```

---

## 🧩 Core Capabilities

### Strict vs. Loose Type Coercion

By default, Grape operates in **loose mode**, making it seamless to validate HTTP form submissions where numbers and booleans arrive as strings:

```php
// Loose mode (Default)
Grape::integer()->validate("42");   // Returns (int) 42
Grape::boolean()->validate("true"); // Returns (bool) true

// Strict mode (Ideal for pure JSON APIs)
Grape::integer(strict: true)->validate("42"); // Throws ValidationException
Grape::integer(strict: true)->validate(42);   // Returns 42
```

---

### Homogeneous Collections & Tuples

```php
// Homogeneous List with sanitization & deduplication
$tagsValidator = Grape::collection(Grape::string()->trim()->lowercase())
    ->min(1)
    ->max(10)
    ->distinct()
    ->skipInvalids();

// Fixed-size, ordered Tuple (e.g., Geo Coordinates: [lat, lng])
$coordinatesValidator = Grape::tuple([
    Grape::float()->min(-90)->max(90),
    Grape::float()->min(-180)->max(180),
]);
```

---

### Custom Error Messages & Dynamic Placeholders

Customize feedback per rule with dynamic interpolation tokens:

```php
$validator = Grape::string()
    ->minLength(5, 'The :field is too short (min :min characters, got :value).')
    ->email('Please provide a valid corporate email address.');
```

Supported placeholders include:
- `:field` — The attribute path (e.g. `user.email` or `items.0.sku`)
- `:value` — The actual input value provided
- `:min` / `:max` / `:length` — Boundary parameters for the active rule

---

### Error Collectors

Choose how errors are gathered and formatted:

```php
// 1. Nested Tree Collector (Default) - mirrors input shape
Grape::useTreeCollector();

// 2. Flat Dot-Notation Collector - e.g. ['user.email' => [...]]
Grape::useFlatCollector();

// 3. First Error Collector - stops validation at first error for maximum performance
Grape::useFirstErrorCollector();
```

---

## 📚 Supported Types Reference

| Type Validator | Description | Key Methods |
| :--- | :--- | :--- |
| `Grape::string()` | String validation & string sanitization | `minLength()`, `maxLength()`, `email()`, `url()`, `uuid()`, `regex()`, `trim()`, `lowercase()`, `uppercase()` |
| `Grape::number()` | Unified numeric validation (int or float) | `min()`, `max()`, `positive()`, `negative()`, `clamp()` |
| `Grape::integer()` | Exact integer numbers | `min()`, `max()`, `even()`, `odd()`, `clamp()` |
| `Grape::float()` | Floating-point numbers | `min()`, `max()`, `precision()`, `clamp()` |
| `Grape::boolean()` | Boolean flag parsing | `truthy()`, `falsy()`, customizable boolean-like mappings |
| `Grape::accepted()` | Terms / agreement checkboxes | `yes`, `on`, `1`, `true` acceptance |
| `Grape::schema()` | Associative object/dictionary schemas | `strict()`, `ignoreExtraKeys()`, presence contracts |
| `Grape::collection()` | Homogeneous indexed arrays | `distinct()`, `skipInvalids()`, `min()`, `max()` |
| `Grape::tuple()` | Fixed-length ordered heterogeneous arrays | Exact index-by-index positional validation |
| `Grape::literal()` | Exact scalar value matching (Enums / Discriminated Unions) | `literal('draft', 'published', 'archived')` |
| `Grape::null()` | Explicit `null` value validation | Ensures target value is strictly null |
| `Grape::mixed()` | Untyped pass-through with custom constraints | Custom closures and pre-conditions |

---

## 🧪 Testing

Grape is rigorously tested with **Pest** and statically analyzed with **PHPStan** (Level 8+):

```bash
# Run test suite
composer test

# Run static analysis
composer analyse
```

---

## 📄 License

The Potager Grape library is open-sourced software licensed under the **[MIT license](LICENSE)**.

---

<div align="center">
  <sub>Built with care for the PHP Community by the <a href="https://github.com/potagerphp">Potager PHP</a> team.</sub>
</div>
