<?php

use Potager\Grape\Exceptions\ValidationException;

it('can be constructed with messages and default message', function () {
    $exception = new ValidationException(['error1', 'error2']);
    expect($exception)->toBeInstanceOf(ValidationException::class);
    expect($exception->getMessages())->toBe(['error1', 'error2']);
    expect($exception->getMessage())->toBe('Validation failed');
});

it('can be constructed with a custom message', function () {
    $exception = new ValidationException(['error'], 'Custom message');
    expect($exception->getMessage())->toBe('Custom message');
});

it('can attach and retrieve raw value', function () {
    $exception = new ValidationException([]);
    $exception->attachRaw('raw-data');
    expect($exception->getRawValue())->toBe('raw-data');
});

it('returns null for raw value if not set', function () {
    $exception = new ValidationException([]);
    expect($exception->getRawValue())->toBeNull();
});
