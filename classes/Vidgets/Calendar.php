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
    private function getAppointmentCalendar(\DateTime $period, \Application\RoomItem $room, \Core\Database $db)
    {
        $result = array();
        $last_day = \Utility\DateHelper::GetLastDayInMonth($period);
        $appMapper = new \DBMappers\AppointmentItem();
        for ($i = 1; $i <= $last_day; $i++) {
            $query_date = \Utility\DateHelper::DateOfDay($period, $i);
            $result[$i] = $appMapper->getDayAppointments($room->getId(), $query_date, $db);
        }
        return $result;
    }

    public function render(array $appData, $templateName, \Core\Registry $registry)
    {
        $app = $registry->get(REG_APP);
        $schedule = $this->getAppointmentCalendar($app->getCurrentPeriod(), $appData['browse_room_item'], $registry->get(REG_DB));
        return (new \Utility\Template())->parse($templateName, array(
            'browse_calendar' => $schedule,
            'browse_first_day' => $app->getFirstDay(),
            'browse_hour_mode' => $app->getHourMode(),
            'browse_period' => $app->getCurrentPeriod(),
            'site_root' => $registry->get(REG_SITE_ROOT)
        ));
    }
}