<?php

use Potager\Grape\FieldContext;
use Potager\Grape\Contracts\ErrorCollectorContract;
use Potager\Grape\Collectors\SimpleErrorCollector;
use Potager\Grape\Messages\SimpleMessageProvider;

describe('FieldContext', function () {

    describe('Constructor and Basic Properties', function () {

        it('can create a root context', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, null, messageProvider: $provider, errorCollector: $collector);

            expect($context->getValue())->toBe('test');
            expect($context->getName())->toBeNull();
            expect($context->hasParent())->toBeFalse();
            expect($context->getParent())->toBeNull();
            expect($context->getPathSegments())->toBeEmpty();
            expect($context->getQualifiedPath())->toBeNull();
            expect($context->getWildcardPath())->toBeNull();
            expect($context->isValid())->toBeTrue();
            expect($context->hasFatalError())->toBeFalse();
            expect($context->hasErrors())->toBeFalse();
            expect($context->isStagingEnabled())->toBeFalse();
            expect($context->getCollector())->toBeInstanceOf(ErrorCollectorContract::class);
        });

        it('can create a nested context', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $parentValue = ['child' => 'test'];
            $parentContext = new FieldContext($parentValue, null, $provider, $collector);
            $childValue = $parentValue['child'];
            $childContext = new FieldContext($childValue, 'child', $provider, $collector, $parentContext);

            expect($childContext->getValue())->toBe('test');
            expect($childContext->getName())->toBe('child');
            expect($childContext->hasParent())->toBeTrue();
            expect($childContext->getParent())->toBe($parentContext);
            expect($childContext->getPathSegments())->toBe(['child']);
            expect($childContext->getQualifiedPath())->toBe('child');
            expect($childContext->getWildcardPath())->toBe('child');
            expect($childContext->getCollector())->toBe($parentContext->getCollector());
        });

        it('can create deeply nested contexts', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['user' => ['profile' => ['name' => 'John']]];
            $rootContext = new FieldContext($data, null, $provider, $collector);
            $userContext = new FieldContext($data['user'], 'user', $provider, $collector, $rootContext);
            $profileContext = new FieldContext($data['user']['profile'], 'profile', $provider, $collector, $userContext);
            $nameContext = new FieldContext($data['user']['profile']['name'], 'name', $provider, $collector, $profileContext);

            expect($nameContext->getPathSegments())->toBe(['user', 'profile', 'name']);
            expect($nameContext->getQualifiedPath())->toBe('user.profile.name');
            expect($nameContext->getWildcardPath())->toBe('user.profile.name');
        });

        it('can handle numeric indices in paths', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['users' => [0 => ['name' => 'John'], 1 => ['name' => 'Jane']]];
            $rootContext = new FieldContext($data, null, $provider, $collector);
            $usersContext = new FieldContext($data['users'], 'users', $provider, $collector, $rootContext);
            $userContext = new FieldContext($data['users'][0], 0, $provider, $collector, $usersContext);
            $nameContext = new FieldContext($data['users'][0]['name'], 'name', $provider, $collector, $userContext);

            expect($nameContext->getPathSegments())->toBe(['users', 0, 'name']);
            expect($nameContext->getQualifiedPath())->toBe('users.0.name');
            expect($nameContext->getWildcardPath())->toBe('users.*.name');
        });

    });

    describe('Value Manipulation', function () {

        it('can mutate values', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'original';
            $context = new FieldContext($value, null, $provider, $collector);

            expect($context->getValue())->toBe('original');

            $context->mutate('modified');
            expect($context->getValue())->toBe('modified');
        });

        it('maintains reference to original value', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'original';
            $context = new FieldContext($value, null, $provider, $collector);

            $context->mutate('modified');
            expect($value)->toBe('modified'); // Original variable should be modified
        });

        it('can discard nested fields', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['keep' => 'this', 'remove' => 'this'];
            $parentContext = new FieldContext($data, null, $provider, $collector);
            $childContext = new FieldContext($data['remove'], 'remove', $provider, $collector, $parentContext);

            expect($data)->toHaveKey('remove');

            $childContext->discardField();
            expect($data)->not->toHaveKey('remove');
            expect($data)->toHaveKey('keep');
        });

        it('throws exception when trying to discard root context', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, null, $provider, $collector);

            $context->discardField();
        })->throws(LogicException::class, 'Cannot discard root context or empty path.');

    });

    describe('Error Reporting', function () {

        it('can report immediate errors', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            expect($context->isValid())->toBeTrue();
            expect($context->hasErrors())->toBeFalse();
            expect($context->isGloballyValid())->toBeTrue();

            $context->report('Test error message', 'test_rule');

            expect($context->isValid())->toBeFalse();
            expect($context->hasErrors())->toBeTrue();
            expect($context->isGloballyValid())->toBeFalse();

            $messages = $context->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('');
            expect($messages[''][0]['message'])->toBe('Test error message');
            expect($messages[''][0]['rule'])->toBe('test_rule');
        });

        it('can report fatal errors', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            expect($context->hasFatalError())->toBeFalse();

            $context->fatal('Fatal error occurred', 'fatal_rule');

            expect($context->hasFatalError())->toBeTrue();
            expect($context->isValid())->toBeFalse();
            expect($context->hasErrors())->toBeTrue();

            $messages = $context->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('');
            expect($messages[''][0]['message'])->toBe('Fatal error occurred');
            expect($messages[''][0]['rule'])->toBe('fatal_rule');
        });

    });

    describe('Error Staging', function () {

        it('can enable and disable staging', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, null, $provider, $collector);

            expect($context->isStagingEnabled())->toBeFalse();

            $context->enableStaging();
            expect($context->isStagingEnabled())->toBeTrue();

            $context->disableStaging();
            expect($context->isStagingEnabled())->toBeFalse();
        });

        it('stages errors when staging is enabled', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            $context->enableStaging();
            $context->report('Staged error', 'staged_rule');

            // Error should not be in collector yet
            expect($context->isGloballyValid())->toBeTrue();
            expect($context->getCollector()->createError()->getMessages())->toBeEmpty();

            // But context should know it has errors
            expect($context->hasErrors())->toBeTrue();
            expect($context->isValid())->toBeFalse();
        });

        it('can commit staged errors', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            $context->enableStaging();
            $context->report('Staged error 1', 'rule1');
            $context->report('Staged error 2', 'rule2');

            expect($context->getCollector()->createError()->getMessages())->toBeEmpty();

            $context->commitStagedErrors();

            $messages = $context->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('');
            expect($context->isGloballyValid())->toBeFalse();
        });

        it('can clear staged errors without committing', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            $context->enableStaging();
            $context->report('Staged error', 'rule');

            expect($context->hasErrors())->toBeTrue();

            $context->clearStagedErrors();

            // Note: valid flag remains false once set, but staged errors are cleared
            expect($context->isValid())->toBeFalse(); // Still false because error was reported
            expect($context->getCollector()->createError()->getMessages())->toBeEmpty();
            expect($context->isGloballyValid())->toBeTrue();
        });

        it('can stage fatal errors', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            $context->enableStaging();
            $context->fatal('Fatal staged error', 'fatal_rule');

            expect($context->hasFatalError())->toBeTrue();
            expect($context->getCollector()->createError()->getMessages())->toBeEmpty();

            $context->commitStagedErrors();

            $messages = $context->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('');
            expect($messages[''][0]['message'])->toBe('Fatal staged error');
        });

    });

    describe('Nested Context Building', function () {

        it('can build nested context for existing array key', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['user' => ['name' => 'John']];
            $context = new FieldContext($data, null, $provider, $collector);

            $nestedContext = $context->buildNestedContext('user');

            expect($nestedContext->getValue())->toBe(['name' => 'John']);
            expect($nestedContext->getName())->toBe('user');
            expect($nestedContext->getParent())->toBe($context);
            expect($nestedContext->getQualifiedPath())->toBe('user');
        });

        it('can build nested context for non-existing array key', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['existing' => 'value'];
            $context = new FieldContext($data, null, $provider, $collector);

            $nestedContext = $context->buildNestedContext('missing');

            expect($nestedContext->getValue())->toBeNull();
            expect($nestedContext->getName())->toBe('missing');
            expect($nestedContext->getParent())->toBe($context);
            expect($nestedContext->getQualifiedPath())->toBe('missing');
        });

        it('can build nested context for non-array value', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'string_value';
            $context = new FieldContext($value, null, $provider, $collector);

            $nestedContext = $context->buildNestedContext('key');

            expect($nestedContext->getValue())->toBeNull();
            expect($nestedContext->getName())->toBe('key');
            expect($nestedContext->getParent())->toBe($context);
        });

        it('maintains reference in nested context', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['user' => ['name' => 'John']];
            $context = new FieldContext($data, null, $provider, $collector);
            $nestedContext = $context->buildNestedContext('user');

            $nestedContext->mutate(['name' => 'Jane']);

            expect($data['user']['name'])->toBe('Jane');
        });

    });

    describe('Error Collection Integration', function () {

        it('shares error collector between parent and child contexts', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['user' => 'value'];
            $parentContext = new FieldContext($data, null, $provider, $collector);
            $childContext = new FieldContext($data['user'], 'user', $provider, $collector, $parentContext);

            expect($childContext->getCollector())->toBe($parentContext->getCollector());

            $childContext->report('Child error', 'child_rule');
            $parentContext->report('Parent error', 'parent_rule');

            $parentMessages = $parentContext->getCollector()->createError()->getMessages();
            $childMessages = $childContext->getCollector()->createError()->getMessages();

            expect($parentMessages)->toBe($childMessages);
            expect($parentMessages)->toHaveKey('user');
            expect($parentMessages)->toHaveKey('');
        });

        it('can check global validity across contexts', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['user' => 'value'];
            $parentContext = new FieldContext($data, null, $provider, $collector);
            $childContext = new FieldContext($data['user'], 'user', $provider, $collector, $parentContext);

            expect($parentContext->isGloballyValid())->toBeTrue();
            expect($childContext->isGloballyValid())->toBeTrue();

            $childContext->report('Error in child', 'rule');

            expect($parentContext->isGloballyValid())->toBeFalse();
            expect($childContext->isGloballyValid())->toBeFalse();
        });

    });

    describe('Method Chaining', function () {

        it('supports method chaining for mutating operations', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            $result = $context
                ->mutate('new_value')
                ->enableStaging()
                ->report('Error message', 'rule')
                ->disableStaging()
                ->fatal('Fatal error', 'fatal_rule');

            expect($result)->toBe($context);
            expect($context->getValue())->toBe('new_value');
            expect($context->isStagingEnabled())->toBeFalse();
            expect($context->hasFatalError())->toBeTrue();
        });

        it('supports chaining for error management', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = 'test';
            $context = new FieldContext($value, 'field', $provider, $collector);

            $result = $context
                ->enableStaging()
                ->report('Error 1', 'rule1')
                ->report('Error 2', 'rule2')
                ->clearStagedErrors()
                ->report('Error 3', 'rule3')
                ->commitStagedErrors();

            expect($result)->toBe($context);

            $messages = $context->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('');
            expect($messages[''][0]['rule'])->toBe('rule3'); // Only the last error should be committed
        });

    });

    describe('Edge Cases and Complex Scenarios', function () {

        it('handles mixed string and numeric keys in paths', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['users' => [0 => ['profile' => [1 => 'value']]]];
            $rootContext = new FieldContext($data, null, $provider, $collector);
            $usersContext = new FieldContext($data['users'], 'users', $provider, $collector, $rootContext);
            $userContext = new FieldContext($data['users'][0], 0, $provider, $collector, $usersContext);
            $profileContext = new FieldContext($data['users'][0]['profile'], 'profile', $provider, $collector, $userContext);
            $valueContext = new FieldContext($data['users'][0]['profile'][1], 1, $provider, $collector, $profileContext);

            expect($valueContext->getPathSegments())->toBe(['users', 0, 'profile', 1]);
            expect($valueContext->getQualifiedPath())->toBe('users.0.profile.1');
            expect($valueContext->getWildcardPath())->toBe('users.*.profile.*');
        });

        it('handles empty string keys', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['' => 'empty_key_value'];
            $context = new FieldContext($data, null, $provider, $collector);
            $nestedContext = new FieldContext($data[''], '', $provider, $collector, $context);

            expect($nestedContext->getName())->toBe('');
            expect($nestedContext->getQualifiedPath())->toBe('');
        });

        it('handles null values correctly', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $value = null;
            $context = new FieldContext($value, 'nullable_field', $provider, $collector);

            expect($context->getValue())->toBeNull();

            $context->mutate('not_null');
            expect($context->getValue())->toBe('not_null');
        });

        it('preserves array structure when discarding fields', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['a' => 1, 'b' => 2, 'c' => 3];
            $parentContext = new FieldContext($data, null, $provider, $collector);
            $bContext = new FieldContext($data['b'], 'b', $provider, $collector, $parentContext);

            $bContext->discardField();

            expect($data)->toBe(['a' => 1, 'c' => 3]);
            expect(array_keys($data))->toBe(['a', 'c']);
        });

        it('handles object-like arrays', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = (object) ['property' => 'value'];
            $context = new FieldContext($data, null, $provider, $collector);

            expect($context->getValue())->toBeInstanceOf(stdClass::class);

            // Building nested context with object should return null
            $nestedContext = $context->buildNestedContext('property');
            expect($nestedContext->getValue())->toBeNull();
        });

        it('maintains error state across complex operations', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['level1' => ['level2' => 'value']];
            $rootContext = new FieldContext($data, null, $provider, $collector);
            $level1Context = $rootContext->buildNestedContext('level1');
            $level2Context = $level1Context->buildNestedContext('level2');

            // Stage errors at different levels
            $rootContext->enableStaging();
            $level1Context->enableStaging();
            $level2Context->enableStaging();

            $rootContext->report('Root error', 'root_rule');
            $level1Context->report('Level1 error', 'level1_rule');
            $level2Context->report('Level2 error', 'level2_rule');

            expect($rootContext->isGloballyValid())->toBeTrue(); // No committed errors yet

            // Commit all errors
            $rootContext->commitStagedErrors();
            $level1Context->commitStagedErrors();
            $level2Context->commitStagedErrors();

            expect($rootContext->isGloballyValid())->toBeFalse();

            $messages = $rootContext->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('');
            expect($messages)->toHaveKey('level1');
            expect($messages)->toHaveKey('level1.level2');
        });

        it('handles zero and false values correctly', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $zeroValue = 0;
            $falseValue = false;
            $emptyStringValue = '';

            $zeroContext = new FieldContext($zeroValue, null, $provider, $collector);
            $falseContext = new FieldContext($falseValue, null, $provider, $collector);
            $emptyContext = new FieldContext($emptyStringValue, null, $provider, $collector);

            expect($zeroContext->getValue())->toBe(0);
            expect($falseContext->getValue())->toBeFalse();
            expect($emptyContext->getValue())->toBe('');
        });

        it('handles very deep nesting', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['a' => ['b' => ['c' => ['d' => ['e' => 'deep_value']]]]];
            $rootContext = new FieldContext($data, null, $provider, $collector);

            $aContext = $rootContext->buildNestedContext('a');
            $bContext = $aContext->buildNestedContext('b');
            $cContext = $bContext->buildNestedContext('c');
            $dContext = $cContext->buildNestedContext('d');
            $eContext = $dContext->buildNestedContext('e');

            expect($eContext->getQualifiedPath())->toBe('a.b.c.d.e');
            expect($eContext->getValue())->toBe('deep_value');

            // Test mutation at deep level
            $eContext->mutate('modified_deep_value');
            expect($data['a']['b']['c']['d']['e'])->toBe('modified_deep_value');
        });

        it('handles simultaneous staging across contexts', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['user' => ['name' => 'John'], 'email' => 'john@example.com'];
            $rootContext = new FieldContext($data, null, $provider, $collector);
            $userContext = $rootContext->buildNestedContext('user');
            $nameContext = $userContext->buildNestedContext('name');
            $emailContext = $rootContext->buildNestedContext('email');

            // Enable staging on all contexts
            $rootContext->enableStaging();
            $userContext->enableStaging();
            $nameContext->enableStaging();
            $emailContext->enableStaging();

            // Report errors
            $nameContext->report('Name too short', 'min_length');
            $emailContext->report('Email invalid', 'email_format');

            expect($rootContext->isGloballyValid())->toBeTrue();

            // Commit only email error
            $emailContext->commitStagedErrors();

            expect($rootContext->isGloballyValid())->toBeFalse();

            $messages = $rootContext->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('email');
            expect($messages)->not->toHaveKey('user.name');

            // Now commit name error
            $nameContext->commitStagedErrors();

            $messages = $rootContext->getCollector()->createError()->getMessages();
            expect($messages)->toHaveKey('email');
            expect($messages)->toHaveKey('user.name');
        });

        it('handles circular references in buildNestedContext', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            $data = ['self' => null];
            $data['self'] = &$data; // Create circular reference

            $context = new FieldContext($data, null, $provider, $collector);
            $selfContext = $context->buildNestedContext('self');

            expect($selfContext->getName())->toBe('self');
            expect($selfContext->getQualifiedPath())->toBe('self');
            // The circular reference should be maintained
            expect($selfContext->getValue())->toBe($data);
        });

        it('handles discarding from different array types', function (): void {
            $provider = new SimpleMessageProvider();
            $collector = new SimpleErrorCollector();

            // Test discarding from indexed array
            $indexedArray = ['a', 'b', 'c'];
            $indexedContext = new FieldContext($indexedArray, null, $provider, $collector);
            $middleContext = new FieldContext($indexedArray[1], 1, $provider, $collector, $indexedContext);

            $middleContext->discardField();
            expect($indexedArray)->toBe([0 => 'a', 2 => 'c']); // Index 1 should be gone

            // Test discarding from associative array
            $assocArray = ['first' => 1, 'second' => 2, 'third' => 3];
            $assocContext = new FieldContext($assocArray, null, $provider, $collector);
            $secondContext = new FieldContext($assocArray['second'], 'second', $provider, $collector, $assocContext);

            $secondContext->discardField();
            expect($assocArray)->toBe(['first' => 1, 'third' => 3]);
        });

    });

});
