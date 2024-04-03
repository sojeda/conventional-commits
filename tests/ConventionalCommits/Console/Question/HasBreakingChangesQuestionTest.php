<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Console\Question;

use Sojeda\ConventionalCommits\Console\Question\HasBreakingChangesQuestion;
use Sojeda\Test\TestCase;

class HasBreakingChangesQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new HasBreakingChangesQuestion();

        $this->assertSame(
            'Are there any breaking changes?',
            $question->getQuestion(),
        );
        $this->assertFalse($question->getDefault());
    }
}
