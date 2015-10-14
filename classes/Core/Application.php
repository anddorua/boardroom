<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:31
 */

namespace Core;


use Utility\Template;

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

    const SECTION_REDIRECT = 'redirect_url';

    private $state;
    private $templateMap;
    private $vidgetViews;
    private $siteRoot;
    private $session;
    private $appData = array(); // application data, stores state of the app, used for render

    /**
     * Application constructor.
     */
    public function __construct($templateMap, $vidgetViews, $siteRoot, $session)
    {
        $this->templateMap = $templateMap;
        $this->vidgetViews = $vidgetViews;
        $this->siteRoot = $siteRoot;
        $this->session = $session;
        $this->state = self::STATE_LOGIN;
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

    public function setStateRedirect($url)
    {
        $this->state = self::STATE_REDIRECT;
        $this->appData[self::SECTION_REDIRECT] = $url;
    }

    /**
     * Returns rendered content of the vidget collection, put to section
     * @param $vidgetString string kind of 'Vidget1,Vidget2', found in data-vidgets attribute of the tag
     * @return string rendered content
     */
    private function getVidgetSectionContent($vidgetString, Registry $registry)
    {
        $vidgetList = explode(',', $vidgetString);
        $out = '';
        foreach($vidgetList as $vidgetName) {
            $out .= $this->getVidgetContent(trim($vidgetName), $registry);
        }
        return $out;
    }

    /**
     * Calls vidget and return its rendered output
     * @param $vidgetName string name of the vidget to load
     * @return string
     */
    private function getVidgetContent($vidgetName, Registry $registry)
    {
        $className = 'Vidgets\\' . $vidgetName;
        $vidgetTemplateName = $this->vidgetViews[$vidgetName];
        return (new $className())->render($this->appData, $vidgetTemplateName, $registry);
    }

    /**
     * @param $url
     * @param int $statusCode
     */
    private function redirect($url, $statusCode = 303)
    {
        $newLocation = $this->siteRoot . $url;
        //error_log("\nredirect to" . print_r($newLocation, true), 3, 'my_errors.txt');
        header('Location: ' . $newLocation, true, $statusCode);
        die();
    }
    /**
     * Renders current app state
     */
    public function renderState(Registry $registry)
    {
        if ($this->state == self::STATE_REDIRECT) {
            $this->redirect($this->appData[self::SECTION_REDIRECT]);
        }
        $out = '';
        if (!is_null($this->templateMap[$this->state])) {{
            //$templateContent = file_get_contents(TEMPLATE_ROOT . "/" . $this->templateMap[$this->state]);
            $templateContent = (new \Utility\Template())->parse($this->templateMap[$this->state], array('site_root' => $registry->get(REG_SITE_ROOT)));

            $foundVidgetPlaceCount = preg_match_all('<[\w\s="-;]*data-vidgets="([\w,\s]+)"[\w\s="-;]*>', $templateContent, $matches, PREG_OFFSET_CAPTURE);
            //error_log('matches:' . print_r($matches, true), 3, 'my_errors.txt');
            $prevPos = 0;
            for($i = 0; $i < $foundVidgetPlaceCount; $i++) {
                $vidgetContent = $this->getVidgetSectionContent($matches[1][$i][0], $registry);
                $tagLength = strlen($matches[0][$i][0]) + 1;
                $out .= substr($templateContent, $prevPos, $matches[0][$i][1] + $tagLength - $prevPos);
                $out .= $vidgetContent;
                $prevPos = $matches[0][$i][1] + $tagLength;
            };
            $out .= substr($templateContent, $prevPos);
        }}
        //error_log('out:' . print_r($out, true), 3, 'my_errors.txt');
        echo $out;
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

    public function setRoomSelected(Registry $registry, $room_id)
    {
        $registry->get(REG_SESSION)->set('room_id', $room_id);
    }

    public function getRoomSelected(Registry $registry)
    {
        return $registry->get(REG_SESSION)->get('room_id');
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
        return $this->session->drop('message');
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