<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Console\Question;

use Sojeda\ConventionalCommits\Console\Question\IssueIdentifierQuestion;
use Sojeda\ConventionalCommits\Exception\InvalidConsoleInput;
use Sojeda\ConventionalCommits\Message\Footer;
use Sojeda\Test\TestCase;

class IssueIdentifierQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new IssueIdentifierQuestion('fix');

        $this->assertSame(
            'Enter the issue identifier <comment>(do not include a preceding #-symbol)</comment>',
            $question->getQuestion(),
        );
        $this->assertNull($question->getDefault());
    }

    public function testValidatorReturnsFooter(): void
    {
        $question = new IssueIdentifierQuestion('fix');
        $validator = $question->getValidator();

        /** @var Footer $footer */
        $footer = $validator('1234');

        $this->assertInstanceOf(Footer::class, $footer);
        $this->assertSame('fix', $footer->getToken());
        $this->assertSame('1234', $footer->getValue());
        $this->assertSame(' #', $footer->getSeparator());
    }

    public function testValidatorThrowsExceptionForInvalidValue(): void
    {
        $question = new IssueIdentifierQuestion('fix');
        $validator = $question->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessage('Invalid identifier value. Footer values may not contain other footers.');

        $validator("1234; this is invalid because\ntoken-name: it contains another footer");
    }
}
