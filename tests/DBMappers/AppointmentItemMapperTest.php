<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 23:06
 */
class AppointmentItemMapperTest extends PHPUnit_Framework_TestCase
{
    protected static $db;

    /**
     * @return \Core\Database
     */
    protected static function getClearDatabase()
    {
        $db_host = TEST_DB_HOST; // mysql server host
        $db_name = TEST_DB_NAME; // database name
        $db_user = TEST_DB_USER; // database user
        $db_password = TEST_DB_PASSWORD; // user password
        $db = new \Core\Database(
            'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8',
            $db_user,
            $db_password,
            'appointments',
            '\\Utility\\DatabaseCreateScript');
        $clearScript = new \Helpers\TablesClearScript();
        foreach ($clearScript as $statement) {
            $db->exec($statement, array());
        }
        return $db;
    }

    public static function setUpBeforeClass()
    {
        self::$db = self::getClearDatabase();
    }

    public static function tearDownAfterClass()
    {
        self::$db->close();
    }
    public function testAbsentLoadItem()
    {
        $m = new \DBMappers\AppointmentItem();
        $this->assertFalse($m->getById(1, self::$db));
    }
}
