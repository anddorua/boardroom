<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 21:02
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    protected static $app;

    public function testSetState()
    {
        $sessionStub = $this->getMockBuilder('\\Core\\Session')->getMock();
        $app = new \Core\Application(array(), array(), '', $sessionStub);

    }
}
