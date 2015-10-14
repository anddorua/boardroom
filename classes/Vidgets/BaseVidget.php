<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 11.10.15
 * Time: 11:47
 */

namespace Vidgets;


interface BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry);
}