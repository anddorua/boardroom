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
    private function setWrongLoginState(\Core\Registry $registry)
    {
        $registry->get(REG_APP)->setStateLogin(array(
            'login_error_message' => 'wrong login/password',
            'login_field_login' => $_POST['login']
        ));
    }

    public function act(\Core\Registry $registry, $urlParameters)
    {
        $app = $registry->get(REG_APP);
        $app->reopenSession();
        if (isset($_POST['login'])) {
            //error_log("\nPOST:" . print_r($_POST, true), 3, 'my_errors.txt');
            $empItem = (new \DBMappers\EmpItem())->getByLogin($_POST['login'], $registry->get(REG_DB));
            if (!$empItem) {
                //error_log("\nemp not found", 3, 'my_errors.txt');
                $this->setWrongLoginState($registry);
                return;
            }
            if (!$empItem->isPasswordEqual($_POST['password'])) {
                //error_log("\npassword not equal", 3, 'my_errors.txt');
                $this->setWrongLoginState($registry);
                return;
            }
            $app->setAuthorized($empItem->getId(), $empItem->isAdmin(), $empItem->getFirstDay(), $empItem->getHourMode());
            $app->setStateRedirect(BROWSE_URL);
        } else {
            $app->setStateLogin(array());
        }
        //$app->setStateLogin(array('login_error_message' => 'some error message'));

    }
}