<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 15:22
 */
class AppointmentChainTest extends PHPUnit_Framework_TestCase
{
    protected static $arrayToTest1 = array(
        'id' => 10,
        'emp_id' => '2',
        'time_start' => '2015-10-15 10:25:00',
        'time_end' => '2015-10-15 11:45:00',
        'notes' => 'some notes',
        'creator_id' => 3,
        'chain' => 5,
        'room_id' => 4,
        'submitted' => '2015-10-15 09:25:00',
    );
    protected static $arrayToTest2 = array(
        'id' => 20,
        'emp_id' => '2',
        'time_start' => '2015-10-16 10:25:00',
        'time_end' => '2015-10-16 11:45:00',
        'notes' => 'some notes',
        'creator_id' => 3,
        'chain' => 5,
        'room_id' => 4,
        'submitted' => '2015-10-15 09:25:00',
    );
    protected static $arrayToTest3 = array(
        'id' => 30,
        'emp_id' => '2',
        'time_start' => '2015-10-17 10:25:00',
        'time_end' => '2015-10-17 11:45:00',
        'notes' => 'some notes',
        'creator_id' => 3,
        'chain' => 5,
        'room_id' => 4,
        'submitted' => '2015-10-15 09:25:00',
    );
    public function testUnfilteredInterface()
    {
        $chain = new \Application\AppointmentChain();
        $item1 = new \Application\AppointmentItem(self::$arrayToTest1);
        $item2 = new \Application\AppointmentItem(self::$arrayToTest2);
        $item3 = new \Application\AppointmentItem(self::$arrayToTest3);
        $chain->add($item1);
        $chain->add($item2);
        $chain->add($item3);
        $chain->rewind();
        $this->assertEquals(3, $chain->count());
        $this->assertEquals($item1, $chain->current());
        $chain->next();
        $this->assertEquals($item2, $chain->current());
        $chain->next();
        $this->assertEquals($item3, $chain->current());
        $chain->next();
        $this->assertFalse($chain->valid());
        $this->assertTrue($chain->isMeetFilter($item1));
        $this->assertTrue($chain->isMeetFilter($item2));
        $this->assertTrue($chain->isMeetFilter($item3));
        $this->assertEquals($item1, $chain->get(10));
        $this->assertEquals($item2, $chain->get(20));
        $this->assertEquals($item3, $chain->get(30));
        $this->assertEquals(null, $chain->get(40));
        $chain->setChainId(50);
        $this->assertEquals(50, $item1->getChain());
        $this->assertEquals(50, $item2->getChain());
        $this->assertEquals(50, $item3->getChain());
    }

    public function testUnfilteredChange()
    {
        $chain = new \Application\AppointmentChain();
        $item1 = new \Application\AppointmentItem(self::$arrayToTest1);
        $item2 = new \Application\AppointmentItem(self::$arrayToTest2);
        $item3 = new \Application\AppointmentItem(self::$arrayToTest3);
        $chain->add($item1);
        $chain->add($item2);
        $chain->add($item3);
        $bc = new \Application\BookingChange();
        $bc->setStart('15:00');
        $bc->setEnd('16:25');
        $bc->setEmpId(3);
        $bc->setNotes('new notes');
        $chain->applyChange($bc);
        $this->assertEquals(3,$item1->getEmpId());
        $this->assertEquals(3,$item2->getEmpId());
        $this->assertEquals(3,$item3->getEmpId());
        $this->assertEquals('new notes',$item1->getNotes());
        $this->assertEquals('new notes',$item2->getNotes());
        $this->assertEquals('new notes',$item3->getNotes());
        $this->assertEquals(new \DateTime('2015-10-15 15:00:00'),$item1->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-16 15:00:00'),$item2->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-17 15:00:00'),$item3->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-15 16:25:00'),$item1->getTimeEnd());
        $this->assertEquals(new \DateTime('2015-10-16 16:25:00'),$item2->getTimeEnd());
        $this->assertEquals(new \DateTime('2015-10-17 16:25:00'),$item3->getTimeEnd());
    }

    public function testFilteredChange()
    {
        $chain = new \Application\AppointmentChain();
        $item1 = new \Application\AppointmentItem(self::$arrayToTest1);
        $item2 = new \Application\AppointmentItem(self::$arrayToTest2);
        $item3 = new \Application\AppointmentItem(self::$arrayToTest3);
        $chain->add($item1);
        $chain->add($item2);
        $chain->add($item3);
        $chain->applyFilter(new \DateTime('2015-10-15 11:45:00'));
        $bc = new \Application\BookingChange();
        $bc->setStart('15:00');
        $bc->setEnd('16:25');
        $bc->setEmpId(3);
        $bc->setNotes('new notes');
        $chain->applyChange($bc);
        $this->assertEquals(2,$item1->getEmpId());
        $this->assertEquals(3,$item2->getEmpId());
        $this->assertEquals(3,$item3->getEmpId());
        $this->assertEquals('some notes',$item1->getNotes());
        $this->assertEquals('new notes',$item2->getNotes());
        $this->assertEquals('new notes',$item3->getNotes());
        $this->assertEquals(new \DateTime('2015-10-15 10:25:00'),$item1->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-16 15:00:00'),$item2->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-17 15:00:00'),$item3->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-15 11:45:00'),$item1->getTimeEnd());
        $this->assertEquals(new \DateTime('2015-10-16 16:25:00'),$item2->getTimeEnd());
        $this->assertEquals(new \DateTime('2015-10-17 16:25:00'),$item3->getTimeEnd());
    }

    public function testFilteredInterface()
    {
        $chain = new \Application\AppointmentChain();
        $item1 = new \Application\AppointmentItem(self::$arrayToTest1);
        $item2 = new \Application\AppointmentItem(self::$arrayToTest2);
        $item3 = new \Application\AppointmentItem(self::$arrayToTest3);
        $chain->add($item1);
        $chain->add($item2);
        $chain->add($item3);
        $chain->applyFilter(new \DateTime('2015-10-15 12:00:00'));
        $chain->rewind();
        $this->assertEquals(2, $chain->count());
        $this->assertEquals($item2, $chain->current());
        $chain->next();
        $this->assertEquals($item3, $chain->current());
        $chain->next();
        $this->assertFalse($chain->valid());
        $this->assertFalse($chain->isMeetFilter($item1));
        $this->assertTrue($chain->isMeetFilter($item2));
        $this->assertTrue($chain->isMeetFilter($item3));
        $this->assertEquals($item1, $chain->get(10));
        $this->assertEquals($item2, $chain->get(20));
        $this->assertEquals($item3, $chain->get(30));
        $this->assertEquals(null, $chain->get(40));
        $chain->setChainId(50);
        $this->assertEquals(50, $item1->getChain());
        $this->assertEquals(50, $item2->getChain());
        $this->assertEquals(50, $item3->getChain());
    }
}
