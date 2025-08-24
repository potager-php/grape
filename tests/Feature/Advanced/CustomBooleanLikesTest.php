<?php
use Potager\Grape\Grape;

it('can add custom truthy and falsy values', function () {
    Grape::addTruthy(['oui', 'si']);
    Grape::addFalsy(['non', 'nein']);
    expect(Grape::getTruthy())->toContain('oui')->toContain('si');
    expect(Grape::getFalsy())->toContain('non')->toContain('nein');
});

it('can replace all truthy and falsy values', function () {
    Grape::setTruthy(['confirmed', 'approved']);
    Grape::setFalsy(['denied', 'rejected']);
    expect(Grape::getTruthy())->toBe([true, 'confirmed', 'approved']);
    expect(Grape::getFalsy())->toBe([false, 'denied', 'rejected']);
});

it('can remove specific truthy and falsy values', function () {
    Grape::addTruthy(['enable', 'active']);
    Grape::addFalsy(['disable', 'inactive']);
    Grape::removeTruthy(['enable']);
    Grape::removeFalsy(['disable']);
    expect(Grape::getTruthy())->not->toContain('enable');
    expect(Grape::getFalsy())->not->toContain('disable');
});

it('can reset boolean-like values to defaults', function () {
    Grape::setTruthy(['customTrue']);
    Grape::setFalsy(['customFalse']);
    Grape::resetBooleanValues();
    expect(Grape::getTruthy())->toContain('true')->toContain('yes')->toContain('enable');
    expect(Grape::getFalsy())->toContain('false')->toContain('no')->toContain('disable');
});
