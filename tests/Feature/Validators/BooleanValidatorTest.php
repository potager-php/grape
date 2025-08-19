<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

it('can validate a boolean (strict mode)', function (): void {
    $validator = Grape::boolean(true);
    $result = $validator->validate(true);
    expect($result)->toBe(true);
});

it('can validate a boolean (loose mode)', function (): void {
    $validator = Grape::boolean();
    $result = $validator->validate("1");
    expect($result)->toBe(true);
});

it('can fail if not a boolean (strict mode)', function (): void {
    $validator = Grape::boolean(true);
    $result = $validator->validate("1");
})->throws(ValidationException::class);

it('can fail if not a boolean (loose mode)', function (): void {
    $validator = Grape::boolean();
    $result = $validator->validate([]);
})->throws(ValidationException::class);

it('can validate true value', function (): void {
    $validator = Grape::boolean(true)->true();
    $result = $validator->validate(true);
    expect($result)->toBe(true);
});

it('can fail if value is not true', function (): void {
    $validator = Grape::boolean(true)->true();
    $result = $validator->validate(false);
})->throws(ValidationException::class);

it('can validate false value', function (): void {
    $validator = Grape::boolean(true)->false();
    $result = $validator->validate(false);
    expect($result)->toBe(false);
});

it('can fail if value is not false', function (): void {
    $validator = Grape::boolean(true)->false();
    $result = $validator->validate(true);
})->throws(ValidationException::class);
