<?php
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\Grape;

it('can validate null value', function (): void {
    $validator = Grape::null();
    $result = $validator->validate(null);
    expect($result)->toBe(null);
});

it('can fail if value is not null', function (): void {
    $validator = Grape::null();
    $result = $validator->validate("not null");
})->throws(ValidationException::class);
