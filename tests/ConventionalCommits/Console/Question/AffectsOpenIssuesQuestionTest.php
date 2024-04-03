<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Console\Question;

use Sojeda\ConventionalCommits\Console\Question\AffectsOpenIssuesQuestion;
use Sojeda\Test\TestCase;

class AffectsOpenIssuesQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new AffectsOpenIssuesQuestion();

        $this->assertSame(
            'Does this change affect any open issues?',
            $question->getQuestion(),
        );
        $this->assertFalse($question->getDefault());
    }
}
