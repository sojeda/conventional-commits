<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Message;

use Sojeda\ConventionalCommits\Message\Scope;

class ScopeTest extends NounTestCase
{
    protected function getClassName(): string
    {
        return Scope::class;
    }
}
