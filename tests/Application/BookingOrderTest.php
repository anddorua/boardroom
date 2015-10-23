<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 20.10.15
 * Time: 0:28
 */

namespace Application;


class BookingOrderTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $t = new \Application\BookingOrder();
        $t->setEmpId(1);
        $this->assertEquals(1, $t->getEmpId());
        $t->setRecurring(1, 3);
        $this->assertEquals(1, $t->getRecurring());
        $this->assertEquals(3, $t->getDuration());
        $t->setRecurring(1, 5);
        $this->assertEquals(1, $t->getRecurring());
        $this->assertEquals(4, $t->getDuration());
        $t->setRecurring(2, 2);
        $this->assertEquals(2, $t->getRecurring());
        $this->assertEquals(2, $t->getDuration());
        $t->setRecurring(2, 3);
        $this->assertEquals(2, $t->getRecurring());
        $this->assertEquals(2, $t->getDuration());
        $t->setRecurring(2, 1);
        $this->assertEquals(2, $t->getRecurring());
        $this->assertEquals(0, $t->getDuration());
        $t->setRecurring(2, 6);
        $this->assertEquals(2, $t->getRecurring());
        $this->assertEquals(4, $t->getDuration());
        $t->setRecurring(3, 1);
        $this->assertEquals(3, $t->getRecurring());
        $this->assertEquals(1, $t->getDuration());
        $t->setRecurring(3, 7);
        $this->assertEquals(3, $t->getRecurring());
        $this->assertEquals(7, $t->getDuration());
        $t->setNotes('note');
        $this->assertEquals('note', $t->getNotes());
        $t->setDate(2015, 1, 2);
        $t->setStartTime24(10, 20);
        $this->assertEquals((new \DateTime('2015-01-02 10:20:00'))->getTimestamp(), $t->getStartTime()->getTimestamp());
        $t->setEndTime24(11, 22);
        $this->assertEquals((new \DateTime('2015-01-02 11:22:00'))->getTimestamp(), $t->getEndTime()->getTimestamp());
        $t->setStartTime12(8, 25, 'am');
        $this->assertEquals((new \DateTime('2015-01-02 08:25:00'))->getTimestamp(), $t->getStartTime()->getTimestamp());
        $t->setEndTime12(9, 35, 'pm');
        $this->assertEquals((new \DateTime('2015-01-02 21:35:00'))->getTimestamp(), $t->getEndTime()->getTimestamp());
        $t->setEndTime12(12, 00, 'pm');
        $this->assertEquals((new \DateTime('2015-01-03 00:00:00'))->getTimestamp(), $t->getEndTime()->getTimestamp());
        $t->setEndTime12(12, 15, 'pm');
        $this->assertEquals((new \DateTime('2015-01-02 00:15:00'))->getTimestamp(), $t->getEndTime()->getTimestamp());
        $this->assertFalse($t->isTimeValid());
        $t->setEndTime12(8, 30, 'am');
        $this->assertTrue($t->isTimeValid());
        $this->assertTrue($t->isPeriodBeforeTime(new \DateTime('2015-01-02 08:30:00')));
        $this->assertTrue($t->isPeriodBeforeTime(new \DateTime('2015-01-02 08:40:00')));
        $this->assertFalse($t->isPeriodBeforeTime(new \DateTime('2015-01-02 08:27:00')));
        $this->assertFalse($t->isPeriodBeforeTime(new \DateTime('2015-01-02 08:25:00')));
        $this->assertFalse($t->isPeriodBeforeTime(new \DateTime('2015-01-01 08:27:00')));
    }
}
