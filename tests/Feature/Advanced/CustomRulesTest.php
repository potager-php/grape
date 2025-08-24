<?php
use Potager\Grape\Grape;
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\FieldContext;

it('can validate with a custom rule that passes', function () {
    $validator = Grape::string()->custom(function (FieldContext $ctx) {
        // Accept only 'custom'
        if ($ctx->getValue() !== 'custom') {
            $ctx->fatal('Value must be "custom"', 'custom_rule');
        }
    });
    $result = $validator->validate('custom');
    expect($result)->toBe('custom');
});

it('fails validation with a custom rule', function () {
    $validator = Grape::string()->custom(function (FieldContext $ctx) {
        if ($ctx->getValue() !== 'custom') {
            $ctx->fatal('Value must be "custom"', 'custom_rule');
        }
    });
    $validator->validate('not-custom');
})->throws(ValidationException::class);

it('can chain multiple custom rules', function () {
    $validator = Grape::string()
        ->custom(function (FieldContext $ctx) {
            if (strlen($ctx->getValue()) < 5) {
                $ctx->fatal('Too short', 'length');
            }
        })
        ->custom(function (FieldContext $ctx) {
            if ($ctx->getValue() !== 'hello') {
                $ctx->fatal('Must be hello', 'value');
            }
        });
    $result = $validator->validate('hello');
    expect($result)->toBe('hello');
});

it('fails if any custom rule fails', function () {
    $validator = Grape::string()
        ->custom(function (FieldContext $ctx) {
            if (strlen($ctx->getValue()) < 5) {
                $ctx->fatal('Too short', 'length');
            }
        })
        ->custom(function (FieldContext $ctx) {
            if ($ctx->getValue() !== 'hello') {
                $ctx->fatal('Must be hello', 'value');
            }
        });
    $validator->validate('hi');
})->throws(ValidationException::class);

it('throws if custom rule does not accept FieldContext', function () {
    expect(fn() => Grape::string()->custom(function ($value) {
        // Invalid signature
        return true;
    }))->toThrow(\InvalidArgumentException::class);
});

it('throws if custom rule accepts more than one parameter', function () {
    expect(fn() => Grape::string()->custom(function (FieldContext $ctx, $extra) {
        return true;
    }))->toThrow(\InvalidArgumentException::class);
});

