<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class EmpListMessage implements BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        $app = $registry->get(REG_APP);
        $message = $app->getMessage();
        $app->dropMessage();
        return (new \Utility\Template())->parse($templateName, array('message' => $message));
    }
}