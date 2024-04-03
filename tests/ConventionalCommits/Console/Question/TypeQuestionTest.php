<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Console\Question;

use Sojeda\ConventionalCommits\Configuration\DefaultConfiguration;
use Sojeda\ConventionalCommits\Console\Question\TypeQuestion;
use Sojeda\ConventionalCommits\Exception\InvalidConsoleInput;
use Sojeda\ConventionalCommits\Message\Type;
use Sojeda\Test\TestCase;

class TypeQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new TypeQuestion();

        $this->assertSame(
            'What is the type of change you\'re committing? (e.g., feat, fix, etc.)',
            $question->getQuestion(),
        );
        $this->assertSame('feat', $question->getDefault());
    }

    public function testValidatorReturnsType(): void
    {
        $question = new TypeQuestion();
        $validator = $question->getValidator();

        /** @var Type $type */
        $type = $validator('feat');

        $this->assertInstanceOf(Type::class, $type);
        $this->assertSame('feat', $type->toString());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new TypeQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage(
            'Invalid type. Types must contain only alphanumeric characters, underscores, and dashes',
        );

        $validator('type name');
    }

    public function testAutocompleterCallback(): void
    {
        $question = new TypeQuestion();

        $this->assertSame(['feat', 'fix'], $question->getAutocompleterValues());
    }

    public function testAutocompleterCallbackWithConfiguredTypes(): void
    {
        $question = new TypeQuestion(new DefaultConfiguration([
            'types' => ['foo', 'bar', 'baz'],
        ]));

        $this->assertSame(['feat', 'fix', 'foo', 'bar', 'baz'], $question->getAutocompleterValues());
    }
}
