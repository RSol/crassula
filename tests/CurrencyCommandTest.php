<?php


namespace App\Tests;


use App\Command\CurrencyCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CurrencyCommandTest extends KernelTestCase
{
    public function testExecute():void
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add(new CurrencyCommand($kernel->getContainer()
            ->get('doctrine')
            ->getManager()));

        $command = $application->find('app:currency');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName()
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('Finished', $output);
    }
}