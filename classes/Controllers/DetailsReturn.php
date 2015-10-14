<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


use Application\EmpItem;

class DetailsReturn extends BaseController
{
    public function act(\Core\Registry $registry, $urlParameters)
    {
        $registry->get(REG_APP)->setStateDetailsReturn(array());
    }
}