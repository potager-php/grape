<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

it('can validate accepted values', function (): void {
    $validator = Grape::accepted();

    $result = $validator->validate(true);
    expect($result)->toBe(true);

    $result = $validator->validate(1);
    expect($result)->toBe(1);

    $result = $validator->validate('true');
    expect($result)->toBe('true');

    $result = $validator->validate('1');
    expect($result)->toBe('1');

    $result = $validator->validate('on');
    expect($result)->toBe('on');
});

it('can fail if value is not accepted', function (): void {
    $validator = Grape::accepted();
    $result = $validator->validate('no');
})->throws(ValidationException::class);
