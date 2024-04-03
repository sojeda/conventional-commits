<?php

declare(strict_types=1);

namespace Sojeda\CaptainHook;

use function posix_getppid;
use function preg_match;
use function shell_exec;

/** * @internal
 */
class ForceDetector
{
    public function isForceUsed(): bool
    {
        $parentPid = posix_getppid();
        /** @var string $output */
        $output = shell_exec('ps -ocommand= -p ' . $parentPid);

        return preg_match('/\s(-f|--force)\s/', $output) === 1;
    }
}
