<?php

/**
 * This file is part of ramsey/conventional-commits
 *
 * ramsey/conventional-commits is open source software: you can distribute it
 * and/or modify it under the terms of the MIT License (the "License"). You may
 * not use this file except in compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Sojeda\ConventionalCommits\Console\Question;

use Sojeda\ConventionalCommits\Exception\InvalidArgument;
use Sojeda\ConventionalCommits\Exception\InvalidConsoleInput;
use Sojeda\ConventionalCommits\Exception\InvalidValue;
use Sojeda\ConventionalCommits\Message\Footer;
use Symfony\Component\Console\Question\Question;

use function strlen;
use function trim;

/**
 * A prompt asking the user to enter the relationship of this commit to
 * the issue tracker issue/ticket (i.e., "fix", "re", etc.)
 */
class IssueTypeQuestion extends Question
{
    public function __construct()
    {
        parent::__construct(
            'What is the issue reference type? (e.g., fix, re)',
        );
    }

    public function getValidator(): callable
    {
        return function (?string $answer): ?string {
            if ($answer === null || strlen(trim($answer)) === 0) {
                return null;
            }

            try {
                $validFooter = new Footer($answer, 'validation');
            } catch (InvalidArgument | InvalidValue $exception) {
                throw new InvalidConsoleInput('Invalid issue reference type. ' . $exception->getMessage());
            }

            return $validFooter->getToken();
        };
    }
}
