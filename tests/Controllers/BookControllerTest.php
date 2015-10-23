<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 20:45
 */
require_once("include/constants.php");

class BookControllerTest extends PHPUnit_Framework_TestCase
{
    protected static $db;

    /**
     * @return \Core\Database
     */
    private static function getClearDatabase()
    {
        $db = new \Core\Database(
            'mysql:host=' . TEST_DB_HOST . ';dbname=' . TEST_DB_NAME . ';charset=utf8',
            TEST_DB_USER, TEST_DB_PASSWORD, 'appointments', '\\Utility\\DatabaseCreateScript');
        self::clearTables($db);
        return $db;
    }

    private static function clearTables(\Core\Database $db)
    {
        $clearScript = new \Helpers\TablesClearScript();
        foreach ($clearScript as $statement) {
            $db->exec($statement, array());
        }
    }

    public static function setUpBeforeClass()
    {
        self::$db = self::getClearDatabase();
    }

    public static function tearDownAfterClass()
    {
        self::$db->close();
    }

    public function testActGet()
    {
        $http = $this->getMockBuilder('\\Core\\Http')->getMock();
        $session = $this->getMockBuilder('\\Core\\Session')->getMock();
        $http->method('getRequestMethod')->willReturn('GET');
        $app = $this->getMockBuilder('\\Core\\Application')
            ->setConstructorArgs(array('', $session))
            ->setMethods(array('setStateBook'))
            ->getMock();
        $app->expects($this->once())
            ->method('setStateBook')
            ->with($this->equalTo(array()));
        $db = $this->getMockBuilder('\\Core\\Database')
            ->disableOriginalConstructor()
            ->getMock();
        $appMapper = $this->getMockBuilder('\\DBMappers\\AppointmentItem')
            ->setConstructorArgs(array('appointments'))
            ->getMock();
        $empItemMapper = $this->getMockBuilder('\\DBMappers\\EmpItem')
            ->setConstructorArgs(array('employees'))
            ->getMock();
        $c = new \Controllers\Book();
        $c->act(array(), $http, $app, $db, $appMapper, $empItemMapper);
    }

    public function testActPostEmptyForm()
    {
        $session = $this->getMockBuilder('\\Core\\Session')->getMock();
        $http = $this->getMockBuilder('\\Core\\Http')->getMock();
        $http->method('getRequestMethod')->willReturn('POST');
        $http->method('post')->willReturn(array(
            'employee' => '',
            'start-year' => '0',
            'start-month' => '0',
            'start-day' => '0',
            'start-hour-12' => '',
            'start-meridiem' => '',
            'start-hour-24' => '',
            'end-hour-12' => '',
            'end-meridiem' => '',
            'end-hour-24' => '',
            'end-minute' => '',
            'notes' => '',
            'recurring' => 1,
            'recurring-period' => 1,
            'duration' => '',
        ));
        $app = $this->getMockBuilder('\\Core\\Application')
            ->setConstructorArgs(array('', $session))
            ->setMethods(array('setStateBook'))
            ->getMock();
        $app->expects($this->once())
            ->method('setStateBook')
            ->with($this->callback(function(array $arr){
                return isset($arr['book_values'])
                    && isset($arr['book_errors'])
                    && (isset($arr['error_message']) || is_null($arr['error_message']))
                    && is_array($arr['book_errors'])
                    && count($arr['book_errors']) > 0;
            }));
        $db = $this->getMockBuilder('\\Core\\Database')
            ->disableOriginalConstructor()
            ->getMock();
        $appMapper = $this->getMockBuilder('\\DBMappers\\AppointmentItem')
            ->setConstructorArgs(array('appointments'))
            ->getMock();
        $empItemMapper = $this->getMockBuilder('\\DBMappers\\EmpItem')
            ->setConstructorArgs(array('employees'))
            ->getMock();
        $c = new \Controllers\Book();
        $c->act(array(), $http, $app, $db, $appMapper, $empItemMapper);
    }

    public function succeedFormProvider()
    {
        $imp = new \Helpers\CsvImporter('tests/Controllers/BookSingleEventRight.csv', true,',');
        $formDataRight = $imp->get();
        $imp = new \Helpers\CsvImporter('tests/Controllers/BookSingleEventRightEvents.csv', true,',');
        $evtDataRight = $imp->get();
        $result = array(
            array($formDataRight[0], $evtDataRight[0]),
        );
        //print_r($result);
        return $result;
    }

    /**
     * @dataProvider succeedFormProvider
     */
    public function testPostSuccessful($formData, $eventData)
    {
        //print_r("\nformdata:");
        //print_r($formData);
        self::clearTables(self::$db);
        $session = $this->getMockBuilder('\\Core\\Session')->getMock();
        $http = $this->getMockBuilder('\\Core\\Http')->getMock();
        $http->method('getRequestMethod')->willReturn('POST');
        $http->method('post')->willReturn($formData);
        $app = $this->getMockBuilder('\\Core\\Application')
            ->setConstructorArgs(array('', $session))
            ->setMethods(array('setStateBook', 'getEmpId', 'getCurrentRoom'))
            ->getMock();
        $app->method('getEmpId')->willReturn($eventData['creator_id']);
        $app->method('getCurrentRoom')->willReturn($eventData['room_id']);
        $appMapper = $this->getMockBuilder('\\DBMappers\\AppointmentItem')
            ->setConstructorArgs(array('appointments'))
            ->setMethods(array('getMaxChainId', 'getDayAppointments'))
            ->getMock();
        $appMapper->method('getMaxChainId')->willReturn($eventData['chain'] - 1);
        $appMapper->method('getDayAppointments')->willReturn(array());
        $empItemMapper = $this->getMockBuilder('\\DBMappers\\EmpItem')
            ->setConstructorArgs(array('employees'))
            ->getMock();
        $c = new \Controllers\Book();
        $c->act(array(), $http, $app, self::$db, $appMapper, $empItemMapper);
        $app2 = (new \DBMappers\AppointmentItem('appointments'))->getDayAppointments($eventData['room_id'], new \DateTime($eventData['time_start']), self::$db);
        $this->assertCount(1, $app2);
        $this->assertEquals($eventData['emp_id'],$app2[0]->getEmpId());
        $this->assertEquals($eventData['notes'],$app2[0]->getNotes());
        $this->assertEquals($eventData['chain'],$app2[0]->getChain());
        $this->assertEquals($eventData['room_id'],$app2[0]->getRoomId());
        $this->assertEquals((new \DateTime($eventData['time_start']))->getTimestamp(),$app2[0]->getTimeStart()->getTimestamp());
        $this->assertEquals((new \DateTime($eventData['time_end']))->getTimestamp(),$app2[0]->getTimeEnd()->getTimestamp());
    }
}
