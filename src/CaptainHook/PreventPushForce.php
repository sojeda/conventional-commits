<?php

declare(strict_types=1);

namespace Sojeda\CaptainHook;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ConfigAction;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action;
use Error;
use Exception;
use SebastianFeldmann\Git\Repository;

use function count;
use function explode;
use function sprintf;
use function str_contains;
use function trim;

use const PHP_EOL;

final class PreventPushForce implements Action
{
    private ?StdinReader $stdinReader;

    private ?ForceDetector $forceDetector;

    public function __construct(?StdinReader $stdinReader = null, ?ForceDetector $forceDetector = null)
    {
        $this->stdinReader = $stdinReader;
        $this->forceDetector = $forceDetector;
        if (!$this->stdinReader) {
            $this->stdinReader = new StdinReader();
        }
        if (!$this->forceDetector) {
            $this->forceDetector = new ForceDetector();
        }
    }

    /**
     * https://git-scm.com/docs/githooks#_pre_push to see pre-push standard input
     *
     * @throws Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, ConfigAction $action): void
    {
        if (!$this->stdinReader) {
            return;
        }

        if (!$this->forceDetector) {
            return;
        }

        $stdin = $this->stdinReader->read();
        if ($stdin === '') {
            return;
        }
        if (!$this->forceDetector->isForceUsed()) {
            return;
        }
        $lines = explode(PHP_EOL, trim($stdin));
        /** @var array<string> $protectedBranches */
        $protectedBranches = $action->getOptions()->get('protected-branches');
        if (count($protectedBranches) === 0) {
            throw new Error(
                sprintf(
                    'You must configure the "protected-branches" option for the action "%s".',
                    self::class,
                ),
            );
        }
        foreach ($lines as $line) {
            $line = explode(' ', trim($line));
            /**
             * @see https://git-scm.com/docs/githooks#_pre_push
             * $line[0] => local ref/branch
             * $line[1] => local sha1
             * $line[2] => remote ref/branch
             * $line[3] => remote sha1
             */
            $remoteBranch = $line[2];
            foreach ($protectedBranches as $protectedBranch) {
                if (str_contains($remoteBranch, $protectedBranch)) {
                    throw new Error("Never force push or delete the \"$protectedBranch\" branch!");
                }
            }
        }
    }
}
