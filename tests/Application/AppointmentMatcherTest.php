<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 20.10.15
 * Time: 23:42
 */

namespace Application;


class AppointmentMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testMakeSingle()
    {
        $order = new \Application\BookingOrder();
        $order->setDate(2015, 10, 17);
        $order->setStartTime24(8, 15);
        $order->setEndTime24(9, 25);
        $order->setNotes('notes');
        $order->setEmpId(3);
        $order->setRecurring(BookingOrder::NOT_RECURRING, 2);
        $start = new \DateTime('2015-10-17 08:15:00');
        $end = new \DateTime('2015-10-17 09:25:00');
        $matcher = new \Application\AppointmentMatcher();
        $chain = $matcher->makeChain($order, 1, 2);
        $counted = 0;
        $step = new \DateInterval('P0D');
        foreach($chain as $res) {
            $this->assertEquals($start->getTimestamp(), $res->getTimeStart()->getTimestamp());
            $this->assertEquals($end->getTimestamp(), $res->getTimeEnd()->getTimestamp());
            $this->assertEquals('notes', $res->getNotes());
            $this->assertEquals(3, $res->getEmpId());
            $this->assertEquals(2, $res->getRoomId());
            $start->add($step);
            $end->add($step);
            ++$counted;
        }
        $this->assertEquals(1, $counted);
    }

    public function testMakeRecurringWeekly()
    {
        $order = new \Application\BookingOrder();
        $order->setDate(2015, 10, 17);
        $order->setStartTime24(8, 15);
        $order->setEndTime24(9, 25);
        $order->setNotes('notes');
        $order->setEmpId(3);
        $order->setRecurring(BookingOrder::RECURRING_WEEKLY, 2);
        $start = new \DateTime('2015-10-17 08:15:00');
        $end = new \DateTime('2015-10-17 09:25:00');
        $matcher = new \Application\AppointmentMatcher();
        $chain = $matcher->makeChain($order, 1, 2);
        $counted = 0;
        $step = new \DateInterval('P7D');
        foreach($chain as $res) {
            $this->assertEquals($start->getTimestamp(), $res->getTimeStart()->getTimestamp());
            $this->assertEquals($end->getTimestamp(), $res->getTimeEnd()->getTimestamp());
            $this->assertEquals('notes', $res->getNotes());
            $this->assertEquals(3, $res->getEmpId());
            $this->assertEquals(2, $res->getRoomId());
            $start->add($step);
            $end->add($step);
            ++$counted;
        }
        $this->assertEquals(3, $counted);
    }

    public function testMakeRecurringBiWeekly()
    {
        $order = new \Application\BookingOrder();
        $order->setDate(2015, 10, 17);
        $order->setStartTime24(8, 15);
        $order->setEndTime24(9, 25);
        $order->setNotes('notes');
        $order->setEmpId(3);
        $order->setRecurring(BookingOrder::RECURRING_BI_WEEKLY, 2);
        $start = new \DateTime('2015-10-17 08:15:00');
        $end = new \DateTime('2015-10-17 09:25:00');
        $matcher = new \Application\AppointmentMatcher();
        $chain = $matcher->makeChain($order, 1, 2);
        $counted = 0;
        $step = new \DateInterval('P14D');
        foreach($chain as $res) {
            $this->assertEquals($start->getTimestamp(), $res->getTimeStart()->getTimestamp());
            $this->assertEquals($end->getTimestamp(), $res->getTimeEnd()->getTimestamp());
            $this->assertEquals('notes', $res->getNotes());
            $this->assertEquals(3, $res->getEmpId());
            $this->assertEquals(2, $res->getRoomId());
            $start->add($step);
            $end->add($step);
            ++$counted;
        }
        $this->assertEquals(2, $counted);
    }

    public function testMakeRecurringMonthly()
    {
        $order = new \Application\BookingOrder();
        $order->setDate(2015, 10, 17);
        $order->setStartTime24(8, 15);
        $order->setEndTime24(9, 25);
        $order->setNotes('notes');
        $order->setEmpId(3);
        $order->setRecurring(BookingOrder::RECURRING_MONTHLY, 2);
        $start = new \DateTime('2015-10-17 08:15:00');
        $end = new \DateTime('2015-10-17 09:25:00');
        $matcher = new \Application\AppointmentMatcher();
        $chain = $matcher->makeChain($order, 1, 2);
        $counted = 0;
        $step = new \DateInterval('P1M');
        foreach($chain as $res) {
            $this->assertEquals($start->getTimestamp(), $res->getTimeStart()->getTimestamp());
            $this->assertEquals($end->getTimestamp(), $res->getTimeEnd()->getTimestamp());
            $this->assertEquals('notes', $res->getNotes());
            $this->assertEquals(3, $res->getEmpId());
            $this->assertEquals(2, $res->getRoomId());
            $start->add($step);
            $end->add($step);
            ++$counted;
        }
        $this->assertEquals(3, $counted);
    }
}
