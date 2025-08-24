<?php
use Potager\Grape\Collectors\SimpleErrorCollector;
use Potager\Grape\Grape;
use Potager\Grape\Exceptions\ValidationException;
use Potager\Grape\FieldContext;
use Potager\Grape\Contracts\ErrorCollectorContract;

class CustomErrorCollector implements ErrorCollectorContract
{
    private array $errors = [];
    private ?string $message;

    public function __construct(?string $message = null)
    {
        $this->message = $message;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function report(FieldContext $context, string $rule, string $message): void
    {
        $this->errors[] = [
            'field' => $context->getQualifiedPath(),
            'rule' => $rule,
            'message' => $message,
            'messageAndRule' => $message . ' (' . $rule . ')',
        ];
    }

    public function createError(): ValidationException
    {
        return new ValidationException($this->errors, $this->message ?? 'Custom validation error');
    }
}

it('can use a custom error collector globally', function () {
    $factory = fn(): CustomErrorCollector => new CustomErrorCollector('Global custom error');
    Grape::setErrorCollector($factory);
    $validator = Grape::string()->minLength(10);
    try {
        $validator->validate('short');
    } catch (ValidationException $e) {
        expect($e->getMessage())->toBe('Global custom error');
        $errors = $e->getMessages();
        expect($errors[0]['messageAndRule'])->toContain('The value must be at least 10 characters long.');
    }
    Grape::setErrorCollector(fn(): SimpleErrorCollector => new SimpleErrorCollector()); // Reset to default
});

it('can use a custom error collector for a validator', function () {
    $factory = fn(): CustomErrorCollector => new CustomErrorCollector('Validator custom error');
    $validator = Grape::string()->minLength(10);
    $validator->setErrorCollector($factory);
    try {
        $validator->validate('short');
    } catch (ValidationException $e) {
        expect($e->getMessage())->toBe('Validator custom error');
        $errors = $e->getMessages();
        expect($errors[0]['messageAndRule'])->toContain('The value must be at least 10 characters long.');
    }
});

it('can use a custom error collector for a single validation call', function () {
    $collector = new CustomErrorCollector('Single call custom error');
    $validator = Grape::string()->minLength(10);
    try {
        $validator->validate('short', errorCollector: $collector);
    } catch (ValidationException $e) {
        expect($e->getMessage())->toBe('Single call custom error');
        $errors = $e->getMessages();
        expect($errors[0]['messageAndRule'])->toContain('The value must be at least 10 characters long.');
    }
});

it('throws if custom collector factory does not return valid instance', function () {
    $invalidFactory = fn(): stdClass => new stdClass();
    expect(fn() => Grape::setErrorCollector($invalidFactory))
        ->toThrow(InvalidArgumentException::class, 'The provided factory must return an instance of ErrorCollectorContract.');

});

it('throws if custom collector factory does not return valid instance on validator', function () {
    $invalidFactory = fn(): stdClass => new stdClass();
    $validator = Grape::string()->minLength(10);
    expect(fn() => $validator->setErrorCollector($invalidFactory))
        ->toThrow(InvalidArgumentException::class, 'The provided factory must return an instance of ErrorCollectorContract.');

});