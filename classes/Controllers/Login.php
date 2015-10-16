<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


use Application\EmpItem;

class Login extends BaseController
{
    private function setWrongLoginState(\Core\Application $app, $loginValue)
    {
        $app->setStateLogin(array(
            'login_error_message' => 'wrong login/password',
            'login_field_login' => $loginValue
        ));
    }

    public function act(\Core\Registry $registry, $urlParameters, \Core\Http $http)
    {
        $app = $registry->get(REG_APP);
        $app->reopenSession();
        if (isset($http->post()['login'])) {
            $loginValue = $http->post()['login'];
            //error_log("\nPOST:" . print_r($http->post(), true), 3, 'my_errors.txt');
            $empItem = (new \DBMappers\EmpItem())->getByLogin($loginValue, $registry->get(REG_DB));
            if (!$empItem) {
                $this->setWrongLoginState($app, $loginValue);
                return;
            }
            if (!$empItem->isPasswordEqual($http->post()['password'])) {
                $this->setWrongLoginState($app, $loginValue);
                return;
            }
            $app->setAuthorized($empItem->getId(), $empItem->isAdmin(), $empItem->getFirstDay(), $empItem->getHourMode());
            $app->setStateRedirect(BROWSE_URL);
        } else {
            $app->setStateLogin(array());
        }
    }
}