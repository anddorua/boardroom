<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 13:13
 */
class AppointmentItemTest extends PHPUnit_Framework_TestCase
{
    protected static $arrayToTest = array(
        'id' => 1,
        'emp_id' => '2',
        'time_start' => '2015-10-15 10:25:00',
        'time_end' => '2015-10-15 11:45:00',
        'notes' => 'some notes',
        'creator_id' => 3,
        'chain' => 5,
        'room_id' => 4,
        'submitted' => '2015-10-15 09:25:00',
    );
    public function testCreation()
    {
        $app = new \Application\AppointmentItem(self::$arrayToTest);
        $this->assertEquals(1, $app->getId());
        $this->assertEquals(2, $app->getEmpId());
        $this->assertEquals(new \DateTime('2015-10-15 10:25:00'), $app->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-15 11:45:00'), $app->getTimeEnd());
        $this->assertEquals('some notes', $app->getNotes());
        $this->assertEquals(5, $app->getChain());
        $this->assertEquals(4, $app->getRoomId());
        $this->assertEquals(new \DateTime('2015-10-15 09:25:00'), $app->getSubmitted());
        return $app;
    }
    /**
     * @depends testCreation
     * @param \Application\AppointmentItem $app
     * @return \Application\AppointmentItem
     */
    public function testArrayFunctions(\Application\AppointmentItem $app)
    {
        $arr = $app->toArray();
        $subset = array_merge(array(), self::$arrayToTest);
        $this->assertArraySubset($subset, $arr);
        return $app;
    }

    /**
     * @depends testArrayFunctions
     * @param \Application\AppointmentItem $app
     */
    public function testSetters(\Application\AppointmentItem $app)
    {
        $app->setTimeStart(new \DateTime('2015-10-15 10:27:00'));
        $this->assertEquals(new \DateTime('2015-10-15 10:27:00'), $app->getTimeStart());
        $app->setTimeEnd(new \DateTime('2015-10-15 10:28:00'));
        $this->assertEquals(new \DateTime('2015-10-15 10:28:00'), $app->getTimeEnd());
        $app->setChain(6);
        $this->assertEquals(6, $app->getChain());
        $app->setNotes('other note');
        $this->assertEquals('other note', $app->getNotes());
        $app->setEmpId(20);
        $this->assertEquals(20, $app->getEmpId());
    }

    public function testCrossing()
    {
        // 'time_start' => '2015-10-15 10:25:00',
        // 'time_end' => '2015-10-15 11:45:00',
        $app = new \Application\AppointmentItem(self::$arrayToTest);
        $this->assertTrue($app->isCrossing(new \DateTime('2015-10-15 10:25:00'), new \DateTime('2015-10-15 11:45:00')));
        $this->assertTrue($app->isCrossing(new \DateTime('2015-10-15 10:25:00'), new \DateTime('2015-10-15 11:40:00')));
        $this->assertTrue($app->isCrossing(new \DateTime('2015-10-15 10:30:00'), new \DateTime('2015-10-15 11:50:00')));
        $this->assertTrue($app->isCrossing(new \DateTime('2015-10-15 10:30:00'), new \DateTime('2015-10-15 11:45:00')));
        $this->assertTrue($app->isCrossing(new \DateTime('2015-10-15 10:20:00'), new \DateTime('2015-10-15 11:40:00')));
        $this->assertFalse($app->isCrossing(new \DateTime('2015-10-15 09:25:00'), new \DateTime('2015-10-15 10:20:00')));
        $this->assertFalse($app->isCrossing(new \DateTime('2015-10-15 09:25:00'), new \DateTime('2015-10-15 10:25:00')));
        $this->assertFalse($app->isCrossing(new \DateTime('2015-10-15 11:50:00'), new \DateTime('2015-10-15 12:00:00')));
        $this->assertFalse($app->isCrossing(new \DateTime('2015-10-15 11:45:00'), new \DateTime('2015-10-15 12:00:00')));
    }

    public function testSetNewTime()
    {
        // 'time_start' => '2015-10-15 10:25:00',
        // 'time_end' => '2015-10-15 11:45:00',
        $app = new \Application\AppointmentItem(self::$arrayToTest);
        $app->setNewTime('12:30', '13:05');
        $this->assertEquals(new \DateTime('2015-10-15 12:30:00'), $app->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-15 13:05:00'), $app->getTimeEnd());
        $app->setNewTime('08:15', '00:00');
        $this->assertEquals(new \DateTime('2015-10-15 08:15:00'), $app->getTimeStart());
        $this->assertEquals(new \DateTime('2015-10-16 00:00:00'), $app->getTimeEnd());
    }
}
