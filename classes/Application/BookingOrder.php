<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 16.10.15
 * Time: 21:44
 */

namespace Application;

/**
 * Class BookingOrder
 * @package Application
 */
class BookingOrder
{
    const NOT_RECURRING = 0;
    const RECURRING_WEEKLY = 1;
    const RECURRING_BI_WEEKLY = 2;
    const RECURRING_MONTHLY = 3;

    private $empId;
    private $startTime;
    private $endTime;
    private $notes;
    private $recurring;
    private $duration;
    private $errorMessage = null;

    /**
     * BookingOrder constructor.
     */
    public function __construct()
    {
        $this->startTime = new \DateTime();
        $this->endTime = new \DateTime();
    }

    /**
     * @param mixed $empId
     */
    public function setEmpId($empId)
    {
        $this->empId = $empId;
    }

    /**
     * @return mixed
     */
    public function getEmpId()
    {
        return $this->empId;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @param mixed $recurring
     */
    public function setRecurring($recurring, $duration = null)
    {
        $this->recurring = $recurring;
        if ($recurring == self::RECURRING_WEEKLY) {
            $this->duration = min(4, $duration);
        } else if ($recurring == self::RECURRING_BI_WEEKLY) {
            $this->duration = floor(min(4, $duration) / 2) * 2;
        } else {
            $this->duration = $duration;
        }
    }

    public function isRecurring()
    {
        return $this->recurring != self::NOT_RECURRING;
    }

    /**
     * @return mixed
     */
    public function getRecurring()
    {
        return $this->recurring;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    private function correctZeroEndDate()
    {
        if ($this->endTime->format('G') == 0 && $this->endTime->format('i') == 0) {
            $this->endTime->add(new \DateInterval('P1D'));
        }
    }
    public function setDate($year, $month, $day)
    {
        $this->startTime->setDate($year, $month, $day);
        $this->endTime->setDate($year, $month, $day);
        $this->correctZeroEndDate();
    }

    public function setStartTime24($hour, $minute)
    {
        $this->startTime->setTime($hour, $minute);
    }

    public function setEndTime24($hour, $minute)
    {
        $this->endTime->setTimestamp($this->startTime->getTimestamp());
        $this->endTime->setTime($hour, $minute);
        $this->correctZeroEndDate();
    }

    private function hoursMerToFull($hours, $meridiem)
    {
        if ($meridiem == 'am') {
            return $hours;
        } else {
            if ($hours < 12) {
                return $hours + 12;
            } else {
                return 0;
            }
        }
    }

    public function setStartTime12($hour, $minute, $meridiem)
    {
        $this->setStartTime24($this->hoursMerToFull($hour, strtolower($meridiem)), $minute);
    }

    public function setEndTime12($hour, $minute, $meridiem)
    {
        $this->setEndTime24($this->hoursMerToFull($hour, strtolower($meridiem)), $minute);
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    public function isTimeValid()
    {
        if ($this->startTime->getTimestamp() - $this->endTime->getTimestamp() >= 0) {
            $this->errorMessage = 'Start time should be less than end time';
            return false;
        } else {
            $this->errorMessage = null;
            return true;
        }
    }

    public function isPeriodBeforeTime(\DateTime $timeToTest)
    {
        return $this->endTime->getTimestamp() <= $timeToTest->getTimestamp();
    }

    /**
     * @return null
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }


}