<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Tests\Security;

use MS\Bundle\FrontControllerSecurityBundle\Tests\TestCase;

use MS\Bundle\FrontControllerSecurityBundle\Security\IPChecker;
use MS\Bundle\FrontControllerSecurityBundle\Security\IPRange;
use org\bovigo\vfs\vfsStream;

/**
 * @author msmith
 * @created 3/11/13 6:01 PM
 */
class IPCheckerTest extends TestCase
{
    public function testAddIPRangeObject()
    {
        $checker = new IPChecker();

        $checker->addIPRangeObject(new IPRange('127.0.0.1', '127.0.0.1'));

        $this->assertTrue($checker->isAuthorized('127.0.0.1'));
        $this->assertFalse($checker->isAuthorized('127.0.0.2'));
    }

    public function testAddIPRange()
    {
        $checker = new IPChecker();

        $checker->addIPRange('127.0.0.1', '127.0.0.1');

        $this->assertTrue($checker->isAuthorized('127.0.0.1'));
        $this->assertFalse($checker->isAuthorized('127.0.0.2'));
    }

    public function testAddIP()
    {
        $checker = new IPChecker();

        $checker->addIP('127.0.0.1');

        $this->assertTrue($checker->isAuthorized('127.0.0.1'));
        $this->assertFalse($checker->isAuthorized('127.0.0.2'));
    }

    public function testAddJSON()
    {
        $checker = new IPChecker();

        $checker->addJSON(json_encode(array(
            array('begin' => '127.0.0.1', 'end' => '127.0.0.1'),
            array('begin' => '192.168.1.1', 'end' => '192.168.1.255'),
        )));

        $this->assertTrue($checker->isAuthorized('127.0.0.1'));
        $this->assertFalse($checker->isAuthorized('127.0.0.2'));
        $this->assertTrue($checker->isAuthorized('192.168.1.100'));
        $this->assertFalse($checker->isAuthorized('192.168.2.1'));
    }

    public function testAddFile()
    {
        vfsStream::setup('root', null, array('.app_dev.security.json' => json_encode(array(
            array('begin' => '127.0.0.1', 'end' => '127.0.0.1'),
            array('begin' => '192.168.1.1', 'end' => '192.168.1.255'),
        ))));

        $checker = new IPChecker();

        $checker->addFile(vfsStream::url('.app_dev.security.json'));

        $this->assertTrue($checker->isAuthorized('127.0.0.1'));
        $this->assertFalse($checker->isAuthorized('127.0.0.2'));
        $this->assertTrue($checker->isAuthorized('192.168.1.100'));
        $this->assertFalse($checker->isAuthorized('192.168.2.1'));
    }

}
