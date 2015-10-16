<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 23:02
 */

namespace Controllers;


class DefaultController extends BaseController
{
    public function act(\Core\Registry $registry, $urlParameters, \Core\Http $http)
    {
        $app = $registry->get(REG_APP);
        if ($app->isAuthorized()) {
            $empItem = (new \DBMappers\EmpItem())->getById($app->getEmpId(), $registry->get(REG_DB));
            if ($empItem->isPasswordEqual(null)) {
                $app->setStateRedirect(EMPLOYEE_URL . '/edit/' . $empItem->getId());
            } else {
                $app->setStateRedirect(BROWSE_URL);
            }
        } else {
            $app->setStateRedirect(LOGIN_URL);
        }
    }

}