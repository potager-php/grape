<?php

use Potager\Grape\Grape;
use Potager\Grape\FieldContext;
use Potager\Grape\Messages\SimpleMessageProvider;
use Mockery;

class Stub
{
    use \Potager\Grape\Traits\MessageConfiguration;
    public static function expose()
    {
        return static::getDefaultMessageProvider();
    }
}

it('returns a default simple message provider when no one is configured', function () {
    expect(Grape::getMessageProvider())->toBeInstanceOf(SimpleMessageProvider::class);
    expect(Stub::expose())->toBeInstanceOf(SimpleMessageProvider::class);
    expect(Stub::getMessageProvider())->toBeInstanceOf(SimpleMessageProvider::class);
});

it('resolves global messages', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => 'email',
        'getQualifiedPath' => 'email',
        'getWildcardPath' => 'email',
    ]);

    $provider = new SimpleMessageProvider([
        'required' => 'The {field} is required.',
        'email' => 'The {field} must be a valid email address.',
    ]);

    expect($provider->getMessage('Default message', 'required', $field))
        ->toBe('The email is required.');

    expect($provider->getMessage('Default message', 'email', $field))
        ->toBe('The email must be a valid email address.');
});

it('resolves field-specific messages', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => 'email',
        'getQualifiedPath' => 'user.email',
        'getWildcardPath' => 'user.email',
    ]);

    $provider = new SimpleMessageProvider([
        'user.email.email' => 'The user email must be valid.',
    ]);

    expect($provider->getMessage('Default message', 'email', $field))
        ->toBe('The user email must be valid.');
});

it('resolves wildcard messages', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => 'color',
        'getQualifiedPath' => 'tags.0.color',
        'getWildcardPath' => 'tags.*.color',
    ]);

    $provider = new SimpleMessageProvider([
        'tags.*.color.required' => 'Each tag must have a color.',
    ]);

    expect($provider->getMessage('Default message', 'required', $field))
        ->toBe('Each tag must have a color.');
});

it('resolves root-specific messages', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => '',
        'getQualifiedPath' => '',
        'getWildcardPath' => '',
    ]);

    $provider = new SimpleMessageProvider([
        '.required' => 'The root element is required.',
    ]);

    expect($provider->getMessage('Default message', 'required', $field))
        ->toBe('The root element is required.');
});

it('resolves messages with interpolation', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => 'username',
        'getQualifiedPath' => 'username',
        'getWildcardPath' => 'username',
    ]);

    $provider = new SimpleMessageProvider([
        'minLength' => 'The {field} must be at least {length} characters long.',
    ]);

    expect($provider->getMessage('Default message', 'minLength', $field, ['length' => 5]))
        ->toBe('The username must be at least 5 characters long.');
});

it('resolves field name aliases', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => 'email',
        'getQualifiedPath' => 'user.email',
        'getWildcardPath' => 'user.email',
    ]);

    $provider = new SimpleMessageProvider([
        'required' => 'The {field} is required.',
    ], [
        'user.email' => 'User Email',
    ]);

    expect($provider->getMessage('Default message', 'required', $field))
        ->toBe('The User Email is required.');
});

it('falls back to default message when no match is found', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => 'username',
        'getQualifiedPath' => 'username',
        'getWildcardPath' => 'username',
    ]);

    $provider = new SimpleMessageProvider([]);

    expect($provider->getMessage('Default message', 'unknownRule', $field))
        ->toBe('Default message');
});

it('resolves messages based on hierarchy', function () {
    $field1 = Mockery::mock(FieldContext::class);
    $field1->allows([
        'getName' => 'color',
        'getQualifiedPath' => 'tags.0.color',
        'getWildcardPath' => 'tags.*.color',
    ]);

    $field2 = Mockery::mock(FieldContext::class);
    $field2->allows([
        'getName' => 'color',
        'getQualifiedPath' => 'tags.1.color',
        'getWildcardPath' => 'tags.*.color',
    ]);

    $field3 = Mockery::mock(FieldContext::class);
    $field3->allows([
        'getName' => 'name',
        'getQualifiedPath' => 'tags.0.name',
        'getWildcardPath' => 'tags.*.name',
    ]);

    $provider = new SimpleMessageProvider([
        'required' => 'The {field} is required.',
        'tags.*.color.required' => 'Each tag must have a color.',
        'tags.0.color.required' => 'The first tag must have a color.',
    ]);

    expect($provider->getMessage('Default message', 'required', $field1))
        ->toBe('The first tag must have a color.');

    expect($provider->getMessage('Default message', 'required', $field2))
        ->toBe('Each tag must have a color.');

    expect($provider->getMessage('Default message', 'required', $field3))
        ->toBe('The name is required.');
});

it('falls back to wildcard message when field-specific message is not found', function () {
    $field = Mockery::mock(FieldContext::class);
    $field->allows([
        'getName' => 'color',
        'getQualifiedPath' => 'tags.1.color',
        'getWildcardPath' => 'tags.*.color',
    ]);

    $provider = new SimpleMessageProvider([
        'required' => 'The {field} is required.',
        'tags.*.color.required' => 'Each tag must have a color.',
    ]);

    expect($provider->getMessage('Default message', 'required', $field))
        ->toBe('Each tag must have a color.');
});