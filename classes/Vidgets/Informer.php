<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class Informer implements BaseVidget
{
    use \Utility\DependencyInjection;
    public function render(array $appData, $templateName)
    {
        return (new \Utility\Template())->parse($templateName, array(
            'browse_room_name' => $appData['browse_room_item']->getRoomName()
        ));
    }
}