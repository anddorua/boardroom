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
        $db = new \Core\Database(
            'mysql:host=' . TEST_DB_HOST . ';dbname=' . TEST_DB_NAME . ';charset=utf8',
            TEST_DB_USER, TEST_DB_PASSWORD, 'appointments', '\\Utility\\DatabaseCreateScript');
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
        $m = new \DBMappers\AppointmentItem("appointments");
        $this->assertFalse($m->getById(1, self::$db));
    }
}
