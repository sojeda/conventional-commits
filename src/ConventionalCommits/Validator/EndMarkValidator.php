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

namespace Sojeda\ConventionalCommits\Validator;

use Sojeda\ConventionalCommits\Configuration\ConfigurableTool;
use Sojeda\ConventionalCommits\Exception\InvalidArgument;
use Sojeda\ConventionalCommits\Exception\InvalidValue;

use function gettype;
use function is_string;
use function mb_strlen;
use function mb_substr;
use function preg_match;
use function sprintf;

/**
 * Validates whether the value contains the expected end mark (i.e. full stop)
 */
class EndMarkValidator implements Validator
{
    use ConfigurableTool;

    private ?string $endMark;

    public function __construct(?string $endMark)
    {
        $this->endMark = $endMark;
    }

    /**
     * @inheritDoc
     */
    public function isValid($value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgument(sprintf(
                "The value must be a string; received '%s'",
                gettype($value),
            ));
        }

        if ($this->endMark === null) {
            return true;
        }

        if ($this->endMark === '') {
            return (bool) preg_match('/^[^[:punct:]]$/u', mb_substr($value, -1));
        }

        $length = mb_strlen($this->endMark) * -1;

        return mb_substr($value, $length) === $this->endMark;
    }

    /**
     * @inheritDoc
     */
    public function isValidOrException($value): bool
    {
        if ($this->isValid($value)) {
            return true;
        }

        /** @var string $guaranteedStringValue */
        $guaranteedStringValue = $value;

        throw new InvalidValue(sprintf(
            "'%s' does not end with the expected end mark '%s'.",
            $guaranteedStringValue,
            (string) $this->endMark,
        ));
    }
}
