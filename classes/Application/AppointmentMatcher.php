<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 13.10.15
 * Time: 17:50
 */

namespace Application;


class AppointmentMatcher
{
    private function makeDatesChain(\DateTime $start_date, $recurring_period, $duration)
    {
        $event_dates = array($start_date);
        $step = null;
        $duration_to_count = $duration;
        switch ($recurring_period) {
            case BookingOrder::RECURRING_WEEKLY:
                $step = new \DateInterval('P7D');
                break;
            case BookingOrder::RECURRING_BI_WEEKLY:
                $step = new \DateInterval('P14D');
                $duration_to_count = $duration / 2;
                break;
            case BookingOrder::RECURRING_MONTHLY:
                $step = new \DateInterval('P1M');
                break;
        }
        $event_date = clone $event_dates[0];
        for ($i = 1; $i <= $duration_to_count && $recurring_period != BookingOrder::NOT_RECURRING; $i++) {
            $event_date->add($step);
            $event_dates[] = clone $event_date;
        }
        return $event_dates;
    }

    /**
     * Returns new Appointments chain according to bookingData, user entered
     * @param array $bookingData
     * @param $creatorId
     * @param $roomId
     * @return AppointmentChain
     */
    public function makeChain(\Application\BookingOrder $bookingOrder, $creatorId, $roomId)
    {
        $result = new AppointmentChain();
        if ($bookingOrder->isRecurring()) {
            $event_dates = $this->makeDatesChain($bookingOrder->getStartTime(), $bookingOrder->getRecurring(), $bookingOrder->getDuration());
        } else {
            $event_dates = array($bookingOrder->getStartTime());
        }
        $event_duration = $bookingOrder->getEndTime()->getTimestamp() - $bookingOrder->getStartTime()->getTimestamp();
        foreach($event_dates as $event_date) {
            $appItem = new \Application\AppointmentItem(array(
                'emp_id' => $bookingOrder->getEmpId(),
                'notes' => $bookingOrder->getNotes(),
                'creator_id' => $creatorId,
                'room_id' => $roomId,
            ));
            $appItem->setTimeStart($event_date);
            $appItem->setTimeEnd((new \DateTime())->setTimestamp($event_date->getTimestamp() + $event_duration));
            $result->add($appItem);
        }
        return $result;
    }

    public function getCrossingAppointments(\Application\AppointmentChain $chain, \DBMappers\AppointmentItem $mapper, \Core\Database $db)
    {
        $result = array();
        foreach($chain as $appointment) {
            $day_apps = $mapper->getDayAppointments($appointment->getRoomId(), $appointment->getTimeStart(), $db);
            foreach($day_apps as $match) {
                if (!is_null($appointment->getChain()) && $appointment->getChain() == $match->getChain()) {
                    continue; // пропускаем, если из той же цепочки
                }
                if ($appointment->isCrossing($match->getTimeStart(), $match->getTimeEnd())) {
                    $result[] = $match;
                }
            }
        }
        return $result;
    }
}