<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

it('can validate an integer', function (): void {
    $validator = Grape::integer();
    $result = $validator->validate(42);
    expect($result)->toBe(42);
});

it('can validate an integer (loose mode)', function (): void {
    $validator = Grape::integer(false);
    $result = $validator->validate("42");
    expect($result)->toBe(42);
});

it('can fail if it\'s not an integer (loose mode)', function (): void {
    $validator = Grape::integer(false);
    $result = $validator->validate("not an integer");
})->throws(ValidationException::class);

it('can fail if it\'s not an integer (strict mode)', function (): void {
    $validator = Grape::integer(true);
    $result = $validator->validate("42");
})->throws(ValidationException::class);

it('can validate a positive integer', function (): void {
    $validator = Grape::integer()->positive();
    $result = $validator->validate(42);
    expect($result)->toBe(42);
});

it('can fail if integer is not positive', function (): void {
    $validator = Grape::integer()->positive();
    $result = $validator->validate(-42);
})->throws(ValidationException::class);

it('can validate a negative integer', function (): void {
    $validator = Grape::integer()->negative();
    $result = $validator->validate(-42);
    expect($result)->toBe(-42);
});

it('can fail if integer is not negative', function (): void {
    $validator = Grape::integer()->negative();
    $result = $validator->validate(42);
})->throws(ValidationException::class);

it('can validate absolute value of an integer', function (): void {
    $validator = Grape::integer()->abs();
    $result = $validator->validate(-42);
    expect($result)->toBe(42);
});

it('can validate integer with a minimum value', function (): void {
    $validator = Grape::integer()->min(10);
    $result = $validator->validate(15);
    expect($result)->toBe(15);
});

it('can fail if integer is less than minimum value', function (): void {
    $validator = Grape::integer()->min(10);
    $result = $validator->validate(5);
})->throws(ValidationException::class);

it('can validate integer with a maximum value', function (): void {
    $validator = Grape::integer()->max(20);
    $result = $validator->validate(15);
    expect($result)->toBe(15);
});

it('can fail if integer is greater than maximum value', function (): void {
    $validator = Grape::integer()->max(20);
    $result = $validator->validate(25);
})->throws(ValidationException::class);

it('can validate integer within a range', function (): void {
    $validator = Grape::integer()->range(10, 20);
    $result = $validator->validate(15);
    expect($result)->toBe(15);
});

it('can fail if integer is outside the range', function (): void {
    $validator = Grape::integer()->range(10, 20);
    $result = $validator->validate(25);
})->throws(ValidationException::class);

it('can validate zero as an integer', function (): void {
    $validator = Grape::integer()->zero();
    $result = $validator->validate(0);
    expect($result)->toBe(0);
});

it('can fail if integer is not zero', function (): void {
    $validator = Grape::integer()->zero();
    $result = $validator->validate(1);
})->throws(ValidationException::class);

it('can validate non-zero integer', function (): void {
    $validator = Grape::integer()->notZero();
    $result = $validator->validate(42);
    expect($result)->toBe(42);
});

it('can fail if integer is zero when notZero is required', function (): void {
    $validator = Grape::integer()->notZero();
    $result = $validator->validate(0);
})->throws(ValidationException::class);

it('can validate odd integer', function (): void {
    $validator = Grape::integer()->odd();
    $result = $validator->validate(3);
    expect($result)->toBe(3);
});

it('can fail if integer is not odd', function (): void {
    $validator = Grape::integer()->odd();
    $result = $validator->validate(4);
})->throws(ValidationException::class);

it('can validate even integer', function (): void {
    $validator = Grape::integer()->even();
    $result = $validator->validate(4);
    expect($result)->toBe(4);
});

it('can fail if integer is not even', function (): void {
    $validator = Grape::integer()->even();
    $result = $validator->validate(3);
})->throws(ValidationException::class);

it('can validate integer that is a multiple of a given number', function (): void {
    $validator = Grape::integer()->multipleOf(5);
    $result = $validator->validate(15);
    expect($result)->toBe(15);
});

it('can fail if integer is not a multiple of a given number', function (): void {
    $validator = Grape::integer()->multipleOf(5);
    $result = $validator->validate(14);
})->throws(ValidationException::class);

it('can validate integer that is a multiple of a negative number', function (): void {
    $validator = Grape::integer()->multipleOf(-3);
    $result = $validator->validate(-9);
    expect($result)->toBe(-9);
});

it('can fail if integer is not a multiple of a negative number', function (): void {
    $validator = Grape::integer()->multipleOf(-3);
    $result = $validator->validate(10);
})->throws(ValidationException::class);

it('can validate zero as a multiple of any non-zero number', function (): void {
    $validator = Grape::integer()->multipleOf(7);
    $result = $validator->validate(0);
    expect($result)->toBe(0);
});

it('can clamp integer below minimum', function (): void {
    $validator = Grape::integer()->clamp(10, 20);
    $result = $validator->validate(5);
    expect($result)->toBe(10);
});

it('can clamp integer above maximum', function (): void {
    $validator = Grape::integer()->clamp(10, 20);
    $result = $validator->validate(25);
    expect($result)->toBe(20);
});

it('Does not clamp integer within range', function (): void {
    $validator = Grape::integer()->clamp(10, 20);
    $result = $validator->validate(15);
    expect($result)->toBe(15);
});

it('can clamp integer at exact minimum', function (): void {
    $validator = Grape::integer()->clamp(10, 20);
    $result = $validator->validate(10);
    expect($result)->toBe(10);
});

it('can clamp integer at exact maximum', function (): void {
    $validator = Grape::integer()->clamp(10, 20);
    $result = $validator->validate(20);
    expect($result)->toBe(20);
});
