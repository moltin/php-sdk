<?php

namespace Moltin\SDK\Tests;

use Moltin;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    private $underTest;

    public function setUp()
    {
        $this->underTest = new Moltin\Session();
    }

    public function testCanSetAndGetKey()
    {
        $this->underTest->setKey('test', []);
        $this->assertEquals([], $this->underTest->getKey('test'));
    }

    public function testGetUnsetKeyReturnsFalse()
    {
        $this->assertEquals(false, $this->underTest->getKey('nope'));
    }

    public function testRemoveKeyRemovesFromSessionCorrectly()
    {
        $this->underTest->setKey('tmp', 'value');
        $this->underTest->removeKey('tmp');
        $this->assertEquals(false, $this->underTest->getKey('tmp'));
    }

}
