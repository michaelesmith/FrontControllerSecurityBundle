<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Tests\Command;

use MS\Bundle\FrontControllerSecurityBundle\Tests\TestCase;

use MS\Bundle\FrontControllerSecurityBundle\Command\FrontControllerSecurityIPAddCommand;
use MS\Bundle\FrontControllerSecurityBundle\Command\FrontControllerSecurityIPListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;

/**
 * @author msmith
 * @created 3/11/13 6:01 PM
 */
class FrontControllerSecurityIPAddCommandTest extends TestCase
{
    public function testExecute()
    {
        vfsStream::setup('root', null, array('.app_dev.security.json' => json_encode(array(
            array('begin' => '127.0.0.1', 'end' => '127.0.0.1', 'expire' => null, 'note' => 'loop back'),
            array('begin' => '192.168.1.1', 'end' => '192.168.1.255', 'expire' => '2013-03-11', 'note' => 'local'),
        ))));

        $application = new Application();
        $application->add(new FrontControllerSecurityIPAddCommand());
        $application->add(new FrontControllerSecurityIPListCommand());

        $command = $application->find('front-controller:security:ip:add');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--file' => vfsStream::url('.app_dev.security.json'),
            'begin' => '10.0.0.1',
        ));

        $this->assertContains('10.0.0.1', $commandTester->getDisplay());

        $output = json_decode(file_get_contents(vfsStream::url('.app_dev.security.json')));
        $this->assertCount(3, $output);
    }
}
