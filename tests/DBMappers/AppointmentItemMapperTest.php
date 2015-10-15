<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 23:06
 */
class AppointmentItemMapperTest extends PHPUnit_Framework_TestCase
{
    private function clearDatabase(\Core\Database $db)
    {
        $db_host = 'localhost'; // mysql server host
        $db_name = 'bdr_test'; // database name
        $db_user = 'root'; // database user
        $db_password = ''; // user password
        $db = new \Core\Database(
            'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8',
            $db_user,
            $db_password,
            'appointments',
            '\\Utility\\DatabaseCreateScript');

    }
    public function testLoadItem()
    {

    }
}
