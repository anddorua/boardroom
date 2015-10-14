<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 10.10.15
 * Time: 22:42
 */

namespace DBMappers;


class EmpItem extends ObjectMapper
{
    /**
     * @param $empId int
     * @return \Application\EmpItem|bool
     */
    public function getById($empId, \Core\Database $db)
    {
        if ($arr = $db->fetchFirstAssoc("select * from employees where id=:id", array(":id" => $empId))) {
            return new \Application\EmpItem($arr);
        } else {
            return false;
        }
    }

    public function getAll(\Core\Database $db)
    {
        if ($recs = $db->fetchAllAssoc("select * from employees", array())) {
            $result = array();
            foreach($recs as $record) {
                $result[] = new \Application\EmpItem($record);
            }
            return $result;
        } else {
            return array();
        }
    }

    public function getByLogin($login, \Core\Database $db)
    {
        if ($arr = $db->fetchFirstAssoc("select * from employees where login=:login", array(":login" => $login))) {
            return new \Application\EmpItem($arr);
        } else {
            return false;
        }
    }

    public function save(\Application\EmpItem $emp, \Core\Database $db)
    {
        $fields_to_save = $emp->toArray();
        unset($fields_to_save['id']);
        if (is_null($emp->getId()) || $emp->getId() == '') {
            return $this->makeInsertQuery('employees', $fields_to_save, $db);
        } else {
            return $this->makeUpdateQuery('employees', $fields_to_save, array('id' => $emp->getId()), $db);
        }
    }

    public function remove($empId, \Core\Database $db)
    {
        $sql = "delete from employees where id=:id";
        $db->exec($sql, array("id" => $empId));
    }
}