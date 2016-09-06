<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 13:06
 */
class RoomItemTest extends PHPUnit_Framework_TestCase
{
    protected static $arrayToTest = array(
        'id' => 1,
        'room_name' => 'some_room_name'
    );
    public function testCreation()
    {
        $room = new \Application\RoomItem(self::$arrayToTest);
        $this->assertEquals(1, $room->getId());
        $this->assertEquals('some_room_name', $room->getRoomName());
        return $room;
    }

    /**
     * @depends testCreation
     * @param \Application\RoomItem $room
     */
    public function testArrayFunctions(\Application\RoomItem $room)
    {
        $arr = $room->toArray();
        $subset = array_merge(array(), self::$arrayToTest);
        $this->assertArraySubset($subset, $arr);
    }

}
