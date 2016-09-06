<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 16:51
 */
class BookingChangeTest extends PHPUnit_Framework_TestCase
{
    public function testTimeValidator()
    {
        $bc = new \Application\BookingChange();
        $bc->setStart('08:00');
        $bc->setEnd('09:00');
        $this->assertTrue($bc->isTimeValid());
        $bc->setEnd('00:00');
        $this->assertTrue($bc->isTimeValid());
        $bc->setEnd('00:01');
        $this->assertFalse($bc->isTimeValid());
        $bc->setStart('25:00');
        $bc->setEnd('26:00');
        $this->assertFalse($bc->isTimeValid());
        $bc->setStart('08:00');
        $bc->setEnd('26:00');
        $this->assertFalse($bc->isTimeValid());
    }
}
