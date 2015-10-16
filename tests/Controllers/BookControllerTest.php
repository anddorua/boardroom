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
        $registry = new \Core\Registry();
        $registry->set(REG_APP, $app);
        $c = new \Controllers\Book();
        $c->act($registry, array(), $http);
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
        $registry = new \Core\Registry();
        $registry->set(REG_APP, $app);
        $c = new \Controllers\Book();
        $c->act($registry, array(), $http);
    }
}
