<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Validator;

use Sojeda\ConventionalCommits\Configuration\DefaultConfiguration;
use Sojeda\ConventionalCommits\Exception\InvalidValue;
use Sojeda\ConventionalCommits\Message;
use Sojeda\ConventionalCommits\Message\Body;
use Sojeda\ConventionalCommits\Message\Description;
use Sojeda\ConventionalCommits\Message\Type;
use Sojeda\ConventionalCommits\Validator\DefaultMessageValidator;
use Sojeda\Test\TestCase;
use stdClass;

class DefaultMessageValidatorTest extends TestCase
{
    public function testIsValidReturnsTrue(): void
    {
        $message = new Message(new Type('foo'), new Description('a test description'));
        $messageValidator = new DefaultMessageValidator($message->getConfiguration());

        $this->assertTrue($messageValidator->isValid($message));
    }

    public function testIsValidReturnsFalseForInvalidMessage(): void
    {
        $message = new Message(new Type('foo'), new Description('a test description'));
        $message->setConfiguration(new DefaultConfiguration(['types' => ['bar']]));
        $messageValidator = new DefaultMessageValidator($message->getConfiguration());

        $this->assertFalse($messageValidator->isValid($message));
    }

    public function testIsValidReturnsFalseForNonMessageValues(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration());

        $this->assertFalse($messageValidator->isValid('foobar'));
    }

    public function testIsValidOrExceptionThrowsForNonMessageScalars(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration());

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Expected an instance of ' . Message::class . '; received string',
        );

        $messageValidator->isValidOrException('foobar');
    }

    public function testIsValidOrExceptionThrowsForNonMessageObjects(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration());

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage(
            'Expected an instance of ' . Message::class . '; received stdClass',
        );

        $messageValidator->isValidOrException(new stdClass());
    }

    public function testValidateBodyWhenNullAndNotRequired(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration());

        $this->assertTrue($messageValidator->validateBody(null));
    }

    public function testValidateBodyWhenEmptyStringAndNotRequired(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration());
        $body = new Body('');

        $this->assertTrue($messageValidator->validateBody($body));
    }

    public function testValidateBodyWhenNullAndRequired(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration([
            'bodyRequired' => true,
        ]));

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('You must provide a body.');

        $messageValidator->validateBody(null);
    }

    public function testValidateBodyWhenEmptyStringAndRequired(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration([
            'bodyRequired' => true,
        ]));

        $body = new Body('');

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('You must provide a body.');

        $messageValidator->validateBody($body);
    }

    public function testValidateScopeWhenNullAndNotRequired(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration());

        $this->assertTrue($messageValidator->validateScope(null));
    }

    public function testValidateScopeWhenNullAndRequired(): void
    {
        $messageValidator = new DefaultMessageValidator(new DefaultConfiguration([
            'scopeRequired' => true,
        ]));

        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('You must provide a scope.');

        $messageValidator->validateScope(null);
    }
}
