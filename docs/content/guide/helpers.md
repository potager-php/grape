---
outline: deep
---

# Validation Helpers

Grape provides a standalone helpers facade accessible via `Grape::helpers()`. This allows you to use Grape's built-in validation rules and utilities directly anywhere in your application without needing to define a schema or instantiate a validator object.

```php
use Potager\Grape\Grape;

if (Grape::helpers()->isUrl($input)) {
    // Valid URL
}
```

---

## Available Helpers

### `isUrl`

Check if a given string is a valid URL format:

```php
Grape::helpers()->isUrl('https://example.com');       // true
Grape::helpers()->isUrl('ftp://files.example.com');   // true
Grape::helpers()->isUrl('not-a-url');                 // false
```

### `isActiveUrl`

Check if a given URL has valid active DNS A, AAAA, or CNAME records:

```php
Grape::helpers()->isActiveUrl('https://google.com');   // true
Grape::helpers()->isActiveUrl('https://invalid-domain-xyz123.com'); // false
```

### `isMobilePhone`

Validate a mobile phone number according to country/locale rules:

```php
// General validation
Grape::helpers()->isMobilePhone('+33612345678'); // true

// Specific locales (e.g. 'fr-FR', 'en-US')
Grape::helpers()->isMobilePhone('+33612345678', ['fr-FR']); // true

// Strict formatting mode (enforces international standard)
Grape::helpers()->isMobilePhone('+33612345678', ['fr-FR'], strict: true);
```

### `isCreditCard`

Validate credit card numbers using the Luhn checksum algorithm and provider patterns:

```php
// Check against all supported card providers
Grape::helpers()->isCreditCard('4111111111111111'); // true (Visa)

// Check against specific providers only
Grape::helpers()->isCreditCard('4111111111111111', ['visa']); // true
Grape::helpers()->isCreditCard('4111111111111111', ['mastercard']); // false
```

**Supported providers:** `visa`, `mastercard`, `amex`, `discover`, `jcb`, `diners_club`, `maestro`, `union_pay`, etc.

### `isLuhnNumber`

Verify any number against the Luhn modulus 10 algorithm:

```php
Grape::helpers()->isLuhnNumber('79927398713'); // true
```

### `isIp`, `isIpv4`, `isIpv6`

Validate IP addresses with optional version constraints:

```php
Grape::helpers()->isIp('192.168.1.1');         // true
Grape::helpers()->isIp('::1');                 // true

Grape::helpers()->isIpv4('192.168.1.1');       // true
Grape::helpers()->isIpv4('::1');               // false

Grape::helpers()->isIpv6('::1');               // true
Grape::helpers()->isIpv6('192.168.1.1');       // false
```

### `isJson`

Check if a string contains valid, parsable JSON:

```php
Grape::helpers()->isJson('{"key": "value"}');  // true
Grape::helpers()->isJson('[1, 2, 3]');         // true
Grape::helpers()->isJson('{invalid json}');    // false
```

### `isTrue` & `isFalse`

Check whether a value is considered truthy or falsy according to Grape's configured boolean-like values:

```php
Grape::helpers()->isTrue(true);     // true
Grape::helpers()->isTrue('yes');    // true
Grape::helpers()->isTrue('1');      // true
Grape::helpers()->isTrue('no');     // false

Grape::helpers()->isFalse(false);   // true
Grape::helpers()->isFalse('off');   // true
Grape::helpers()->isFalse('0');     // true
```
