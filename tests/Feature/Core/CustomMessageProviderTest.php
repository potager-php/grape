<?php

use Potager\Grape\Grape;
use Potager\Grape\Contracts\MessageProviderContract;
use Potager\Grape\Messages\SimpleMessageProvider;

class CustomMessageProvider implements MessageProviderContract
{
    public function getMessage(string $defaultMessage, string $rule, Potager\Grape\FieldContext $field, array $meta = []): string
    {
        return "A custom message for {$rule}";
    }
}

beforeEach(function (): void {
    $this->validator = Grape::schema([
        'hello' => Grape::string()->email()->required(),
        'world' => Grape::integer()->required()
    ]);

    $this->simple = new SimpleMessageProvider([
        'hello.email' => 'A custom message for email',
        'world.integer' => 'A custom message for integer',
    ]);

    $this->custom = new CustomMessageProvider();

    $this->invalidData = [
        'hello' => 'invalid-email',
        'world' => 'not-a-number'
    ];

    $this->expectedErrors = [
        'hello' => [
            [
                'message' => 'A custom message for email',
                'path' => 'hello',
                'rule' => 'email',
            ]
        ],
        'world' => [
            [
                'message' => 'A custom message for integer',
                'path' => 'world',
                'rule' => 'integer',
            ]
        ]
    ];
});

it("Can register a SimpleMessageProvider globally and use it", function (): void {
    Grape::setMessageProvider($this->simple);

    [$error, $_] = $this->validator->check($this->invalidData);

    expect($error->getMessages())->toEqual($this->expectedErrors);
});


it("Can register a SimpleMessageProvider by validator and use it", function (): void {
    $this->validator->setMessageProvider($this->simple);

    [$error, $_] = $this->validator->check($this->invalidData);

    expect($error->getMessages())->toEqual($this->expectedErrors);
});

it("Can register a SimpleMessageProvider at validation and use it", function (): void {
    [$error, $_] = $this->validator->check($this->invalidData, messageProvider: $this->simple);

    expect($error->getMessages())->toEqual($this->expectedErrors);
});

it("Can register a CustomMessageProvider globally and use it", function (): void {
    Grape::setMessageProvider($this->custom);

    [$error, $_] = $this->validator->check($this->invalidData);

    expect($error->getMessages())->toEqual($this->expectedErrors);
});

it("Can register a CustomMessageProvider by validator and use it", function (): void {
    $this->validator->setMessageProvider($this->custom);

    [$error, $_] = $this->validator->check($this->invalidData);

    expect($error->getMessages())->toEqual($this->expectedErrors);
});

it("Can register a CustomMessageProvider at validation and use it", function (): void {
    [$error, $_] = $this->validator->check($this->invalidData, messageProvider: $this->custom);

    expect($error->getMessages())->toEqual($this->expectedErrors);
});

it("Can resolve concurrance between global and local", function (): void {
    Grape::setMessageProvider($this->simple);

    $this->validator->setMessageProvider(new SimpleMessageProvider([
        'hello.email' => 'Another custom message for email',
        'world.integer' => 'Another custom message for integer',
    ]));

    $expectedErrors = [
        'hello' => [
            [
                'message' => 'Another custom message for email',
                'path' => 'hello',
                'rule' => 'email',
            ]
        ],
        'world' => [
            [
                'message' => 'Another custom message for integer',
                'path' => 'world',
                'rule' => 'integer',
            ]
        ]
    ];

    [$error, $_] = $this->validator->check($this->invalidData);

    expect($error->getMessages())->toEqual($expectedErrors);
});

it("Can resolve concurrance between local and runtime", function (): void {
    $this->validator->setMessageProvider($this->simple);

    $expectedErrors = [
        'hello' => [
            [
                'message' => 'Another custom message for email',
                'path' => 'hello',
                'rule' => 'email',
            ]
        ],
        'world' => [
            [
                'message' => 'Another custom message for integer',
                'path' => 'world',
                'rule' => 'integer',
            ]
        ]
    ];

    [$error, $_] = $this->validator->check($this->invalidData, messageProvider: new SimpleMessageProvider([
        'hello.email' => 'Another custom message for email',
        'world.integer' => 'Another custom message for integer',
    ]));

    expect($error->getMessages())->toEqual($expectedErrors);
});
