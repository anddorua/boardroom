<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 09.10.15
 * Time: 13:58
 */

namespace Vidgets;


class PeriodNavigator implements BaseVidget
{
    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        $app = $registry->get(REG_APP);
        $cur_per = $app->getCurrentPeriod();
        $period_now = $cur_per->format('F Y');
        $first_day = (new \DateTime())->setDate($cur_per->format('Y'), $cur_per->format('n'), 1);
        $next_per = (new \DateTime())->setTimeStamp($first_day->getTimestamp());
        $next_per->add(new \DateInterval('P1M'));
        $prev_per = (new \DateTime())->setTimeStamp($first_day->getTimestamp());
        $prev_per->sub(new \DateInterval('P1M'));

        return (new \Utility\Template())->parse($templateName, array(
            'period_navigator_now' => $period_now,
            'period_left_link' => $registry->get(REG_SITE_ROOT) . BROWSE_URL . "?year=" . $prev_per->format('Y') . "&month=" . $prev_per->format('n'),
            'period_right_link' => $registry->get(REG_SITE_ROOT) . BROWSE_URL . "?year=" . $next_per->format('Y') . "&month=" . $next_per->format('n'),
        ));
    }
}