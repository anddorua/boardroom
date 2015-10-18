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
    use \Utility\DependencyInjection;
    public function act($urlParameters, \Core\Http $http, \Core\Application $app, \Core\Database $db, \DBMappers\EmpItem $empItemMapper)
    {
        if ($app->isAuthorized()) {
            $empItem = $empItemMapper->getById($app->getEmpId(), $db);
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