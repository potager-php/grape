<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

it('can validate a number', function (): void {
    $validator = Grape::number();
    $result = $validator->validate(42);
    expect($result)->toBe(42);

    $result = $validator->validate(42.5);
    expect($result)->toBe(42.5);
});

it('can validate a number (loose mode)', function (): void {
    $validator = Grape::number(false); // loose mode
    $result = $validator->validate("42");
    expect($result)->toBe(42);

    $result = $validator->validate("42.5");
    expect($result)->toBe(42.5);
});

it('can fail if not a number', function (): void {
    $validator = Grape::number();
    $validator->validate("not a number");
})->throws(ValidationException::class);

it('can fail if not a number (strict mode)', function (): void {
    $validator = Grape::number(true); // strict mode
    $validator->validate("43.4");
})->throws(ValidationException::class);

it('can clamp a number within range', function (): void {
    $validator = Grape::number()->clamp(10, 20);
    $result = $validator->validate(5);
    expect($result)->toBe(10);

    $result = $validator->validate(15);
    expect($result)->toBe(15);

    $result = $validator->validate(25);
    expect($result)->toBe(20);
});
