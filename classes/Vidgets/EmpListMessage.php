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
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName, \Core\Application $app)
    {
        $message = $app->getMessage();
        $app->dropMessage();
        return (new \Utility\Template())->parse($templateName, array('message' => $message));
    }
}