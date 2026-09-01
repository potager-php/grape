---
outline: deep
---

# Global Configuration

Grape allows you to configure global default behaviors that apply across all validators in your application.

```php
use Potager\Grape\Grape;

// Configure global behaviors during bootstrap
Grape::$defaultStrict = true;
Grape::$defaultRequired = true;
Grape::$convertEmptyStringsToNull = true;
```

---

## Behavior Properties

### `$defaultStrict`

Controls whether validators enforce strict type checking by default.

- **Default:** `false` (Loose mode: casts scalar numbers/strings automatically)
- **When `true`:** Validators will only accept exact native types without automatic type coercion.

```php
use Potager\Grape\Grape;

Grape::$defaultStrict = true;

// String validator now requires a native string by default
$validator = Grape::string();
$validator->validate(123); // Throws ValidationException (strict mode active)
```

### `$defaultRequired`

Controls whether schema fields are required by default.

- **Default:** `false` (Fields are optional by default unless `->required()` is called)
- **When `true`:** All fields in schemas become mandatory unless explicitly marked as optional.

```php
use Potager\Grape\Grape;

Grape::$defaultRequired = true;

$validator = Grape::schema([
    "username" => Grape::string(), // Required by default!
]);
```

### `$defaultNullable`

Controls whether fields accept `null` values by default.

- **Default:** `false` (Fields reject `null` unless `->nullable()` is called)
- **When `true`:** All fields permit `null` values by default.

```php
use Potager\Grape\Grape;

Grape::$defaultNullable = true;

$validator = Grape::string();
$validator->validate(null); // Passes validation
```

### `$convertEmptyStringsToNull`

Automatically sanitizes empty strings `""` into `null` values before validation rules run. This is particularly helpful when processing HTML form submissions.

- **Default:** `false`
- **When `true`:** Empty string inputs `""` are mutated to `null`.

```php
use Potager\Grape\Grape;

Grape::$convertEmptyStringsToNull = true;

$validator = Grape::string()->nullable();
$result = $validator->validate(""); // Returns null
```

---

## Boolean-Like Values

You can also globally customize which strings and numbers are interpreted as boolean `true` (truthy) or `false` (falsy):

```php
use Potager\Grape\Grape;

// Add multilingual terms
Grape::addTruthy(['oui', 'si', 'vrai', 'active']);
Grape::addFalsy(['non', 'faux', 'inactive']);

// Replace all truthy/falsy values
Grape::setTruthy(['yes', 'true', 1]);
Grape::setFalsy(['no', 'false', 0]);

// Reset to framework defaults
Grape::resetBooleanValues();
```
