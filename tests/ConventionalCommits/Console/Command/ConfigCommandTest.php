<?php

declare(strict_types=1);

namespace Sojeda\Test\ConventionalCommits\Console\Command;

use Mockery\MockInterface;
use Sojeda\ConventionalCommits\Console\Command\BaseCommand;
use Sojeda\ConventionalCommits\Console\Command\ConfigCommand;
use Sojeda\ConventionalCommits\Console\SymfonyStyleFactory;
use Sojeda\Test\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

use function file_get_contents;
use function realpath;
use function str_replace;
use function trim;

class ConfigCommandTest extends TestCase
{
    public function testCommandName(): void
    {
        $command = new ConfigCommand();

        $this->assertSame('config', $command->getName());
    }

    public function testRunDoesNothing(): void
    {
        /** @var SymfonyStyle & MockInterface $style */
        $style = $this->mockery(SymfonyStyle::class);
        $style->shouldNotReceive('writeln');

        /** @var SymfonyStyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(SymfonyStyleFactory::class, [
            'factory' => $style,
        ]);

        $configFile = (string) realpath(__DIR__ . '/../../../configs/default.json');

        // Windows-proof the file path.
        $configFile = str_replace('\\', '\\\\', $configFile);

        $input = new StringInput("--config=\"{$configFile}\"");
        $output = new NullOutput();

        $command = new ConfigCommand($styleFactory);

        $this->assertSame(BaseCommand::SUCCESS, $command->run($input, $output));
    }

    public function testRunWritesConfigToConsoleWithDump(): void
    {
        $configFile = (string) realpath(__DIR__ . '/../../../configs/default.json');
        $configFileContents = trim((string) file_get_contents($configFile));

        /** @var SymfonyStyle & MockInterface $style */
        $style = $this->mockery(SymfonyStyle::class);
        $style
            ->expects()
            ->writeln($configFileContents);

        /** @var SymfonyStyleFactory & MockInterface $styleFactory */
        $styleFactory = $this->mockery(SymfonyStyleFactory::class, [
            'factory' => $style,
        ]);

        // Windows-proof the file path.
        $configFile = str_replace('\\', '\\\\', $configFile);

        $input = new StringInput("--config=\"{$configFile}\" --dump");
        $output = new NullOutput();

        $command = new ConfigCommand($styleFactory);

        $this->assertSame(BaseCommand::SUCCESS, $command->run($input, $output));
    }
}
