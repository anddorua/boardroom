<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


class EmployeeList extends BaseController
{
    use \Utility\DependencyInjection;
    public function act($urlParameters, \Core\Http $http, \Core\Application $app)
    {
        $app->setStateEmployeeList(array());
    }
}