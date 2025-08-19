<?php
use Potager\Grape\Exceptions\InvalidSchemaException;
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

// Basic Schema Validation Tests
it('can validate a basic schema', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
        'age' => Grape::integer(),
    ]);

    $data = ['name' => 'John', 'age' => 30];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John', 'age' => 30]);
});

it('can validate an empty schema', function (): void {
    $validator = Grape::schema([]);
    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can validate schema with no field definitions', function (): void {
    $validator = Grape::schema();
    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can fail if value is not an array', function (): void {
    $validator = Grape::schema(['name' => Grape::string()]);
    $validator->validate('not an array');
})->throws(ValidationException::class);

it('can fail if value is null', function (): void {
    $validator = Grape::schema(['name' => Grape::string()]);
    $validator->validate(null);
})->throws(ValidationException::class);

// Required Fields Tests
it('can validate when all required fields are present', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->required(),
        'age' => Grape::integer()->required(),
    ]);

    $data = ['name' => 'John', 'age' => 30];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John', 'age' => 30]);
});

it('can fail when required field is missing', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->required(),
        'age' => Grape::integer()->required(),
    ]);

    $validator->validate(['name' => 'John']);
})->throws(ValidationException::class);

it('can validate when optional fields are missing', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->required(),
        'age' => Grape::integer(), // optional
    ]);

    $data = ['name' => 'John'];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John']);
});

// Field Validation Tests
it('can apply field-specific validation rules', function (): void {
    $validator = Grape::schema([
        'email' => Grape::string()->email(),
        'age' => Grape::integer()->min(18),
    ]);

    $data = ['email' => 'john@example.com', 'age' => 25];
    $result = $validator->validate($data);

    expect($result)->toBe(['email' => 'john@example.com', 'age' => 25]);
});

it('can fail when field validation fails', function (): void {
    $validator = Grape::schema([
        'age' => Grape::integer()->min(18),
    ]);

    $validator->validate(['age' => 16]);
})->throws(ValidationException::class);

it('can transform field values according to validator rules', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->trim()->uppercase(),
        'age' => Grape::string(), // Will convert to string
    ]);

    $data = ['name' => '  john  ', 'age' => 30];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'JOHN', 'age' => '30']);
});

// Nested Schema Tests
it('can validate nested schemas', function (): void {
    $validator = Grape::schema([
        'user' => Grape::schema([
            'name' => Grape::string(),
            'age' => Grape::integer(),
        ]),
        'active' => Grape::boolean(),
    ]);

    $data = [
        'user' => ['name' => 'John', 'age' => 30],
        'active' => true,
    ];

    $result = $validator->validate($data);
    expect($result)->toBe($data);
});

it('can fail when nested schema validation fails', function (): void {
    $validator = Grape::schema([
        'user' => Grape::schema([
            'name' => Grape::string()->required(),
            'age' => Grape::integer(),
        ]),
    ]);

    $validator->validate(['user' => ['age' => 30]]);
})->throws(ValidationException::class);

// Unknown Properties Handling Tests - Discard Strategy (Default)
it('can discard unknown properties by default', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ]);

    $data = ['name' => 'John', 'unknown' => 'value'];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John']);
});

it('can discard multiple unknown properties', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ]);

    $data = [
        'name' => 'John',
        'unknown1' => 'value1',
        'unknown2' => 'value2',
        'unknown3' => 'value3',
    ];

    $result = $validator->validate($data);
    expect($result)->toBe(['name' => 'John']);
});

// Unknown Properties Handling Tests - Keep Strategy
it('can keep unknown properties when configured', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ])->allowUnknownProperties();

    $data = ['name' => 'John', 'extra' => 'data'];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John', 'extra' => 'data']);
});

it('can keep multiple unknown properties', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ])->allowUnknownProperties(true);

    $data = [
        'name' => 'John',
        'extra1' => 'data1',
        'extra2' => 'data2',
    ];

    $result = $validator->validate($data);
    expect($result)->toBe($data);
});

it('can toggle allow unknown properties off', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ])->allowUnknownProperties(false);

    $data = ['name' => 'John', 'extra' => 'data'];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John']);
});

// Unknown Properties Handling Tests - Reject Strategy
it('can reject unknown properties when configured', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ])->rejectUnknownProperties();

    $validator->validate(['name' => 'John', 'unknown' => 'value']);
})->throws(ValidationException::class);

it('can reject multiple unknown properties', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ])->rejectUnknownProperties(true);

    $validator->validate([
        'name' => 'John',
        'unknown1' => 'value1',
        'unknown2' => 'value2',
    ]);
})->throws(ValidationException::class);

it('can toggle reject unknown properties off', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ])->rejectUnknownProperties(false);

    $data = ['name' => 'John', 'extra' => 'data'];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John']);
});

// Schema Definition Validation Tests
it('can fail with invalid schema definition (list array)', function (): void {
    expect(fn() => Grape::schema([
        Grape::string(), // This is a list array, not associative
        Grape::integer(),
    ])->validate(['test', 123]))
        ->toThrow(InvalidSchemaException::class, 'Schema definition must be an associative array');
});

it('can fail with invalid schema definition (non-validator value)', function (): void {
    expect(fn() => Grape::schema([
        'name' => 'not a validator', // String instead of validator
    ])->validate(['name' => 'John']))
        ->toThrow(InvalidSchemaException::class, "Schema field 'name' must be an instance of AbstractValidator");
});

it('can fail with invalid schema definition (object value)', function (): void {
    expect(fn() => Grape::schema([
        'name' => new stdClass(), // Object but not validator
    ])->validate(['name' => 'John']))
        ->toThrow(InvalidSchemaException::class, "Schema field 'name' must be an instance of AbstractValidator, got stdClass");
});

it('can fail with invalid schema definition (null value)', function (): void {
    expect(fn() => Grape::schema([
        'name' => null, // Null instead of validator
    ])->validate(['name' => 'John']))
        ->toThrow(InvalidSchemaException::class, "Schema field 'name' must be an instance of AbstractValidator, got NULL");
});

// Nullable and Required Combinations
it('can validate nullable required field when provided', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->nullable()->required(),
    ]);

    $result = $validator->validate(['name' => 'John']);
    expect($result)->toBe(['name' => 'John']);
});

it('can validate nullable required field when null', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->nullable()->required(),
    ]);

    $result = $validator->validate(['name' => null]);
    expect($result)->toBe(['name' => null]);
});

it('can fail when nullable required field is missing', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->nullable()->required(),
    ]);

    $validator->validate([]);
})->throws(ValidationException::class);

// Complex Validation Scenarios
it('can validate complex nested structure', function (): void {
    $validator = Grape::schema([
        'user' => Grape::schema([
            'profile' => Grape::schema([
                'name' => Grape::string()->required(),
                'email' => Grape::string()->email(),
            ]),
            'preferences' => Grape::schema([
                'notifications' => Grape::boolean(),
                'theme' => Grape::string(),
            ]),
        ])->required(),
        'metadata' => Grape::schema([
            'created_at' => Grape::string(),
            'version' => Grape::integer(),
        ]),
    ]);

    $data = [
        'user' => [
            'profile' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
            'preferences' => [
                'notifications' => true,
                'theme' => 'dark',
            ],
        ],
        'metadata' => [
            'created_at' => '2023-01-01',
            'version' => 1,
        ],
    ];

    $result = $validator->validate($data);
    expect($result)->toBe($data);
});

it('can validate array of schemas', function (): void {
    $userSchema = Grape::schema([
        'name' => Grape::string()->required(),
        'age' => Grape::integer(),
    ]);

    $validator = Grape::collection($userSchema);

    $data = [
        ['name' => 'John', 'age' => 30],
        ['name' => 'Jane', 'age' => 25],
    ];

    $result = $validator->validate($data);
    expect($result)->toBe($data);
});

// Edge Cases
it('can handle empty data with only optional fields', function (): void {
    $validator = Grape::schema([
        'optional1' => Grape::string(),
        'optional2' => Grape::integer(),
    ]);

    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can handle data with only unknown fields when allowing them', function (): void {
    $validator = Grape::schema([])->allowUnknownProperties();

    $data = ['unknown1' => 'value1', 'unknown2' => 'value2'];
    $result = $validator->validate($data);

    expect($result)->toBe($data);
});

it('can handle data with only unknown fields when discarding them', function (): void {
    $validator = Grape::schema([]);

    $data = ['unknown1' => 'value1', 'unknown2' => 'value2'];
    $result = $validator->validate($data);

    // When schema is empty, there are no field definitions to check against,
    // so unknown fields are preserved
    expect($result)->toBe($data);
});

it('can discard unknown fields when schema has defined fields', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string(),
    ]);

    $data = ['unknown1' => 'value1', 'unknown2' => 'value2'];
    $result = $validator->validate($data);

    // When schema has defined fields, unknown fields should be discarded
    expect($result)->toBe([]);
});

it('can handle mixed known and unknown fields with transformations', function (): void {
    $validator = Grape::schema([
        'name' => Grape::string()->trim()->uppercase(),
    ])->allowUnknownProperties();

    $data = ['name' => '  john  ', 'extra' => 'preserved'];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'JOHN', 'extra' => 'preserved']);
});

// Method Chaining Tests
it('can chain multiple unknown property configurations', function (): void {
    $validator = Grape::schema(['name' => Grape::string()])
        ->allowUnknownProperties()
        ->rejectUnknownProperties()
        ->allowUnknownProperties(false);

    $data = ['name' => 'John', 'extra' => 'data'];
    $result = $validator->validate($data);

    expect($result)->toBe(['name' => 'John']);
});
