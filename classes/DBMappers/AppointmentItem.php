<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 10.10.15
 * Time: 22:42
 */

namespace DBMappers;


class AppointmentItem extends ObjectMapper
{
    /**
     * @param $appId int
     * @return \Application\AppointmentItem|bool
     */
    public function getById($appId, \Core\Database $db)
    {
        if ($arr = $db->fetchFirstAssoc("select * from appointments where id=:id", array(":id" => $appId))) {
            return new \Application\AppointmentItem($arr);
        } else {
            return false;
        }
    }

    public function save(\Application\AppointmentItem $app, \Core\Database $db)
    {
        $fields_to_save = $app->toArray();
        unset($fields_to_save['id']);
        unset($fields_to_save['submitted']);
        if (is_null($app->getId()) || $app->getId() == '') {
            $this->makeInsertQuery('appointments', $fields_to_save, $db);
            $lid = $db->getLastInsertId();
            $app->fromArray(array($app->getIdFieldName() => $lid));
        } else {
            $this->makeUpdateQuery('appointments', $fields_to_save, array('id' => $app->getId()), $db);
        }
    }

    public function getDayAppointments($roomId, \DateTime $date, \Core\Database $db)
    {
        $ds = (new \DateTime())->setDate($date->format('Y'), $date->format('n'), $date->format('j'))->setTime(0,0,0);
        $de = \Utility\DateHelper::GetNextDay($ds);
        $ds_str = $ds->format('Y-m-d H:i:s');
        $de_str = $de->format('Y-m-d H:i:s');
        $sql = "select * from appointments where :ds <= time_start and time_start < :de and room_id = :rid order by time_start";
        if ($recs = $db->fetchAllAssoc($sql, array(":ds" => $ds_str, ":de" => $de_str, ":rid" => $roomId))) {
            $result = array();
            foreach($recs as $record) {
                $result[] = new \Application\AppointmentItem($record);
            }
            return $result;
        } else {
            return array();
        }
    }

    public function getChain($chainId, \Core\Database $db)
    {
        $sql = "select * from appointments where chain=:chain";
        if ($recs = $db->fetchAllAssoc($sql, array(":chain" => $chainId))) {
            $result = new \Application\AppointmentChain();
            foreach($recs as $record) {
                $result->add(new \Application\AppointmentItem($record));
            }
            return $result;
        } else {
            return new \Application\AppointmentChain();
        }
    }

    public function getMaxChainId(\Core\Database $db)
    {
        $sql = "select max(chain) from appointments";
        $res = $db->fetchFirstAssoc($sql, array());
        if ($res) {
            return $res['max(chain)'];
        } else {
            return false;
        }
    }

    public function deleteById($appId, \Core\Database $db)
    {
        $sql = 'delete from appointments where id=:id';
        $db->exec($sql, array(':id' => $appId));
    }

}