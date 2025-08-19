<?php

use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

// Basic Tuple Validation Tests
it('can validate a basic tuple with matching elements', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer(),
        Grape::boolean()
    ]);
    $result = $validator->validate(['hello', 42, true]);
    expect($result)->toBe(['hello', 42, true]);
});

it('can validate an empty tuple', function (): void {
    $validator = Grape::tuple([]);
    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can fail if value is not an array', function (): void {
    $validator = Grape::tuple([Grape::string()]);
    $validator->validate('not an array');
})->throws(ValidationException::class);

it('can fail if value is null', function (): void {
    $validator = Grape::tuple([Grape::string()]);
    $validator->validate(null);
})->throws(ValidationException::class);

it('can fail if tuple has fewer elements than required', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer(),
        Grape::boolean()
    ]);
    $validator->validate(['hello', 42]);
})->throws(ValidationException::class);

it('can normalize indexed arrays to sequential arrays', function (): void {
    $validator = Grape::tuple([Grape::string(), Grape::integer()]);
    $result = $validator->validate([0 => 'hello', 1 => 42]);
    expect($result)->toBe(['hello', 42]);
});

// Element Validation Tests
it('can validate each element against its corresponding validator', function (): void {
    $validator = Grape::tuple([
        Grape::string()->minLength(3),
        Grape::integer()->positive(),
        Grape::boolean()
    ]);
    $result = $validator->validate(['hello', 42, true]);
    expect($result)->toBe(['hello', 42, true]);
});

it('can fail when first element validation fails', function (): void {
    $validator = Grape::tuple([
        Grape::string(true),
        Grape::integer(true)
    ]);
    $validator->validate([123, 42]);
})->throws(ValidationException::class);

it('can fail when second element validation fails', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer()
    ]);
    $validator->validate(['hello', 'not a number']);
})->throws(ValidationException::class);

it('can apply transformations to tuple elements', function (): void {
    $validator = Grape::tuple([
        Grape::string()->trim(),
        Grape::integer(),
        Grape::string()->uppercase()
    ]);
    $result = $validator->validate(['  hello  ', 42, 'world']);
    expect($result)->toBe(['hello', 42, 'WORLD']);
});

// Unknown Items Strategy Tests
it('can discard unknown items by default', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer()
    ]);
    $result = $validator->validate(['hello', 42, 'extra', 99]);
    expect($result)->toBe(['hello', 42]);
});

it('can explicitly discard unknown items', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer()
    ])->discardUnknownItems();
    $result = $validator->validate(['hello', 42, 'extra', 99]);
    expect($result)->toBe(['hello', 42]);
});

it('can keep unknown items when configured', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer()
    ])->allowUnknownItems();
    $result = $validator->validate(['hello', 42, 'extra', 99]);
    expect($result)->toBe(['hello', 42, 'extra', 99]);
});

it('can reject unknown items when configured', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer()
    ])->rejectUnknownItems();
    $validator->validate(['hello', 42, 'extra']);
})->throws(ValidationException::class);

it('can reject multiple unknown items', function (): void {
    $validator = Grape::tuple([
        Grape::string()
    ])->rejectUnknownItems();
    $validator->validate(['hello', 'extra1', 'extra2']);
})->throws(ValidationException::class);

// Distinct/Unique Elements Tests
it('can validate distinct elements', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::string(),
        Grape::string()
    ])->distinct();
    $result = $validator->validate(['hello', 'world', 'test']);
    expect($result)->toBe(['hello', 'world', 'test']);
});

it('can fail when elements are not distinct', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::string(),
        Grape::string()
    ])->distinct();
    $validator->validate(['hello', 'world', 'hello']);
})->throws(ValidationException::class);

it('can validate distinct elements with resolver function', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::string(),
        Grape::string()
    ])->distinct(fn($value) => strtolower($value));
    $result = $validator->validate(['Hello', 'WORLD', 'test']);
    expect($result)->toBe(['Hello', 'WORLD', 'test']);
});

it('can fail when resolved elements are not distinct', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::string()
    ])->distinct(fn($value) => strtolower($value));
    $validator->validate(['Hello', 'HELLO']);
})->throws(ValidationException::class);

// Nested Tuple Tests
it('can validate nested tuples', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::tuple([
            Grape::integer(),
            Grape::boolean()
        ])
    ]);
    $result = $validator->validate(['hello', [42, true]]);
    expect($result)->toBe(['hello', [42, true]]);
});

it('can fail when nested tuple validation fails', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::tuple([
            Grape::integer(),
            Grape::boolean()
        ])
    ]);
    $validator->validate(['hello', [42, 'not a boolean']]);
})->throws(ValidationException::class);

// Complex Validation Tests
it('can validate tuples with complex nested structures', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::collection(Grape::integer()),
        Grape::schema([
            'name' => Grape::string(),
            'age' => Grape::integer()
        ])
    ]);
    $result = $validator->validate([
        'title',
        [1, 2, 3],
        ['name' => 'John', 'age' => 30]
    ]);
    expect($result)->toBe([
        'title',
        [1, 2, 3],
        ['name' => 'John', 'age' => 30]
    ]);
});

// Constructor Validation Tests
it('can throw exception when element validator is not AbstractValidator', function (): void {
    expect(function () {
        Grape::tuple(['not a validator']);
    })->toThrow(InvalidArgumentException::class, 'Element validator at index 0 must be an instance of AbstractValidator, got string');
});

it('can throw exception when element validator is wrong object type', function (): void {
    expect(function () {
        Grape::tuple([new stdClass()]);
    })->toThrow(InvalidArgumentException::class, 'Element validator at index 0 must be an instance of AbstractValidator, got stdClass');
});

// Method Chaining Tests
it('can chain multiple configuration methods', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::string()
    ])
        ->allowUnknownItems()
        ->distinct();

    $result = $validator->validate(['hello', 'world', 'extra']);
    expect($result)->toBe(['hello', 'world', 'extra']);
});

it('can chain distinct with reject unknown items', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::string()
    ])
        ->rejectUnknownItems()
        ->distinct();

    $validator->validate(['hello', 'world', 'extra']);
})->throws(ValidationException::class);

// Edge Cases
it('can handle associative arrays by converting to indexed', function (): void {
    $validator = Grape::tuple([
        Grape::string(),
        Grape::integer()
    ]);
    $result = $validator->validate(['first' => 'hello', 'second' => 42]);
    expect($result)->toBe(['hello', 42]);
});

it('can validate single element tuple', function (): void {
    $validator = Grape::tuple([Grape::string()]);
    $result = $validator->validate(['hello']);
    expect($result)->toBe(['hello']);
});

it('can handle boolean values in distinct check', function (): void {
    $validator = Grape::tuple([
        Grape::boolean(),
        Grape::boolean()
    ])->distinct();
    $result = $validator->validate([true, false]);
    expect($result)->toBe([true, false]);
});

it('can handle null values in tuple elements', function (): void {
    $validator = Grape::tuple([
        Grape::string()->nullable(),
        Grape::integer()
    ]);
    $result = $validator->validate([null, 42]);
    expect($result)->toBe([null, 42]);
});

// Strategy Switching Tests
it('can switch from discard to allow unknown items', function (): void {
    $validator = Grape::tuple([Grape::string()])->discardUnknownItems();
    $result1 = $validator->validate(['hello', 'extra']);
    expect($result1)->toBe(['hello']);

    $validator->allowUnknownItems();
    $result2 = $validator->validate(['hello', 'extra']);
    expect($result2)->toBe(['hello', 'extra']);
});

it('can switch from allow to reject unknown items', function (): void {
    $validator = Grape::tuple([Grape::string()])->allowUnknownItems();
    $result = $validator->validate(['hello', 'extra']);
    expect($result)->toBe(['hello', 'extra']);

    $validator->rejectUnknownItems();
    expect(function () use ($validator) {
        $validator->validate(['hello', 'extra']);
    })->toThrow(ValidationException::class);
});
