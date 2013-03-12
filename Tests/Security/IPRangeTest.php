<?php

namespace MS\Bundle\FrontControllerSecurityBundle\Tests\Security;

use MS\Bundle\FrontControllerSecurityBundle\Tests\TestCase;

use MS\Bundle\FrontControllerSecurityBundle\Security\IPRange;

/**
 * @author msmith
 * @created 3/11/13 6:01 PM
 */
class IPRangeTest extends TestCase
{
    public function testConstruct()
    {
        $range = new IPRange('127.0.0.1', '127.0.0.1', '2013-03-11', 'my message');

        $this->assertInstanceOf('\MS\Bundle\FrontControllerSecurityBundle\Security\IPRange', $range);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage beginning
     */
    public function testConstructExceptionBegin()
    {
        $range = new IPRange('127.000.000.1', '127.0.0.1');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage ending
     */
    public function testConstructExceptionEnd()
    {
        $range = new IPRange('127.0.0.1', '127.000.000.1');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage expire
     */
    public function testConstructExceptionExpire()
    {
        $range = new IPRange('127.0.0.1', '127.0.0.1', 'abc');
    }

    public function testIsAuthorized()
    {
        $range = new IPRange('127.0.0.1', '127.0.0.1');

        $this->assertTrue($range->isAuthorized('127.0.0.1'));
        $this->assertFalse($range->isAuthorized('127.0.0.2'));


        $range = new IPRange('127.0.0.1', '127.0.0.255');

        $this->assertTrue($range->isAuthorized('127.0.0.1'));
        $this->assertTrue($range->isAuthorized('127.0.0.2'));


        $range = new IPRange('127.0.0.1', '127.0.0.1', '+1 week');

        $this->assertTrue($range->isAuthorized('127.0.0.1'));


        $range = new IPRange('127.0.0.1', '127.0.0.1', '-1 week');

        $this->assertFalse($range->isAuthorized('127.0.0.1'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsAuthorizedException()
    {
        $range = new IPRange('127.0.0.1', '127.0.0.1');

        $range->isAuthorized('127.000.000.1');
    }
}
