<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Message;

use Sojeda\ConventionalCommits\Message\Type;

class TypeTest extends NounTestCase
{
    protected function getClassName(): string
    {
        return Type::class;
    }
}
