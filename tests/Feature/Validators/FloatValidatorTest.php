<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

it('can validate a float', function (): void {
    $validator = Grape::float();
    $result = $validator->validate(42.5);
    expect($result)->toBe(42.5);
});

it('can validate a float (loose mode)', function (): void {
    $validator = Grape::float();
    $result = $validator->validate("42.5");
    expect($result)->toBe(42.5);
});

it('can fail if it\'s not a float (loose mode)', function (): void {
    $validator = Grape::float();
    $result = $validator->validate("test");
})->throws(ValidationException::class);

it('can fail if it\'s not a float (strict mode)', function (): void {
    $validator = Grape::float(true);
    $result = $validator->validate("42.5");
})->throws(ValidationException::class);

it('can round a float', function (): void {
    $validator = Grape::float()->round(1);
    $result = $validator->validate(42.567);
    expect($result)->toBe(42.6);
});

it('can floor a float', function (): void {
    $validator = Grape::float()->floor();
    $result = $validator->validate(42.9);
    expect($result)->toBe(42.0);
});

it('can validate NaN', function (): void {
    $validator = Grape::float()->NaN();
    $result = $validator->validate(NAN);
    expect(is_nan(NAN))->toBe(true);
});

it('can fail if value is not NaN', function (): void {
    $validator = Grape::float()->NaN();
    $result = $validator->validate(42.5);
})->throws(ValidationException::class);

it('can validate not NaN', function (): void {
    $validator = Grape::float()->notNaN();
    $result = $validator->validate(42.5);
    expect($result)->toBe(42.5);
});

it('can fail if value is NaN when notNaN is required', function (): void {
    $validator = Grape::float()->notNaN();
    $result = $validator->validate(NAN);
})->throws(ValidationException::class);

it('can validate float without decimals', function (): void {
    $validator = Grape::float()->withoutDecimal();
    $result = $validator->validate(42.0);
    expect($result)->toBe(42.0);
});

it('can fail if float has decimals when without_decimals is required', function (): void {
    $validator = Grape::float()->withoutDecimal();
    $result = $validator->validate(42.5);
})->throws(ValidationException::class);

it('can clamp float below minimum', function (): void {
    $validator = Grape::float()->clamp(10.1, 20.9);
    $result = $validator->validate(5.5);
    expect($result)->toBe(10.1);
});

it('can clamp float above maximum', function (): void {
    $validator = Grape::float()->clamp(10.1, 20.9);
    $result = $validator->validate(25.7);
    expect($result)->toBe(20.9);
});

it('Does not clamp float within range', function (): void {
    $validator = Grape::float()->clamp(10.1, 20.9);
    $result = $validator->validate(15.5);
    expect($result)->toBe(15.5);
});

it('can clamp float at exact minimum', function (): void {
    $validator = Grape::float()->clamp(10.1, 20.9);
    $result = $validator->validate(10.1);
    expect($result)->toBe(10.1);
});

it('can clamp float at exact maximum', function (): void {
    $validator = Grape::float()->clamp(10.1, 20.9);
    $result = $validator->validate(20.9);
    expect($result)->toBe(20.9);
});