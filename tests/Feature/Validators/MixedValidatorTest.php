<?php

use Potager\Grape\Grape;
use Potager\Grape\Exceptions\ValidationException;

it('accepts any value type', function () {
    $validator = Grape::mixed();
    expect($validator->validate('string'))->toBe('string');
    expect($validator->validate(123))->toBe(123);
    expect($validator->validate(12.34))->toBe(12.34);
    expect($validator->validate(true))->toBe(true);
    expect($validator->validate(false))->toBe(false);
    expect($validator->validate([1, 2, 3]))->toBe([1, 2, 3]);
    expect($validator->validate((object) ['a' => 1]))->toEqual((object) ['a' => 1]);
});

it('can be marked as required', function () {
    $validator = Grape::mixed()->required();
    expect($validator->isRequired())->toBeTrue();
});

it('can be marked as nullable', function () {
    $validator = Grape::mixed()->nullable();
    expect($validator->validate(null))->toBeNull();
});

it('can chain required and nullable', function () {
    $validator = Grape::mixed()->required()->nullable();
    expect($validator->isRequired())->toBeTrue();
    expect($validator->validate(null))->toBeNull();
});
