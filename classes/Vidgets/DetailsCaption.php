<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class DetailsCaption implements BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        return (new \Utility\Template())->parse($templateName, array(
            'details_appointment' => $appData['details_appointment'],
            'hour_mode' => $appData['hour_mode']
        ));
    }
}