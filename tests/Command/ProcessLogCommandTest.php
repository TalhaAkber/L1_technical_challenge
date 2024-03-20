<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessLogCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        // Replace ProcessLogCommand with the actual command class name
        $command = $application->find('app:process-log');
        $commandTester = new CommandTester($command);

        // Replace this line in the command execution
        $commandTester->execute(['filePath' => '/var/tests/logs.log']);
        // $output = $commandTester->getDisplay();
        $commandTester->assertCommandIsSuccessful();
        // Can do more assertions after adding it to database we can run the query to test the count. etc
    }
}
