<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Console\Question;

use Sojeda\ConventionalCommits\Console\Question\AddFootersQuestion;
use Sojeda\Test\TestCase;

class AddFootersQuestionTest extends TestCase
{
    public function testQuestion(): void
    {
        $question = new AddFootersQuestion();

        $this->assertSame(
            'Would you like to add any footers? (e.g., Signed-off-by, See-also)',
            $question->getQuestion(),
        );
        $this->assertFalse($question->getDefault());
    }
}
