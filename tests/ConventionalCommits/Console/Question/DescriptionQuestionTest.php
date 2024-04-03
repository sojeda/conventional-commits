<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Console\Question;

use Sojeda\ConventionalCommits\Configuration\DefaultConfiguration;
use Sojeda\ConventionalCommits\Console\Question\DescriptionQuestion;
use Sojeda\ConventionalCommits\Exception\InvalidConsoleInput;
use Sojeda\ConventionalCommits\Message\Description;
use Sojeda\Test\TestCase;

class DescriptionQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new DescriptionQuestion();

        $this->assertSame(
            'Write a short description of the change',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsFooter(): void
    {
        $question = new DescriptionQuestion();
        $validator = $question->getValidator();

        /** @var Description $description */
        $description = $validator('this is a description');

        $this->assertInstanceOf(Description::class, $description);
        $this->assertSame('this is a description', $description->toString());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new DescriptionQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid description. Description may not contain any control characters.');

        $validator("foo\nbar");
    }

    public function testValidatorThrowsExceptionForMissingShortDescription(): void
    {
        $question = new DescriptionQuestion();
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('You must provide a short description.');

        $validator("\n");
    }

    public function testValidatorThrowsExceptionForInvalidValueWithDefaultMessageValidator(): void
    {
        $question = new DescriptionQuestion(new DefaultConfiguration([
            'descriptionCase' => 'lower',
        ]));

        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage(
            'Invalid description. \'THIS IS AN INVALID DESCRIPTION\' is not formatted in lower case.',
        );

        $validator('THIS IS AN INVALID DESCRIPTION');
    }
}
