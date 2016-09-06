<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:25
 */

namespace Controllers;


class BaseController
{
    use \Utility\DependencyInjection;
    protected function isEmptyValues(array $toTest)
    {
        return count(array_filter($toTest, function($item){ return !empty($item); })) == 0;
    }
}