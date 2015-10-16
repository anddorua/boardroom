<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 21:02
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    protected static $app;

    public function testSetState()
    {
        $stateData = array(
            'login_key' => 'login_value',
            'book_key' => 'book_value',
            'browse_key' => 'browse_value',
            'det_key' => 'det_value',
            'det_ret_key' => 'det_ret_value',
            'emp_key' => 'emp_value',
            'empl_key' => 'empl_value'
        );
        $sessionStub = $this->getMockBuilder('\\Core\\Session')->getMock();
        $app = new \Core\Application('', $sessionStub);
        $app->setStateLogin(array('login_key' => 'login_value'));
        $this->assertEquals(\Core\Application::STATE_LOGIN, $app->getState());
        $app->setStateBook(array('book_key' => 'book_value'));
        $this->assertEquals(\Core\Application::STATE_BOOK, $app->getState());
        $app->setStateBrowse(array('browse_key' => 'browse_value'));
        $this->assertEquals(\Core\Application::STATE_BROWSE, $app->getState());
        $app->setStateDetails(array('det_key' => 'det_value'));
        $this->assertEquals(\Core\Application::STATE_DETAILS, $app->getState());
        $app->setStateDetailsReturn(array('det_ret_key' => 'det_ret_value'));
        $this->assertEquals(\Core\Application::STATE_DETAILS_RETURN, $app->getState());
        $app->setStateEmployee(array('emp_key' => 'emp_value'));
        $this->assertEquals(\Core\Application::STATE_EMPLOYEE, $app->getState());
        $app->setStateEmployeeList(array('empl_key' => 'empl_value'));
        $this->assertEquals(\Core\Application::STATE_EMPLOYEE_LIST, $app->getState());
        $app->setStateRedirect('http');
        $this->assertEquals(\Core\Application::STATE_REDIRECT, $app->getState());
        $this->assertEquals('http', $app->getRedirectUrl());
        $this->assertArraySubset($stateData, $app->getAppData());
    }
}
