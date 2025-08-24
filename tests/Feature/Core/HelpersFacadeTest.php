<?php
use Potager\Grape\Grape;

it('can check truthy values with isTrue', function () {
    $helpers = Grape::helpers();
    expect($helpers->isTrue(true))->toBeTrue();
    expect($helpers->isTrue('yes'))->toBeTrue();
    expect($helpers->isTrue('1'))->toBeTrue();
    expect($helpers->isTrue('no'))->toBeFalse();
});

it('can check falsy values with isFalse', function () {
    $helpers = Grape::helpers();
    expect($helpers->isFalse(false))->toBeTrue();
    expect($helpers->isFalse('no'))->toBeTrue();
    expect($helpers->isFalse('0'))->toBeTrue();
    expect($helpers->isFalse('yes'))->toBeFalse();
});

it('can validate URLs', function () {
    $helpers = Grape::helpers();
    expect($helpers->isUrl('https://example.com'))->toBeTrue();
    expect($helpers->isUrl('not-a-url'))->toBeFalse();
});

it('can validate active URLs', function () {
    $helpers = Grape::helpers();
    expect($helpers->isActiveUrl('https://google.com'))->toBeTrue();
    expect($helpers->isActiveUrl('https://nonexistentdomain12345.com'))->toBeFalse();
});

it('can validate JSON', function () {
    $helpers = Grape::helpers();
    expect($helpers->isJson('{"key": "value"}'))->toBeTrue();
    expect($helpers->isJson('not-json'))->toBeFalse();
});

it('can validate Luhn numbers', function () {
    $helpers = Grape::helpers();
    expect($helpers->isLuhnNumber('4111111111111111'))->toBeTrue();
    expect($helpers->isLuhnNumber('1234567890123456'))->toBeFalse();
});

it('can validate IP addresses', function () {
    $helpers = Grape::helpers();
    expect($helpers->isIp('192.168.1.1'))->toBeTrue();
    expect($helpers->isIp('999.999.999.999'))->toBeFalse();
    expect($helpers->isIpv4('192.168.1.1'))->toBeTrue();
    expect($helpers->isIpv6('2001:0db8:85a3:0000:0000:8a2e:0370:7334'))->toBeTrue();
});

it('can validate mobile phone numbers', function () {
    $helpers = Grape::helpers();
    expect($helpers->isMobilePhone('+32456789000'))->toBeTrue();
    expect($helpers->isMobilePhone('not-a-phone'))->toBeFalse();
});

use Potager\Grape\Helpers\MobilePhone;

it('validates mobile phone numbers for supported locales', function () {
    $validNumbers = [
        'en-US' => '+14155552671',
        'fr-FR' => '+33612345678',
        'de-DE' => '+4915123456789',
        'en-GB' => '+447911123456',
        'es-ES' => '+34612345678',
        'it-IT' => '+393331234567',
        'nl-NL' => '+31612345678',
        'pt-BR' => '+5511999999999',
        'ru-RU' => '+79111234567',
    ];
    foreach ($validNumbers as $locale => $number) {
        expect(MobilePhone::validate($number, [$locale]))->toBeTrue();
    }
});

it('rejects invalid mobile phone numbers for supported locales', function () {
    $invalidNumbers = [
        'en-US' => '+141555526',
        'fr-FR' => '+336123456',
        'de-DE' => '+49151234567',
        'en-GB' => '+4479111234',
        'es-ES' => '+346123456',
        'it-IT' => '+3933312345',
        'nl-NL' => '+316123456',
        'pt-BR' => '+55119999999',
        'ru-RU' => '+791112345',
    ];
    foreach ($invalidNumbers as $locale => $number) {
        $result = MobilePhone::validate($number, [$locale]);
        expect($result)->toBeFalse();
    }
});

it('validates mobile phone numbers in strict mode', function () {
    expect(MobilePhone::validate('+14155552671', ['en-US'], true))->toBeTrue();
    expect(MobilePhone::validate('14155552671', ['en-US'], true))->toBeFalse();
});

it('validates mobile phone numbers for multiple locales', function () {
    expect(MobilePhone::validate('+14155552671', ['en-US', 'fr-FR']))->toBeTrue();
    expect(MobilePhone::validate('+33612345678', ['en-US', 'fr-FR']))->toBeTrue();
    expect(MobilePhone::validate('+5511999999999', ['en-US', 'fr-FR']))->toBeFalse();
});

it('returns false for unknown locale', function () {
    expect(MobilePhone::validate('+14155552671', ['unknown']))->toBeFalse();
});

it('returns false for empty string on mobile phone', function () {
    expect(MobilePhone::validate(''))->toBeFalse();
});

it('returns false for non-numeric string on mobile phone', function () {
    expect(MobilePhone::validate('not-a-phone'))->toBeFalse();
});

it('can validate credit card numbers', function () {
    $helpers = Grape::helpers();
    expect($helpers->isCreditCard('4111111111111111'))->toBeTrue();
    expect($helpers->isCreditCard('1234567890123456'))->toBeFalse();
});

use Potager\Grape\Helpers\CreditCard;
use Potager\Grape\Helpers\ActiveUrl;

it('validates credit cards for all supported providers', function () {
    $validCards = [
        'amex' => '378282246310005',
        'visa' => '4111111111111111',
        'mastercard' => '5555555555554444',
        'discover' => '6011111111111117',
        'jcb' => '3530111333300000',
        'diners_club' => '30569309025904',
        'union_pay' => '6240008631401148',
    ];
    foreach ($validCards as $provider => $card) {
        expect(CreditCard::validate($card, [$provider]))->toBeTrue();
    }
});

it('rejects invalid credit card numbers for all providers', function () {
    $invalidCards = [
        'amex' => '378282246310006',
        'visa' => '4111111111111112',
        'mastercard' => '5555555555554440',
        'discover' => '6011111111111110',
        'jcb' => '3530111333300001',
        'diners_club' => '30569309025900',
        'union_pay' => '6240008631401140',
    ];
    foreach ($invalidCards as $provider => $card) {
        expect(CreditCard::validate($card, [$provider]))->toBeFalse();
    }
});

it('validates credit card with spaces and dashes', function () {
    expect(CreditCard::validate('4111 1111 1111 1111'))->toBeTrue();
    expect(CreditCard::validate('4111-1111-1111-1111'))->toBeTrue();
});

it('validates credit card with multiple providers', function () {
    expect(CreditCard::validate('4111111111111111', ['visa', 'mastercard']))->toBeTrue();
    expect(CreditCard::validate('5555555555554444', ['visa', 'mastercard']))->toBeTrue();
    expect(CreditCard::validate('378282246310005', ['visa', 'mastercard']))->toBeFalse();
});

it('returns false for unknown provider', function () {
    expect(CreditCard::validate('4111111111111111', ['unknown']))->toBeFalse();
});

it('returns false for empty string', function () {
    expect(CreditCard::validate(''))->toBeFalse();
});

it('returns false for non-numeric string', function () {
    expect(CreditCard::validate('not-a-card'))->toBeFalse();
});

it('validates active URLs (real network test, may be flaky)', function () {
    expect(ActiveUrl::validate('https://google.com'))->toBeTrue();
    expect(ActiveUrl::validate('https://nonexistentdomain12345.com'))->toBeFalse();
});

it('returns false for invalid URLs in ActiveUrl', function () {
    expect(ActiveUrl::validate('not-a-url'))->toBeFalse();
    expect(ActiveUrl::validate(''))->toBeFalse();
});
