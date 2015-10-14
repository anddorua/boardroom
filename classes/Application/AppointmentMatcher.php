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
    private function makeSameDateTime(\DateTime $src)
    {
        return (new \DateTime())->setTimestamp($src->getTimestamp());
    }

    private function makeDatesChain(\DateTime $start_date, $recurring_period, $duration)
    {
        $event_dates = array($start_date);
        $step = null;
        switch ($recurring_period) {
            case 1: // weekly
                $step = new \DateInterval('P7D');
                break;
            case 2: // bi-weekly
                $step = new \DateInterval('P14D');
                break;
            case 3: // bi-weekly
                $step = new \DateInterval('P1M');
                break;
        }
        $duration_to_count = min($duration, 4);
        $event_date = $this->makeSameDateTime($event_dates[0]);
        for ($i = 1; $i <= $duration_to_count; $i++) {
            $event_date->add($step);
            $event_dates[] = $this->makeSameDateTime($event_date);
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
    public function makeChain(array $bookingData, $creatorId, $roomId)
    {
        $result = new AppointmentChain();
        if ($bookingData['recurring']) {
            $event_dates = $this->makeDatesChain($bookingData['start-date'], $bookingData['recurring-period'], $bookingData['duration']);
        } else {
            $event_dates = array($bookingData['start-date']);
        }
        $event_duration = $bookingData['end-date']->getTimestamp() - $bookingData['start-date']->getTimestamp();
        foreach($event_dates as $event_date) {
            $appItem = new \Application\AppointmentItem(array(
                'emp_id' => $bookingData['employee'],
                'notes' => $bookingData['notes'],
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