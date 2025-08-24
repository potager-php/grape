<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

// Basic Array Validation Tests
it('can validate a basic array', function (): void {
    $validator = Grape::collection();
    $result = $validator->validate([1, 2, 3]);
    expect($result)->toBe([1, 2, 3]);
});

it('can validate an empty array', function (): void {
    $validator = Grape::collection();
    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can fail if value is not an array', function (): void {
    $validator = Grape::collection();
    $validator->validate('not an array');
})->throws(ValidationException::class);

it('can fail if value is null and expecting array', function (): void {
    $validator = Grape::collection();
    $validator->validate(null);
})->throws(ValidationException::class);

it('can fail if value is object and expecting array', function (): void {
    $validator = Grape::collection();
    $validator->validate((object) ['key' => 'value']);
})->throws(ValidationException::class);

// Item Validator Tests
it('can validate array items with string validator', function (): void {
    $validator = Grape::collection(Grape::string());
    $result = $validator->validate(['hello', 'world']);
    expect($result)->toBe(['hello', 'world']);
});

it('can validate array items with integer validator', function (): void {
    $validator = Grape::collection(Grape::integer());
    $result = $validator->validate([1, 2, 3]);
    expect($result)->toBe([1, 2, 3]);
});

it('can transform array items using item validator', function (): void {
    $validator = Grape::collection(Grape::string()->trim());
    $result = $validator->validate(['  hello  ', '  world  ']);
    expect($result)->toBe(['hello', 'world']);
});

it('can fail when array item fails validation', function (): void {
    $validator = Grape::collection(Grape::integer());
    $validator->validate([1, 'not a number', 3]);
})->throws(ValidationException::class);

it('can validate nested arrays', function (): void {
    $validator = Grape::collection(Grape::collection(Grape::integer()));
    $result = $validator->validate([[1, 2], [3, 4]]);
    expect($result)->toBe([[1, 2], [3, 4]]);
});

// Length Constraint Tests
it('can validate minimum array length', function (): void {
    $validator = Grape::collection()->minLength(2);
    $result = $validator->validate([1, 2, 3]);
    expect($result)->toBe([1, 2, 3]);
});

it('can fail if array is shorter than minimum', function (): void {
    $validator = Grape::collection()->minLength(3);
    $validator->validate([1, 2]);
})->throws(ValidationException::class);

it('can validate maximum array length', function (): void {
    $validator = Grape::collection()->maxLength(3);
    $result = $validator->validate([1, 2]);
    expect($result)->toBe([1, 2]);
});

it('can fail if array is longer than maximum', function (): void {
    $validator = Grape::collection()->maxLength(2);
    $validator->validate([1, 2, 3]);
})->throws(ValidationException::class);

it('can validate array length within range', function (): void {
    $validator = Grape::collection()->minLength(2)->maxLength(4);
    $result = $validator->validate([1, 2, 3]);
    expect($result)->toBe([1, 2, 3]);
});

it('can validate array with fixed length', function () {
    $validator = Grape::collection()->fixedLength(3);
    $result = $validator->validate([1, 2, 3]);
    expect($result)->toBe([1, 2, 3]);
});

it('can fail if array does not match fixed length', function () {
    $validator = Grape::collection()->fixedLength(3);
    $validator->validate([1, 2]);
})->throws(ValidationException::class);

// Empty/Not Empty Tests
it('can validate empty array with empty constraint', function (): void {
    $validator = Grape::collection()->empty();
    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can fail if array is not empty when it should be', function (): void {
    $validator = Grape::collection()->empty();
    $validator->validate([1, 2]);
})->throws(ValidationException::class);

it('can validate non-empty array with notEmpty constraint', function (): void {
    $validator = Grape::collection()->notEmpty();
    $result = $validator->validate([1, 2]);
    expect($result)->toBe([1, 2]);
});

it('can fail if array is empty when it should not be', function (): void {
    $validator = Grape::collection()->notEmpty();
    $validator->validate([]);
})->throws(ValidationException::class);

// Distinct Values Tests
it('can validate array with distinct values', function (): void {
    $validator = Grape::collection()->distinct();
    $result = $validator->validate([1, 2, 3]);
    expect($result)->toBe([1, 2, 3]);
});

it('can fail if array has duplicate values', function (): void {
    $validator = Grape::collection()->distinct();
    $validator->validate([1, 2, 2, 3]);
})->throws(ValidationException::class);

it('can validate distinct values with custom resolver', function (): void {
    $validator = Grape::collection()->distinct(function ($item): mixed {
        return $item['id'];
    });
    $result = $validator->validate([
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Jane']
    ]);
    expect($result)->toBe([
        ['id' => 1, 'name' => 'John'],
        ['id' => 2, 'name' => 'Jane']
    ]);
});

it('can fail with custom resolver when duplicates found', function (): void {
    $validator = Grape::collection()->distinct(function ($item, $index) {
        return $item['id'];
    });
    $validator->validate([
        ['id' => 1, 'name' => 'John'],
        ['id' => 1, 'name' => 'Jane']
    ]);
})->throws(ValidationException::class);

// Normalize Tests
it('can normalize array with sequential keys', function (): void {
    $validator = Grape::collection()->normalize();
    $result = $validator->validate([2 => 'a', 5 => 'b', 'key' => 'c']);
    expect($result)->toBe(['a', 'b', 'c']);
});

it('can normalize already sequential array', function (): void {
    $validator = Grape::collection()->normalize();
    $result = $validator->validate(['a', 'b', 'c']);
    expect($result)->toBe(['a', 'b', 'c']);
});

// Compact Tests
it('can drop empty strings and null values', function (): void {
    $validator = Grape::collection()->compact();
    $result = $validator->validate(['hello', '', null, 'world', 0, false]);
    expect($result)->toBe([0 => 'hello', 3 => 'world', 4 => 0, 5 => false]);
});

it('can preserve other falsy values when dropping empty', function (): void {
    $validator = Grape::collection()->compact();
    $result = $validator->validate([0, false, [], '']);
    expect($result)->toBe([0 => 0, 1 => false, 2 => []]);
});

// Skip Invalids Tests
it('can drop invalid items when skipInvalids is enabled', function (): void {
    $validator = Grape::collection(Grape::integer())->skipInvalids();
    $result = $validator->validate([1, 'invalid', 3, 'also invalid', 5]);
    expect($result)->toBe([0 => 1, 2 => 3, 4 => 5]);
});

it('can fail validation when skipInvalids is disabled (default)', function (): void {
    $validator = Grape::collection(Grape::integer());
    $validator->validate([1, 'invalid', 3]);
})->throws(ValidationException::class);

it('can explicitly disable skipInvalids', function (): void {
    $validator = Grape::collection(Grape::integer())->skipInvalids(false);
    $validator->validate([1, 'invalid', 3]);
})->throws(ValidationException::class);

// Combined Functionality Tests
it('can combine multiple array validations', function (): void {
    $validator = Grape::collection(Grape::string()->trim())
        ->minLength(2)
        ->maxLength(5)
        ->distinct()
        ->notEmpty();

    $result = $validator->validate(['  hello  ', '  world  ', '  test  ']);
    expect($result)->toBe(['hello', 'world', 'test']);
});

it('can normalize and compact empty values together', function (): void {
    $validator = Grape::collection()->compact()->normalize();
    $result = $validator->validate([2 => 'a', 5 => '', 'key' => 'b', 10 => null, 15 => 'c']);
    expect($result)->toBe(['a', 'b', 'c']);
});

it('can validate complex nested structure', function (): void {
    $userValidator = Grape::collection()->minLength(1);
    $validator = Grape::collection($userValidator)->minLength(1);

    $result = $validator->validate([
        ['John', 'Jane'],
        ['Bob']
    ]);

    expect($result)->toBe([
        ['John', 'Jane'],
        ['Bob']
    ]);
});

// Edge Cases
it('can handle associative arrays', function (): void {
    $validator = Grape::collection();
    $result = $validator->validate(['key1' => 'value1', 'key2' => 'value2']);
    expect($result)->toBe(['key1' => 'value1', 'key2' => 'value2']);
});

it('can validate array with mixed types when no item validator', function (): void {
    $validator = Grape::collection();
    $result = $validator->validate([1, 'string', true, null, [1, 2]]);
    expect($result)->toBe([1, 'string', true, null, [1, 2]]);
});

it('can handle zero as minimum length', function (): void {
    $validator = Grape::collection()->minLength(0);
    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can handle zero as maximum length', function (): void {
    $validator = Grape::collection()->maxLength(0);
    $result = $validator->validate([]);
    expect($result)->toBe([]);
});

it('can fail when minimum is zero but array is not empty for max(0)', function (): void {
    $validator = Grape::collection()->maxLength(0);
    $validator->validate([1]);
})->throws(ValidationException::class);

// Test method chaining
it('can chain multiple constraints fluently', function (): void {
    $validator = Grape::collection(Grape::string())
        ->minLength(1)
        ->maxLength(10)
        ->notEmpty()
        ->distinct();

    $result = $validator->validate(['unique1', 'unique2', 'unique3']);
    expect($result)->toBe(['unique1', 'unique2', 'unique3']);
});

// Test with different item validators
it('can validate boolean array items', function (): void {
    $validator = Grape::collection(Grape::boolean());
    $result = $validator->validate([true, false, true]);
    expect($result)->toBe([true, false, true]);
});

it('can validate float array items', function (): void {
    $validator = Grape::collection(Grape::float());
    $result = $validator->validate([1.5, 2.7, 3.14]);
    expect($result)->toBe([1.5, 2.7, 3.14]);
});

// Test error propagation from nested validators
it('can propagate nested validation errors correctly', function (): void {
    $validator = Grape::collection(Grape::string()->minLength(6));
    $validator->validate(['valid_string', 'short']);
})->throws(ValidationException::class);

// Test preservation of keys in various operations
it('can preserve associative keys when not normalizing', function (): void {
    $validator = Grape::collection(Grape::string()->trim());
    $result = $validator->validate(['first' => '  hello  ', 'second' => '  world  ']);
    expect($result)->toBe(['first' => 'hello', 'second' => 'world']);
});

it('can remove associative keys when normalizing', function (): void {
    $validator = Grape::collection()->normalize();
    $result = $validator->validate(['first' => 'hello', 'second' => 'world']);
    expect($result)->toBe(['hello', 'world']);
});

// DEEPLY NESTED ARRAYS TESTS
it('can validate three-level nested arrays', function (): void {
    $validator = Grape::collection(
        Grape::collection(
            Grape::collection(Grape::integer())
        )
    );

    $result = $validator->validate([
        [[1, 2], [3, 4]],
        [[5, 6], [7, 8]],
        [[9, 10]]
    ]);

    expect($result)->toBe([
        [[1, 2], [3, 4]],
        [[5, 6], [7, 8]],
        [[9, 10]]
    ]);
});

it('can validate four-level nested arrays with constraints', function (): void {
    $validator = Grape::collection(
        Grape::collection(
            Grape::collection(
                Grape::collection(Grape::string()->minLength(2))
            )->minLength(1)
        )->minLength(1)
    )->minLength(1);

    $result = $validator->validate([
        [
            [
                ['hello', 'world']
            ]
        ],
        [
            [
                ['test', 'data'],
                ['more', 'items']
            ]
        ]
    ]);

    expect($result)->toBe([
        [
            [
                ['hello', 'world']
            ]
        ],
        [
            [
                ['test', 'data'],
                ['more', 'items']
            ]
        ]
    ]);
});

it('can fail validation in deeply nested structures', function (): void {
    $validator = Grape::collection(
        Grape::collection(
            Grape::collection(Grape::integer())
        )
    );

    $validator->validate([
        [[1, 2], [3, 'not a number']],
        [[5, 6]]
    ]);
})->throws(ValidationException::class);

it('can handle mixed depth arrays with transformations', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::string()->trim()->uppercase())
    )->normalize();

    $result = $validator->validate([
        2 => ['  hello  ', '  world  '],
        5 => ['  test  ']
    ]);

    expect($result)->toBe([
        ['HELLO', 'WORLD'],
        ['TEST']
    ]);
});

// COMPLEX ITEM VALIDATORS TESTS
it('can validate arrays with complex chained string validators', function (): void {
    $validator = Grape::collection(
        Grape::string()
            ->trim()
            ->minLength(3)
            ->maxLength(20)
            ->lowercase()
    );

    $result = $validator->validate(['  HELLO  ', '  WORLD  ', '  TEST  ']);
    expect($result)->toBe(['hello', 'world', 'test']);
});

it('can validate arrays with complex number validators', function (): void {
    $validator = Grape::collection(
        Grape::integer()
            ->positive()
            ->min(1)
            ->max(100)
    );

    $result = $validator->validate([5, 25, 50, 75]);
    expect($result)->toBe([5, 25, 50, 75]);
});

it('can fail with complex number validator constraints', function (): void {
    $validator = Grape::collection(
        Grape::integer()
            ->positive()
            ->min(10)
            ->max(50)
    );

    $validator->validate([5, 25, 75]); // 5 < 10, 75 > 50
})->throws(ValidationException::class);

it('can validate arrays with boolean validators and transformations', function (): void {
    $validator = Grape::collection(Grape::boolean());

    $result = $validator->validate([1, 0, "true", "false", "yes", "no"]);
    expect($result)->toBe([true, false, true, false, true, false]);
});

// ADVANCED NESTED SCENARIOS
it('can validate user profile arrays with nested validations', function (): void {
    // Simulating a user profile validator
    $userValidator = Grape::collection()
        ->minLength(4) // name, email, age, address
        ->maxLength(5); // optional phone

    $validator = Grape::collection($userValidator)->minLength(1);

    $result = $validator->validate([
        ['John Doe', 'john@example.com', 30, ['123 Main St', 'NYC', '10001']],
        ['Jane Smith', 'jane@example.com', 25, ['456 Oak Ave', 'LA', '90210', 'USA'], '555-1234']
    ]);

    expect($result)->toHaveCount(2);
    expect($result[0])->toHaveCount(4);
    expect($result[1])->toHaveCount(5);
});

it('can validate matrix-like structures with numeric constraints', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::float()->min(-100)->max(100))
            ->minLength(3)
            ->maxLength(3) // Each row must have exactly 3 columns
    )->minLength(2); // At least 2 rows

    $result = $validator->validate([
        [1.5, 2.7, -3.14],
        [4.2, -5.8, 6.9],
        [7.1, 8.3, -9.7]
    ]);

    expect($result)->toBe([
        [1.5, 2.7, -3.14],
        [4.2, -5.8, 6.9],
        [7.1, 8.3, -9.7]
    ]);
});

it('can validate and transform nested data with skipInvalids', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::integer()->positive())
            ->notEmpty()
            ->skipInvalids()
    )->skipInvalids()->normalize();

    $result = $validator->validate([
        [1, -2, 3, 'invalid', 5], // -2 and 'invalid' should be dropped
        ['all', 'invalid', 'items'], // entire array should be dropped
        [10, 20, 30] // valid array
    ]);

    expect($result)->toBe([
        [0 => 1, 2 => 3, 4 => 5],
        [10, 20, 30]
    ]);
});

it('can validate nested arrays with distinct constraints', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::integer())
            ->distinct()
            ->minLength(2)
    )->distinct(function ($item) {
        return count($item); // Each sub-array must have different length
    });

    $result = $validator->validate([
        [1, 2], // length 2
        [3, 4, 5], // length 3
        [6, 7, 8, 9] // length 4
    ]);

    expect($result)->toBe([
        [1, 2],
        [3, 4, 5],
        [6, 7, 8, 9]
    ]);
});

it('can fail nested distinct validation', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::integer())
            ->distinct()
    )->distinct(function ($item) {
        return count($item);
    });

    $validator->validate([
        [1, 2], // length 2
        [3, 4], // length 2 (duplicate)
        [5, 6, 7] // length 3
    ]);
})->throws(ValidationException::class);

// COMPLEX TRANSFORMATION SCENARIOS
it('can handle complex nested transformations', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::string()->trim()->lowercase()->nullable())
            ->compact()
            ->normalize()
    )->normalize();

    $result = $validator->validate([
        2 => ['  HELLO  ', '', '  WORLD  ', null],
        5 => ['  TEST  ', '  DATA  ']
    ]);

    expect($result)->toBe([
        ['hello', 'world'],
        ['test', 'data']
    ]);
});

it('can validate nested arrays with mixed constraints and transformations', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::string()->minLength(2)->uppercase())
            ->minLength(1)
            ->distinct()
    )->minLength(2)
        ->maxLength(5);

    $result = $validator->validate([
        ['hello', 'world'],
        ['test'],
        ['data', 'validation']
    ]);

    expect($result)->toBe([
        ['HELLO', 'WORLD'],
        ['TEST'],
        ['DATA', 'VALIDATION']
    ]);
});

// EDGE CASES WITH NESTED STRUCTURES
it('can handle empty nested arrays correctly', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::string())
            ->minLength(0) // Allow empty sub-arrays
    );

    $result = $validator->validate([
        [],
        ['test'],
        []
    ]);

    expect($result)->toBe([
        [],
        ['test'],
        []
    ]);
});

it('can validate deeply nested mixed-type structures', function (): void {
    // Array of arrays containing mixed string/integer pairs
    $pairValidator = Grape::collection()
        ->minLength(2)
        ->maxLength(2); // Exactly 2 items per pair

    $validator = Grape::collection($pairValidator);

    $result = $validator->validate([
        ['name', 'John'],
        ['age', 30],
        ['city', 'NYC']
    ]);

    expect($result)->toBe([
        ['name', 'John'],
        ['age', 30],
        ['city', 'NYC']
    ]);
});

it('can validate and transform complex CSV-like data structures', function (): void {
    $rowValidator = Grape::collection(Grape::string()->trim())
        ->minLength(3) // At least 3 columns
        ->normalize();

    $csvValidator = Grape::collection($rowValidator)
        ->minLength(1) // At least header row
        ->normalize();

    $result = $csvValidator->validate([
        ['  Name  ', '  Age  ', '  City  '],
        ['  John  ', '  30   ', '  NYC   '],
        ['  Jane  ', '  25   ', '  LA    ']
    ]);

    expect($result)->toBe([
        ['Name', 'Age', 'City'],
        ['John', '30', 'NYC'],
        ['Jane', '25', 'LA']
    ]);
});

// PERFORMANCE AND STRESS TESTS
it('can handle large nested arrays efficiently', function (): void {
    $validator = Grape::collection(
        Grape::collection(Grape::integer())
            ->minLength(1)
            ->maxLength(10)
    );

    $largeData = [];
    for ($i = 0; $i < 100; $i++) {
        $largeData[] = range(1, 5);
    }

    $result = $validator->validate($largeData);
    expect($result)->toHaveCount(100);
    expect($result[0])->toBe([1, 2, 3, 4, 5]);
});

it('can validate complex tree-like structures', function (): void {
    // Simulating a tree node: [value, [child1, child2, ...]]
    $nodeValidator = Grape::collection()
        ->minLength(2)
        ->maxLength(2); // [value, children_array]

    $validator = Grape::collection($nodeValidator);

    $result = $validator->validate([
        [
            'root',
            [
                ['child1', []],
                [
                    'child2',
                    [
                        ['grandchild1', []],
                        ['grandchild2', []]
                    ]
                ]
            ]
        ]
    ]);

    expect($result)->toHaveCount(1);
    expect($result[0][0])->toBe('root');
    expect($result[0][1])->toBeArray();
});

// VALIDATION WITH CUSTOM RESOLVERS AND COMPLEX LOGIC
it('can validate arrays with complex distinct resolvers', function (): void {
    $validator = Grape::collection()->distinct(function ($item, $index) {
        // Custom logic: consider arrays with same sum as duplicates
        return is_array($item) ? array_sum($item) : $item;
    });

    $result = $validator->validate([
        [1, 2, 3], // sum = 6
        [2, 4],    // sum = 6 - should fail
        [1, 1, 1]  // sum = 3
    ]);
})->throws(ValidationException::class);

it('can validate nested structures with conditional logic', function (): void {
    // Validate array of user data where each user has different required fields
    $validator = Grape::collection(
        Grape::collection()->minLength(2) // At least name and type
    );

    $result = $validator->validate([
        ['John', 'admin', 'extra_field'],
        ['Jane', 'user'],
        ['Bob', 'guest', 'another_field', 'more_data']
    ]);

    expect($result)->toHaveCount(3);
    expect($result[0])->toHaveCount(3);
    expect($result[1])->toHaveCount(2);
    expect($result[2])->toHaveCount(4);
});

// KEY VALIDATION TESTS
it('can validate array keys with custom resolver', function (): void {
    $validator = Grape::collection()->validateKeys(function ($key, $ctx) {
        if (!is_string($key)) {
            $ctx->report('Key must be a string', 'invalid_key');
        }
        if (strlen($key) < 3) {
            $ctx->report('Key must be at least 3 characters', 'key_too_short');
        }
    });

    $result = $validator->validate(['name' => 'John', 'email' => 'john@example.com']);
    expect($result)->toBe(['name' => 'John', 'email' => 'john@example.com']);
});

it('can fail key validation when keys are invalid', function (): void {
    $validator = Grape::collection()->validateKeys(function ($key, $ctx) {
        if (!is_string($key)) {
            $ctx->report('Key must be a string', 'invalid_key');
        }
    });

    $validator->validate([0 => 'value1', 'valid_key' => 'value2']);
})->throws(ValidationException::class);

it('can fail key validation with length constraints', function (): void {
    $validator = Grape::collection()->validateKeys(function ($key, $ctx) {
        if (is_string($key) && strlen($key) < 3) {
            $ctx->report('Key must be at least 3 characters', 'key_too_short');
        }
    });

    $validator->validate(['ab' => 'value1', 'valid_key' => 'value2']);
})->throws(ValidationException::class);

it('can validate keys with pattern matching', function (): void {
    $validator = Grape::collection()->validateKeys(function ($key, $ctx) {
        if (!preg_match('/^[a-z_]+$/', $key)) {
            $ctx->report('Key must contain only lowercase letters and underscores', 'invalid_key_pattern');
        }
    });

    $result = $validator->validate(['user_name' => 'John', 'email_address' => 'john@example.com']);
    expect($result)->toBe(['user_name' => 'John', 'email_address' => 'john@example.com']);
});

it('can fail key validation with pattern mismatch', function (): void {
    $validator = Grape::collection()->validateKeys(function ($key, $ctx) {
        if (!preg_match('/^[a-z_]+$/', $key)) {
            $ctx->report('Key must contain only lowercase letters and underscores', 'invalid_key_pattern');
        }
    });

    $validator->validate(['userName' => 'John', 'email_address' => 'john@example.com']);
})->throws(ValidationException::class);

it('can validate keys with complex business logic', function (): void {
    $requiredKeys = ['id', 'name', 'email'];
    $validator = Grape::collection()->validateKeys(function ($key, $ctx) use ($requiredKeys) {
        if (in_array($key, $requiredKeys)) {
            // Required keys are always valid
            return;
        }

        // Optional keys must start with 'opt_'
        if (!str_starts_with($key, 'opt_')) {
            $ctx->report('Optional keys must start with "opt_"', 'invalid_optional_key');
        }
    });

    $result = $validator->validate([
        'id' => 1,
        'name' => 'John',
        'email' => 'john@example.com',
        'opt_phone' => '555-1234',
        'opt_address' => '123 Main St'
    ]);

    expect($result)->toHaveCount(5);
});

it('can fail complex key validation logic', function (): void {
    $requiredKeys = ['id', 'name', 'email'];
    $validator = Grape::collection()->validateKeys(function ($key, $ctx) use ($requiredKeys) {
        if (in_array($key, $requiredKeys)) {
            return;
        }

        if (!str_starts_with($key, 'opt_')) {
            $ctx->report('Optional keys must start with "opt_"', 'invalid_optional_key');
        }
    });

    $validator->validate([
        'id' => 1,
        'name' => 'John',
        'invalid_key' => 'value'
    ]);
})->throws(ValidationException::class);

// KEY MUTATION TESTS
it('can transform array keys with mutateKeys', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return strtolower($key);
    });

    $result = $validator->validate(['NAME' => 'John', 'EMAIL' => 'john@example.com']);
    expect($result)->toBe(['name' => 'John', 'email' => 'john@example.com']);
});

it('can normalize keys to snake_case', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return strtolower(preg_replace('/([A-Z])/', '_$1', $key));
    });

    $result = $validator->validate(['firstName' => 'John', 'lastName' => 'Doe', 'emailAddress' => 'john@example.com']);
    expect($result)->toBe(['first_name' => 'John', 'last_name' => 'Doe', 'email_address' => 'john@example.com']);
});

it('can prefix all keys', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return 'user_' . $key;
    });

    $result = $validator->validate(['name' => 'John', 'email' => 'john@example.com']);
    expect($result)->toBe(['user_name' => 'John', 'user_email' => 'john@example.com']);
});

it('can clean and normalize keys', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        // Remove whitespace, convert to lowercase, replace spaces with underscores
        return str_replace(' ', '_', strtolower(trim($key)));
    });

    $result = $validator->validate([
        '  First Name  ' => 'John',
        ' LAST NAME ' => 'Doe',
        'Email Address' => 'john@example.com'
    ]);

    expect($result)->toBe([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email_address' => 'john@example.com'
    ]);
});

it('can transform numeric keys to string keys', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return 'item_' . $key;
    });

    $result = $validator->validate([0 => 'first', 1 => 'second', 2 => 'third']);
    expect($result)->toBe(['item_0' => 'first', 'item_1' => 'second', 'item_2' => 'third']);
});

it('can handle complex key transformations', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        // Convert CamelCase to kebab-case and add prefix
        $kebab = strtolower(preg_replace('/([A-Z])/', '-$1', $key));
        return 'api-' . ltrim($kebab, '-');
    });

    $result = $validator->validate([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'phoneNumber' => '555-1234',
        'isActive' => true
    ]);

    expect($result)->toBe([
        'api-first-name' => 'John',
        'api-last-name' => 'Doe',
        'api-phone-number' => '555-1234',
        'api-is-active' => true
    ]);
});

it('can preserve values when mutating keys', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return strtoupper($key);
    });

    $complexData = [
        'user' => ['name' => 'John', 'age' => 30],
        'settings' => ['theme' => 'dark', 'notifications' => true],
        'scores' => [95, 87, 92]
    ];

    $result = $validator->validate($complexData);

    expect($result)->toBe([
        'USER' => ['name' => 'John', 'age' => 30],
        'SETTINGS' => ['theme' => 'dark', 'notifications' => true],
        'SCORES' => [95, 87, 92]
    ]);
});

// COMBINED KEY VALIDATION AND MUTATION TESTS
it('can combine key validation and mutation', function (): void {
    $validator = Grape::collection()
        ->validateKeys(function ($key, $ctx) {
            if (!is_string($key)) {
                $ctx->report('Key must be a string', 'invalid_key_type');
            }
        })
        ->mutateKeys(function ($key) {
            return strtolower($key);
        });

    $result = $validator->validate(['NAME' => 'John', 'EMAIL' => 'john@example.com']);
    expect($result)->toBe(['name' => 'John', 'email' => 'john@example.com']);
});

it('can fail combined validation before mutation', function (): void {
    $validator = Grape::collection()
        ->validateKeys(function ($key, $ctx) {
            if (!is_string($key)) {
                $ctx->report('Key must be a string', 'invalid_key_type');
            }
        })
        ->mutateKeys(function ($key) {
            return strtolower($key);
        });

    $validator->validate([0 => 'value', 'VALID' => 'data']);
})->throws(ValidationException::class);

it('can chain key operations with other validations', function (): void {
    $validator = Grape::collection(Grape::string()->trim())
        ->validateKeys(function ($key, $ctx) {
            if (strlen($key) < 2) {
                $ctx->report('Key too short', 'key_length');
            }
        })
        ->mutateKeys(function ($key) {
            return 'field_' . strtolower($key);
        })
        ->minLength(1)
        ->maxLength(5);

    $result = $validator->validate([
        'NAME' => '  John  ',
        'EMAIL' => '  john@example.com  '
    ]);

    expect($result)->toBe([
        'field_name' => 'John',
        'field_email' => 'john@example.com'
    ]);
});

it('can handle edge cases with empty keys', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return $key === '' ? 'empty_key' : $key;
    });

    $result = $validator->validate(['' => 'empty', 'normal' => 'value']);
    expect($result)->toBe(['empty_key' => 'empty', 'normal' => 'value']);
});

it('can handle key collisions after mutation', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return 'same_key'; // All keys become the same
    });

    $result = $validator->validate(['key1' => 'value1', 'key2' => 'value2']);
    // Last value should win in case of collision
    expect($result)->toBe(['same_key' => 'value2']);
});

// NESTED STRUCTURES WITH KEY OPERATIONS
it('can validate keys in nested structures', function (): void {
    $itemValidator = Grape::collection()
        ->validateKeys(function ($key, $ctx) {
            if (!in_array($key, ['id', 'name', 'email'])) {
                $ctx->report('Invalid user field', 'invalid_user_field');
            }
        });

    $validator = Grape::collection($itemValidator);

    $result = $validator->validate([
        ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
        ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com']
    ]);

    expect($result)->toHaveCount(2);
});

it('can mutate keys in nested structures', function (): void {
    $itemValidator = Grape::collection()
        ->mutateKeys(function ($key) {
            return 'user_' . $key;
        });

    $validator = Grape::collection($itemValidator);

    $result = $validator->validate([
        ['name' => 'John', 'email' => 'john@example.com'],
        ['name' => 'Jane', 'email' => 'jane@example.com']
    ]);

    expect($result)->toBe([
        ['user_name' => 'John', 'user_email' => 'john@example.com'],
        ['user_name' => 'Jane', 'user_email' => 'jane@example.com']
    ]);
});

// PERFORMANCE AND COMPLEX SCENARIOS
it('can handle large arrays with key transformations', function (): void {
    $validator = Grape::collection()->mutateKeys(function ($key) {
        return 'processed_' . $key;
    });

    $largeData = [];
    for ($i = 0; $i < 100; $i++) {
        $largeData["key_$i"] = "value_$i";
    }

    $result = $validator->validate($largeData);

    expect($result)->toHaveCount(100);
    expect($result['processed_key_0'])->toBe('value_0');
    expect($result['processed_key_99'])->toBe('value_99');
});

it('can validate and mutate keys with complex business rules', function (): void {
    $validator = Grape::collection()
        ->validateKeys(function ($key, $ctx) {
            // Business rule: API keys must be either system fields (case-insensitive) or follow naming convention
            $systemFields = ['id', 'created_at', 'updated_at'];

            if (in_array(strtolower($key), $systemFields)) {
                return; // System fields are always valid (case-insensitive)
            }

            if (!preg_match('/^[a-z]+(_[a-z]+)*$/', $key)) {
                $ctx->report('Custom fields must be in snake_case format', 'invalid_field_format');
            }
        })
        ->mutateKeys(function ($key) {
            // Normalize all keys to ensure consistency
            return strtolower($key);
        });

    $result = $validator->validate([
        'ID' => 1,
        'user_name' => 'John',
        'email_address' => 'john@example.com',
        'CREATED_AT' => '2023-01-01',
        'custom_field' => 'value'
    ]);

    expect($result)->toBe([
        'id' => 1,
        'user_name' => 'John',
        'email_address' => 'john@example.com',
        'created_at' => '2023-01-01',
        'custom_field' => 'value'
    ]);
});



