<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 17.10.15
 * Time: 23:22
 */

namespace Helpers;


class OtherDependent
{
    use \Utility\DependencyInjection;
    public function foo()
    {
        return true;
    }
}