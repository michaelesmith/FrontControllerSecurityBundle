<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Tests\Command;

use MS\Bundle\FrontControllerSecurityBundle\Tests\TestCase;

use MS\Bundle\FrontControllerSecurityBundle\Command\FrontControllerSecurityIPRemoveCommand;
use MS\Bundle\FrontControllerSecurityBundle\Command\FrontControllerSecurityIPListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;

/**
 * @author msmith
 * @created 3/11/13 6:01 PM
 */
class FrontControllerSecurityIPRemoveCommandTest extends TestCase
{
    /**
     * @dataProvider providerExecute
     */
    public function testExecute($action, $results)
    {
        vfsStream::setup('root', 777, array('.app_dev.security.json' => json_encode(array(
            array('begin' => '127.0.0.1', 'end' => '127.0.0.1', 'expire' => null, 'note' => 'loop back'),
            array('begin' => '192.168.1.1', 'end' => '192.168.1.255', 'expire' => '2013-03-11', 'note' => 'local'),
        ))));

        $application = new Application();
        $application->add(new FrontControllerSecurityIPRemoveCommand());
        $application->add(new FrontControllerSecurityIPListCommand());

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askAndValidate'));
        $dialog->expects($this->once())
            ->method('askAndValidate')
            ->will($this->returnValue($action));

        $command = $application->find('front-controller:security:ip:remove');
        $command->getHelperSet()->set($dialog, 'dialog');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--file' => vfsStream::url('.app_dev.security.json'),
        ));

        $output = json_decode(file_get_contents(vfsStream::url('.app_dev.security.json')));
        $this->assertCount(count($results), $output);

        foreach($results as $result){
            $this->assertContains($result, $commandTester->getDisplay());
        }
    }

    public function providerExecute()
    {
        return array(
            array('C', array('127.0.0.1', '192.168.1.1')),
            array('A', array()),
            array('1', array('192.168.1.1')),
            array('2', array('127.0.0.1')),
        );
    }
}
