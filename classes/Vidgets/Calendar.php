<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class Calendar implements BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        $app = $registry->get(REG_APP);
        return (new \Utility\Template())->parse($templateName, array(
            'browse_calendar' => $appData['browse_calendar'],
            'browse_first_day' => $app->getFirstDay(),
            'browse_hour_mode' => $app->getHourMode(),
            'browse_period' => $app->getCurrentPeriod(),
            'site_root' => $registry->get(REG_SITE_ROOT)
        ));
    }
}