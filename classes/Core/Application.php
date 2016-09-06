<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:31
 */

namespace Core;


class Application
{
    const STATE_LOGIN = 1;
    const STATE_BROWSE = 2;
    const STATE_REDIRECT = 3;
    const STATE_EMPLOYEE = 4;
    const STATE_EMPLOYEE_LIST = 5;
    const STATE_BOOK = 6;
    const STATE_DETAILS = 7;
    const STATE_DETAILS_RETURN = 8;

    private $state;
    private $siteRoot;
    private $session;
    private $redirectUrl;
    private $appData = array(); // application data, stores state of the app, used for render

    /**
     * @param $siteRoot string каталог сайта. Если сайт в корневом каталоге, то "/", в противном случае "/somedir"
     * @param Session $session объект сессии, используется для хранения глобального состояния
     */
    public function __construct($siteRoot, Session $session)
    {
        $this->siteRoot = $siteRoot;
        $this->session = $session;
        $this->state = self::STATE_LOGIN;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return array
     */
    public function getAppData()
    {
        return $this->appData;
    }

    public function setStateLogin(array $stateValues)
    {
        $this->state = self::STATE_LOGIN;
        $this->appData = array_merge($this->appData, $stateValues);
    }

    public function setStateBrowse(array $stateValues)
    {
        $this->state = self::STATE_BROWSE;
        $this->appData = array_merge($this->appData, $stateValues);
    }

    public function setStateEmployee(array $stateValues)
    {
        $this->state = self::STATE_EMPLOYEE;
        $this->appData = array_merge($this->appData, $stateValues);
        //error_log('appData:' . print_r($this->appData, true), 3, 'my_errors.txt');
    }

    public function setStateEmployeeList(array $stateValues)
    {
        $this->state = self::STATE_EMPLOYEE_LIST;
        $this->appData = array_merge($this->appData, $stateValues);
    }

    public function setStateBook(array $stateValues)
    {
        $this->state = self::STATE_BOOK;
        $this->appData = array_merge($this->appData, $stateValues);
    }

    public function setStateDetails(array $stateValues)
    {
        $this->state = self::STATE_DETAILS;
        $this->appData = array_merge($this->appData, $stateValues);
    }

    public function setStateDetailsReturn(array $stateValues)
    {
        $this->state = self::STATE_DETAILS_RETURN;
        $this->appData = array_merge($this->appData, $stateValues);
    }

    /**
     * @param $url string do not add site root here, it will be added during http->redirect call
     */
    public function setStateRedirect($url)
    {
        $this->state = self::STATE_REDIRECT;
        $this->redirectUrl = $url;
    }

    /**
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function isAuthorized()
    {
        return $this->session->keyExists('emp_id');
    }

    public function setAuthorized($emp_id, $is_admin, $first_day, $hour_mode)
    {
        $this->session->set('emp_id', $emp_id);
        $this->session->set('is_admin', $is_admin);
        $this->session->set('first_day', $first_day);
        $this->session->set('hour_mode', $hour_mode);
    }

    public function getEmpId()
    {
        return $this->session->get('emp_id');
    }

    public function isAdmin()
    {
        return $this->session->get('is_admin');
    }

    public function getFirstDay()
    {
        return $this->session->get('first_day');
    }

    public function getHourMode()
    {
        return $this->session->get('hour_mode');
    }

    public function setMessage($msg)
    {
        $this->session->set('message', $msg);
    }
    public  function getMessage()
    {
        return $this->session->keyExists('message') ? $this->session->get('message') : '';
    }
    public  function dropMessage()
    {
        $this->session->drop('message');
    }
    public function reopenSession()
    {
        $this->session->close();
        $this->session->open();
    }
    public function setCurrentRoom($roomId)
    {
        $this->session->set('room', $roomId);
    }
    public function getCurrentRoom()
    {
        return $this->session->keyExists('room') ? $this->session->get('room') : false;
    }
    public function setCurrentPeriod($date)
    {
        $this->session->set('period', $date);
    }
    public function getCurrentPeriod()
    {
        return $this->session->keyExists('period') ? $this->session->get('period') : new \DateTime();
    }
}