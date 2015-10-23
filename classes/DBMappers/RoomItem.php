<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 10.10.15
 * Time: 22:42
 */

namespace DBMappers;


class RoomItem extends ObjectMapper
{
    /**
     * @param $roomId int
     * @return \Application\EmpItem|bool
     */
    public function getById($roomId, \Core\Database $db)
    {
        if ($arr = $db->fetchFirstAssoc("select * from rooms where id=:id", array(":id" => $roomId))) {
            return new \Application\RoomItem($arr);
        } else {
            return false;
        }
    }

    public function getAll(\Core\Database $db)
    {
        $recs = $db->fetchAllAssoc("select * from rooms", array());
        $result = array();
        for ($i = 0; $i < count($recs); $i++) {
            $result[] = new \Application\RoomItem($recs[$i]);
        }
        return $result;
    }

    public function save(\Application\RoomItem $room, \Core\Database $db)
    {
        $fields_to_save = $room->toArray();
        unset($fields_to_save[$room->getIdFieldName()]);
        if (is_null($room->getId()) || $room->getId() == '') {
            $this->makeInsertQuery('rooms', $fields_to_save, $db);
            $lid = $db->getLastInsertId();
            $room->fromArray(array($room->getIdFieldName() => $lid));
        } else {
            $this->makeUpdateQuery('rooms', $fields_to_save, array('id' => $room->getId()), $db);
        }
    }

}