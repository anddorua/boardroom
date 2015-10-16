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
    public function act(\Core\Registry $registry, $urlParameters, \Core\Http $http)
    {
        $registry->get(REG_APP)->setStateEmployeeList(array());
    }
}