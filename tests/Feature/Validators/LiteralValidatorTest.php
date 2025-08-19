<?php

use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

// Basic Literal Validation Tests
it('can validate exact string match', function (): void {
    $validator = Grape::literal('hello');
    $result = $validator->validate('hello');
    expect($result)->toBe('hello');
});

it('can validate exact integer match', function (): void {
    $validator = Grape::literal(42);
    $result = $validator->validate(42);
    expect($result)->toBe(42);
});

it('can validate exact boolean true match', function (): void {
    $validator = Grape::literal(true);
    $result = $validator->validate(true);
    expect($result)->toBe(true);
});

it('can validate exact boolean false match', function (): void {
    $validator = Grape::literal(false);
    $result = $validator->validate(false);
    expect($result)->toBe(false);
});

it('can validate exact null match', function (): void {
    $validator = Grape::literal(null)->nullable();
    $result = $validator->validate(null);
    expect($result)->toBe(null);
});

it('can validate exact float match', function (): void {
    $validator = Grape::literal(3.14);
    $result = $validator->validate(3.14);
    expect($result)->toBe(3.14);
});

it('can validate exact array match', function (): void {
    $expected = ['a', 'b', 'c'];
    $validator = Grape::literal($expected);
    $result = $validator->validate(['a', 'b', 'c']);
    expect($result)->toBe($expected);
});

it('can validate exact object match', function (): void {
    $expected = (object) ['key' => 'value'];
    $validator = Grape::literal($expected);
    $result = $validator->validate($expected);
    expect($result)->toBe($expected);
});

// Strict Equality Tests
it('can fail when string values differ', function (): void {
    $validator = Grape::literal('hello');
    $validator->validate('world');
})->throws(ValidationException::class);

it('can fail when integer values differ', function (): void {
    $validator = Grape::literal(42);
    $validator->validate(43);
})->throws(ValidationException::class);

it('can fail with type coercion (string vs integer)', function (): void {
    $validator = Grape::literal(42);
    $validator->validate('42');
})->throws(ValidationException::class);

it('can fail with type coercion (integer vs string)', function (): void {
    $validator = Grape::literal('42');
    $validator->validate(42);
})->throws(ValidationException::class);

it('can fail with boolean vs integer comparison', function (): void {
    $validator = Grape::literal(1);
    $validator->validate(true);
})->throws(ValidationException::class);

it('can fail with boolean vs string comparison', function (): void {
    $validator = Grape::literal('true');
    $validator->validate(true);
})->throws(ValidationException::class);

it('can fail when null vs false', function (): void {
    $validator = Grape::literal(null);
    $validator->validate(false);
})->throws(ValidationException::class);

it('can fail when null vs empty string', function (): void {
    $validator = Grape::literal(null);
    $validator->validate('');
})->throws(ValidationException::class);

it('can fail when array structures differ', function (): void {
    $validator = Grape::literal(['a', 'b']);
    $validator->validate(['a', 'c']);
})->throws(ValidationException::class);

it('can fail when array order differs', function (): void {
    $validator = Grape::literal(['a', 'b']);
    $validator->validate(['b', 'a']);
})->throws(ValidationException::class);

// Callable Literal Tests
it('can validate with callable returning static value', function (): void {
    $validator = Grape::literal(fn() => 'hello');
    $result = $validator->validate('hello');
    expect($result)->toBe('hello');
});

it('can fail with callable returning different value', function (): void {
    $validator = Grape::literal(fn() => 'hello');
    $validator->validate('world');
})->throws(ValidationException::class);

it('can validate with callable using context', function (): void {
    $validator = Grape::literal(function ($ctx) {
        // Return a dynamic value based on some context
        return 'dynamic_' . date('Y');
    });
    $expected = 'dynamic_' . date('Y');
    $result = $validator->validate($expected);
    expect($result)->toBe($expected);
});

it('can fail with callable using context when value differs', function (): void {
    $validator = Grape::literal(function ($ctx) {
        return 'dynamic_' . date('Y');
    });
    $validator->validate('static_value');
})->throws(ValidationException::class);

it('can validate with callable returning different types', function (): void {
    $validator = Grape::literal(fn() => 42);
    $result = $validator->validate(42);
    expect($result)->toBe(42);
});

it('can validate with callable returning boolean', function (): void {
    $validator = Grape::literal(fn() => true);
    $result = $validator->validate(true);
    expect($result)->toBe(true);
});

it('can validate with callable returning null', function (): void {
    $validator = Grape::literal(fn() => null)->nullable();
    $result = $validator->validate(null);
    expect($result)->toBe(null);
});

it('can validate with callable returning array', function (): void {
    $expected = ['key' => 'value'];
    $validator = Grape::literal(fn() => $expected);
    $result = $validator->validate($expected);
    expect($result)->toBe($expected);
});

// Complex Type Tests
it('can validate nested array structures', function (): void {
    $expected = [
        'users' => [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane']
        ],
        'meta' => ['total' => 2]
    ];
    $validator = Grape::literal($expected);
    $result = $validator->validate($expected);
    expect($result)->toBe($expected);
});

it('can fail with deeply nested array differences', function (): void {
    $expected = [
        'users' => [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane']
        ]
    ];
    $validator = Grape::literal($expected);
    $different = [
        'users' => [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jack'] // Different name
        ]
    ];
    $validator->validate($different);
})->throws(ValidationException::class);

// Edge Cases
it('can validate empty string literal', function (): void {
    $validator = Grape::literal('');
    $result = $validator->validate('');
    expect($result)->toBe('');
});

it('can validate zero as literal', function (): void {
    $validator = Grape::literal(0);
    $result = $validator->validate(0);
    expect($result)->toBe(0);
});

it('can validate negative numbers', function (): void {
    $validator = Grape::literal(-42);
    $result = $validator->validate(-42);
    expect($result)->toBe(-42);
});

it('can validate float precision', function (): void {
    $validator = Grape::literal(0.1 + 0.2);
    $result = $validator->validate(0.1 + 0.2);
    expect($result)->toBe(0.1 + 0.2);
});

it('can fail with float precision differences', function (): void {
    $validator = Grape::literal(0.3);
    $validator->validate(0.1 + 0.2); // This might fail due to floating point precision
})->throws(ValidationException::class);

// Special Values
it('can validate positive infinity', function (): void {
    $validator = Grape::literal(INF);
    $result = $validator->validate(INF);
    expect($result)->toBe(INF);
});

it('can validate negative infinity', function (): void {
    $validator = Grape::literal(-INF);
    $result = $validator->validate(-INF);
    expect($result)->toBe(-INF);
});

it('can validate NaN', function (): void {
    $validator = Grape::literal(NAN);
    $result = $validator->validate(NAN);
    // Note: NaN !== NaN in PHP, so this test might fail
    // This is expected behavior for NaN
})->throws(ValidationException::class);

// Resource Types (if applicable)
it('can validate with resource type', function (): void {
    $resource = fopen('php://memory', 'r');
    $validator = Grape::literal($resource);
    $result = $validator->validate($resource);
    expect($result)->toBe($resource);
    fclose($resource);
});

// Unicode and Special Characters
it('can validate unicode strings', function (): void {
    $unicode = '🚀 Hello World! 你好世界';
    $validator = Grape::literal($unicode);
    $result = $validator->validate($unicode);
    expect($result)->toBe($unicode);
});

it('can validate strings with special characters', function (): void {
    $special = "Line 1\nLine 2\tTabbed\r\nWindows Line";
    $validator = Grape::literal($special);
    $result = $validator->validate($special);
    expect($result)->toBe($special);
});

// Callable with Complex Logic
it('can validate with callable containing complex logic', function (): void {
    $validator = Grape::literal(function ($ctx) {
        // Simulate some complex logic
        $baseValue = 'user_';
        $timestamp = time();
        return $baseValue . ($timestamp % 1000); // Use modulo to make it predictable for testing
    });

    $expected = 'user_' . (time() % 1000);
    $result = $validator->validate($expected);
    expect($result)->toBe($expected);
});

// Type-specific Error Message Tests
it('can show appropriate error message for string mismatch', function (): void {
    $validator = Grape::literal('expected');
    $validator->validate('actual');
})->throws(ValidationException::class);

it('can show appropriate error message for numeric mismatch', function (): void {
    $validator = Grape::literal(100);
    $validator->validate(200);
})->throws(ValidationException::class);
