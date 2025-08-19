<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

it('can validate a string', function (): void {
    $validator = Grape::string();
    $result = $validator->validate('test string');
    expect($result)->toBe('test string');
});

it('can validate a string (loose mode)', function (): void {
    $validator = Grape::string();
    $result = $validator->validate(12);
    expect($result)->toBeString()->toBe("12");
});

it('can fail if it\'s not a string (loose mode) ', function (): void {
    $validator = Grape::string();
    $result = $validator->validate([]);
})->throws(ValidationException::class);

it('can fail if it\'s not a string (strict mode) ', function (): void {
    $validator = Grape::string(true);
    $result = $validator->validate(12);
})->throws(ValidationException::class);

it('can validate a string with min and max length', function (): void {
    $validator = Grape::string()->minLength(3)->maxLength(5);
    $result = $validator->validate('test');
    expect($result)->toBe('test');
});

it('can fail if string is too short', function (): void {
    $validator = Grape::string()->minLength(5);
    $result = $validator->validate('test');
})->throws(ValidationException::class);

it('can fail if string is too long', function (): void {
    $validator = Grape::string()->maxLength(3);
    $result = $validator->validate('test');
})->throws(ValidationException::class);

// Test trim() method
it('can trim whitespace from string', function (): void {
    $validator = Grape::string()->trim();
    $result = $validator->validate('  test  ');
    expect($result)->toBe('test');
});

// Test lowercase() method
it('can convert string to lowercase', function (): void {
    $validator = Grape::string()->lowercase();
    $result = $validator->validate('TEST');
    expect($result)->toBe('test');
});

// Test uppercase() method
it('can convert string to uppercase', function (): void {
    $validator = Grape::string()->uppercase();
    $result = $validator->validate('test');
    expect($result)->toBe('TEST');
});

// Test length() method
it('can validate exact string length', function (): void {
    $validator = Grape::string()->fixedLength(4);
    $result = $validator->validate('test');
    expect($result)->toBe('test');
});

it('can fail if string length is wrong', function (): void {
    $validator = Grape::string()->fixedLength(5);
    $result = $validator->validate('test');
})->throws(ValidationException::class);

// Test prefix() method
it('can validate string prefix (case sensitive)', function (): void {
    $validator = Grape::string()->prefix('pre');
    $result = $validator->validate('prefix');
    expect($result)->toBe('prefix');
});

it('can fail if string does not have required prefix', function (): void {
    $validator = Grape::string()->prefix('pre');
    $result = $validator->validate('suffix');
})->throws(ValidationException::class);

it('can validate string prefix (case insensitive)', function (): void {
    $validator = Grape::string()->prefix('PRE', false);
    $result = $validator->validate('prefix');
    expect($result)->toBe('prefix');
});

// Test suffix() method
it('can validate string suffix (case sensitive)', function (): void {
    $validator = Grape::string()->suffix('fix');
    $result = $validator->validate('prefix');
    expect($result)->toBe('prefix');
});

it('can fail if string does not have required suffix', function (): void {
    $validator = Grape::string()->suffix('fix');
    $result = $validator->validate('prefab');
})->throws(ValidationException::class);

it('can validate string suffix (case insensitive)', function (): void {
    $validator = Grape::string()->suffix('FIX', false);
    $result = $validator->validate('prefix');
    expect($result)->toBe('prefix');
});

// Test contains() method
it('can validate string contains substring (case sensitive)', function (): void {
    $validator = Grape::string()->contains('est');
    $result = $validator->validate('test');
    expect($result)->toBe('test');
});

it('can fail if string does not contain required substring', function (): void {
    $validator = Grape::string()->contains('xyz');
    $result = $validator->validate('test');
})->throws(ValidationException::class);

it('can validate string contains substring (case insensitive)', function (): void {
    $validator = Grape::string()->contains('EST', false);
    $result = $validator->validate('test');
    expect($result)->toBe('test');
});

// Test alphabetic() method
it('can validate alphabetic string with whitespaces', function (): void {
    $validator = Grape::string()->alphabetic();
    $result = $validator->validate('Hello World');
    expect($result)->toBe('Hello World');
});

it('can validate alphabetic string without whitespaces', function (): void {
    $validator = Grape::string()->alphabetic(false);
    $result = $validator->validate('HelloWorld');
    expect($result)->toBe('HelloWorld');
});

it('can validate alphabetic string with dashes and underscores', function (): void {
    $validator = Grape::string()->alphabetic(true, true, true);
    $result = $validator->validate('Hello-World_Test');
    expect($result)->toBe('Hello-World_Test');
});

it('can fail if string is not alphabetic', function (): void {
    $validator = Grape::string()->alphabetic();
    $result = $validator->validate('Hello123');
})->throws(ValidationException::class);

// Test numeric() method
it('can validate numeric string with whitespaces', function (): void {
    $validator = Grape::string()->numeric();
    $result = $validator->validate('123 456');
    expect($result)->toBe('123 456');
});

it('can validate numeric string without whitespaces', function (): void {
    $validator = Grape::string()->numeric(false);
    $result = $validator->validate('123456');
    expect($result)->toBe('123456');
});

it('can validate numeric string with dashes and underscores', function (): void {
    $validator = Grape::string()->numeric(true, true, true);
    $result = $validator->validate('123-456_789');
    expect($result)->toBe('123-456_789');
});

it('can fail if string is not numeric', function (): void {
    $validator = Grape::string()->numeric();
    $result = $validator->validate('123abc');
})->throws(ValidationException::class);

// Test alphanumeric() method
it('can validate alphanumeric string with whitespaces', function (): void {
    $validator = Grape::string()->alphanumeric();
    $result = $validator->validate('Hello 123');
    expect($result)->toBe('Hello 123');
});

it('can validate alphanumeric string without whitespaces', function (): void {
    $validator = Grape::string()->alphanumeric(false);
    $result = $validator->validate('Hello123');
    expect($result)->toBe('Hello123');
});

it('can validate alphanumeric string with dashes and underscores', function (): void {
    $validator = Grape::string()->alphanumeric(true, true, true);
    $result = $validator->validate('Hello-123_World');
    expect($result)->toBe('Hello-123_World');
});

it('can fail if string is not alphanumeric', function (): void {
    $validator = Grape::string()->alphanumeric();
    $result = $validator->validate('Hello@123');
})->throws(ValidationException::class);

// Test noWhitespace() method
it('can validate string with no whitespace', function (): void {
    $validator = Grape::string()->noWhitespace();
    $result = $validator->validate('HelloWorld');
    expect($result)->toBe('HelloWorld');
});

it('can fail if string contains whitespace', function (): void {
    $validator = Grape::string()->noWhitespace();
    $result = $validator->validate('Hello World');
})->throws(ValidationException::class);

// Test email() method
it('can validate email address', function (): void {
    $validator = Grape::string()->email();
    $result = $validator->validate('test@example.com');
    expect($result)->toBe('test@example.com');
});

it('can fail if email is invalid', function (): void {
    $validator = Grape::string()->email();
    $result = $validator->validate('invalid-email');
})->throws(ValidationException::class);

// Test phone() method
it('can validate phone number', function (): void {
    $validator = Grape::string()->phone();
    $result = $validator->validate('+32456789000');
    expect($result)->toBe('+32456789000');
});

it('can fail if phone number is invalid', function (): void {
    $validator = Grape::string()->phone();
    $result = $validator->validate('not-a-phone');
})->throws(ValidationException::class);

// Test json() method
it('can validate JSON string', function (): void {
    $validator = Grape::string()->json();
    $result = $validator->validate('{"key": "value"}');
    expect($result)->toBe('{"key": "value"}');
});

it('can fail if JSON is invalid', function (): void {
    $validator = Grape::string()->json();
    $result = $validator->validate('invalid-json');
})->throws(ValidationException::class);

// Test url() method
it('can validate URL', function (): void {
    $validator = Grape::string()->url();
    $result = $validator->validate('https://example.com');
    expect($result)->toBe('https://example.com');
});

it('can fail if URL is invalid', function (): void {
    $validator = Grape::string()->url();
    $result = $validator->validate('not-a-url');
})->throws(ValidationException::class);

// Test activeUrl() method
it('can validate active URL', function (): void {
    $validator = Grape::string()->activeUrl();
    $result = $validator->validate('https://google.com');
    expect($result)->toBe('https://google.com');
});

it('can fail if active URL is invalid', function (): void {
    $validator = Grape::string()->activeUrl();
    $result = $validator->validate('https://nonexistentdomain12345.com');
})->throws(ValidationException::class);

// Test creditCard() method
it('can validate credit card number', function (): void {
    $validator = Grape::string()->creditCard();
    $result = $validator->validate('4111111111111111'); // Valid Visa test number
    expect($result)->toBe('4111111111111111');
});

it('can fail if credit card is invalid', function (): void {
    $validator = Grape::string()->creditCard();
    $result = $validator->validate('1234567890123456');
})->throws(ValidationException::class);

// Test ip() method
it('can validate IP address', function (): void {
    $validator = Grape::string()->ip();
    $result = $validator->validate('192.168.1.1');
    expect($result)->toBe('192.168.1.1');
});

it('can validate IPv4 address', function (): void {
    $validator = Grape::string()->ip('4');
    $result = $validator->validate('192.168.1.1');
    expect($result)->toBe('192.168.1.1');
});

it('can validate IPv6 address', function (): void {
    $validator = Grape::string()->ip('6');
    $result = $validator->validate('2001:0db8:85a3:0000:0000:8a2e:0370:7334');
    expect($result)->toBe('2001:0db8:85a3:0000:0000:8a2e:0370:7334');
});

it('can fail if IP address is invalid', function (): void {
    $validator = Grape::string()->ip();
    $result = $validator->validate('999.999.999.999');
})->throws(ValidationException::class);

// Test empty() method
it('can validate empty string (ignoring whitespaces)', function (): void {
    $validator = Grape::string()->empty();
    $result = $validator->validate('   ');
    expect($result)->toBe('   ');
});

it('can validate empty string (not ignoring whitespaces)', function (): void {
    $validator = Grape::string()->empty(false);
    $result = $validator->validate('');
    expect($result)->toBe('');
});

it('can fail if string is not empty', function (): void {
    $validator = Grape::string()->empty();
    $result = $validator->validate('test');
})->throws(ValidationException::class);

// Test notEmpty() method
it('can validate non-empty string', function (): void {
    $validator = Grape::string()->notEmpty();
    $result = $validator->validate('test');
    expect($result)->toBe('test');
});

it('can fail if string is empty', function (): void {
    $validator = Grape::string()->notEmpty();
    $result = $validator->validate('');
})->throws(ValidationException::class);

// Test pattern() method
it('can validate string with regex pattern', function (): void {
    $validator = Grape::string()->pattern('/^[a-z]+$/');
    $result = $validator->validate('test');
    expect($result)->toBe('test');
});

it('can fail if string does not match pattern', function (): void {
    $validator = Grape::string()->pattern('/^[a-z]+$/');
    $result = $validator->validate('Test123');
})->throws(ValidationException::class);

// Test chaining multiple methods
it('can chain multiple validation methods', function (): void {
    $validator = Grape::string()->trim()->lowercase()->minLength(3)->maxLength(10);
    $result = $validator->validate('  TEST  ');
    expect($result)->toBe('test');
});

it('can fail with chained validations', function (): void {
    $validator = Grape::string()->trim()->minLength(10);
    $result = $validator->validate('  test  ');
})->throws(ValidationException::class);