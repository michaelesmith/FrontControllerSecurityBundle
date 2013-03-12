<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Tests\Command;

use MS\Bundle\FrontControllerSecurityBundle\Tests\TestCase;

use MS\Bundle\FrontControllerSecurityBundle\Command\FrontControllerSecurityIPListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;

/**
 * @author msmith
 * @created 3/11/13 6:01 PM
 */
class FrontControllerSecurityIPListCommandTest extends TestCase
{
    public function testExecute()
    {
        vfsStream::setup('root', null, array('.app_dev.security.json' => json_encode(array(
            array('begin' => '127.0.0.1', 'end' => '127.0.0.1', 'expire' => null, 'note' => 'loop back'),
            array('begin' => '192.168.1.1', 'end' => '192.168.1.255', 'expire' => '2013-03-11', 'note' => 'local'),
        ))));

        $application = new Application();
        $application->add(new FrontControllerSecurityIPListCommand());

        $command = $application->find('front-controller:security:ip:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--file' => vfsStream::url('.app_dev.security.json')));

        $this->assertContains('loop back', $commandTester->getDisplay());
        $this->assertContains('local', $commandTester->getDisplay());
        $this->assertContains('2013-03-11', $commandTester->getDisplay());
    }
}
