<?php

declare(strict_types=1);

namespace Sojeda\Test\CaptainHook;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action as ConfigAction;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use Hamcrest\Core\IsInstanceOf;
use Mockery\MockInterface;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;
use Sojeda\CaptainHook\Output;
use Sojeda\CaptainHook\ValidateConventionalCommit;
use Sojeda\ConventionalCommits\Console\SymfonyStyleFactory;
use Sojeda\Test\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Style\SymfonyStyle;

use function trim;

class ValidateConventionalCommitTest extends TestCase
{
    public function testGetRestriction(): void
    {
        $restriction = ValidateConventionalCommit::getRestriction();

        $this->assertTrue($restriction->isApplicableFor('commit-msg'));
    }

    public function testExecute(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class, [
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => false,
        ]);

        /** @var ConfigAction & MockInterface $configAction */
        $configAction = $this->mockery(ConfigAction::class, [
            'getOptions->getAll' => ['config' => []],
        ]);

        /** @var CommitMessage & MockInterface $commitMessage */
        $commitMessage = $this->mockery(CommitMessage::class);
        $commitMessage
            ->expects()
            ->getContent()
            ->andReturn('feat: implementing something real nice');

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class);
        $repository
            ->expects()
            ->getCommitMsg()
            ->andReturn($commitMessage);

        $action = new ValidateConventionalCommit();

        $action->execute($config, $io, $repository, $configAction);
    }

    public function testExecuteThrowsException(): void
    {
        /** @var Config & MockInterface $config */
        $config = $this->mockery(Config::class);

        $output = '';

        /** @var IO & MockInterface $io */
        $io = $this->mockery(IO::class, [
            'isDebug' => false,
            'isVeryVerbose' => false,
            'isVerbose' => false,
        ]);

        $io->shouldReceive('write')->andReturnUsing(
            function (string $value) use (&$output): void {
                if (trim($value) !== '') {
                    $output .= trim($value) . ' ';
                }
            },
        );

        /** @var ConfigAction & MockInterface $configAction */
        $configAction = $this->mockery(ConfigAction::class, [
            'getOptions->getAll' => ['config' => []],
        ]);

        /** @var CommitMessage & MockInterface $commitMessage */
        $commitMessage = $this->mockery(CommitMessage::class);
        $commitMessage
            ->expects()
            ->getContent()
            ->once()
            ->andReturn('not a valid commit message');

        /** @var Repository & MockInterface $repository */
        $repository = $this->mockery(Repository::class);
        $repository
            ->expects()
            ->getCommitMsg()
            ->andReturn($commitMessage);

        $console = $this->mockery(SymfonyStyle::class);
        $console
            ->expects('error')
            ->with([
                'Invalid Commit Message',
                'The commit message is not properly formatted according to the '
                . 'Conventional Commits specification. For more details, see '
                . 'https://www.conventionalcommits.org/en/v1.0.0/',
            ]);

        $styleFactory = $this->mockery(SymfonyStyleFactory::class);
        $styleFactory
            ->expects('factory')
            ->with(new IsInstanceOf(ArrayInput::class), new IsInstanceOf(Output::class))
            ->andReturns($console);

        $action = new ValidateConventionalCommit($styleFactory);

        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage('Validation failed.');

        $action->execute($config, $io, $repository, $configAction);
    }
}
