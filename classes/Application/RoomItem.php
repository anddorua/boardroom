<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 10.10.15
 * Time: 23:24
 */

namespace Application;


class RoomItem extends ArrayCapable
{
    protected $id = null;
    protected $room_name;

    public function __construct($param)
    {
        $this->fromArray($param);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRoomName()
    {
        return $this->room_name;
    }


}