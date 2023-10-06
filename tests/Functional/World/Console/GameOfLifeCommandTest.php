<?php

declare(strict_types=1);

namespace App\Tests\Functional\World\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class GameOfLifeCommandTest extends KernelTestCase
{
    private CommandTester $gameOfLifeCommand;

    public function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:play');
        $this->gameOfLifeCommand = new CommandTester($command);
    }

    public function testCommandInvalidFile(): void
    {
        $this->gameOfLifeCommand->execute([
            '--file-path' => 'world-invalid.xml'
        ]);
        self::assertStringContainsString(
            'World input XML file world-invalid.xml does not exist!',
            $this->gameOfLifeCommand->getDisplay(),
        );
        self::assertSame(Command::FAILURE, $this->gameOfLifeCommand->getStatusCode());
    }

    public function testCommandSuccess(): void
    {
        $this->gameOfLifeCommand->execute([
            '--file-path' => __DIR__ . '/../../../../resources/world.dist.xml',
        ]);
        $this->gameOfLifeCommand->assertCommandIsSuccessful();
        self::assertStringContainsString(
            'Welcome to Game of Life!',
            $this->gameOfLifeCommand->getDisplay(),
        );
    }
}
