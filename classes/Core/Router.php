<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 20:37
 */

namespace Core;


class Router
{
    private $controllerName;
    private $actionName;
    private $urlParameters;

    public static function toLowerCamelCase($src)
    {
        return preg_replace_callback('(\-[a-z])', function($matches){
            return strtoupper(substr($matches[0], 1));
        }, strtolower($src));
    }

    public static function toUpperCamelCase($src)
    {
        return ucfirst(self::toLowerCamelCase($src));
    }

    private function parsePath(Http $http)
    {
        if (isset($http->get()['route'])) {
            $route = trim($http->get()['route'], "/\\");
            //error_log("\nroute:" . $route, 3, 'my_errors.txt');
            $routeList = explode('/', $route);
            if (count($routeList) > 0) {
                $this->controllerName = self::toUpperCamelCase(array_shift($routeList));
            } else {
                $this->controllerName = DEFAULT_CONTROLLER;
            }
            //error_log("\ncname" . print_r($this->controllerName, true), 3, 'my_errors.txt');
            //error_log("\nrouteList" . print_r($routeList, true), 3, 'my_errors.txt');
            if (count($routeList) > 0) {
                $this->actionName = self::toLowerCamelCase(array_shift($routeList));
            } else {
                $this->actionName = DEFAULT_ACTION;
            }
            $this->urlParameters = $routeList;
        } else {
            $this->controllerName = DEFAULT_CONTROLLER;
            $this->actionName = DEFAULT_ACTION;
        }
    }

    private function checkAuth(Application $app, Database $db, \DBMappers\EmpItem $empMapper)
    {
        //error_log("\nauthorized" . print_r($app->isAuthorized(), true), 3, 'my_errors.txt');
        //error_log("\nsession" . print_r($registry->get(REG_SESSION), true), 3, 'my_errors.txt');
        if (!$app->isAuthorized() && $this->controllerName != LOGIN_CONTROLLER) {
            $this->controllerName = DEFAULT_CONTROLLER;
            $this->actionName = DEFAULT_ACTION;
        } else {
            if ($this->controllerName != LOGIN_CONTROLLER) {
                $empItem = $empMapper->getById($app->getEmpId(), $db);
                if (is_object($empItem)) {
                    $app->setAuthorized($empItem->getId(), $empItem->isAdmin(), $empItem->getFirstDay(), $empItem->getHourMode());
                } else {
                    $app->reopenSession();
                    $this->controllerName = DEFAULT_CONTROLLER;
                    $this->actionName = DEFAULT_ACTION;
                }
            }
        }
    }

    private function needSetPassword(Application $app, Database $db, \DBMappers\EmpItem $empMapper)
    {
        if ($app->isAuthorized()) {
            $empItem = $empMapper->getById($app->getEmpId(), $db);
            if ($empItem->isPasswordEqual(null) && $this->controllerName != EMPLOYEE_CONTROLLER  && $this->controllerName != LOGIN_CONTROLLER) {
                $app->setStateRedirect(EMPLOYEE_URL . '/edit/' . $empItem->getId());
                return true;
            }
        }
        return false;
    }


    public function start(Http $http, Application $app, Database $db, \DBMappers\EmpItem $empMapper)
    {
        $this->parsePath($http);
        $this->checkAuth($app, $db, $empMapper);
        if ($this->needSetPassword($app, $db, $empMapper)) {
            return;
        }
        $controllerClassName = CONTROLLER_NAMESPACE . "\\" . $this->controllerName;
        try {
            if (!class_exists($controllerClassName)) {
                $controllerClassName = CONTROLLER_NAMESPACE . "\\" . DEFAULT_CONTROLLER;
            }
        } catch(\Exception $e) {
            $controllerClassName = CONTROLLER_NAMESPACE . "\\" . DEFAULT_CONTROLLER;
        }
        $classInstance = new $controllerClassName();
        if (!method_exists($classInstance, $this->actionName)) {
            $this->actionName = DEFAULT_ACTION;
        }
        //error_log("\nmethod" . print_r($this->actionName, true), 3, 'my_errors.txt');
        $di_prefix = \Utility\DependencyInjectionStorage::getInstance()->getPrefix();
        call_user_func(array($classInstance, $di_prefix . $this->actionName), $this->urlParameters, $http);
    }
}