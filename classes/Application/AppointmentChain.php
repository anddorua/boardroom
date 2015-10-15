<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 13.10.15
 * Time: 19:07
 */

namespace Application;


use Core\Application;

class AppointmentChain implements \Iterator
{
    private $members = array();
    private $position;
    private $timeFilter = null;

    /**
     * AppointmentChain constructor.
     */
    public function __construct()
    {
        $this->position = 0;
    }

    public function add(\Application\AppointmentItem $item)
    {
        $this->members[] = $item;
    }

    /**
     * отсекает все события, которые закончились на момент фильтра
     * @param AppointmentItem $member
     * @return bool
     */
    public function isMeetFilter(\Application\AppointmentItem $member)
    {
        if (is_null($this->timeFilter)) {
            return true;
        } else {
            $end = $member->getTimeEnd()->getTimestamp();
            $test = $this->timeFilter->getTimestamp();
            return $end > $test;
        }
    }

    public function current()
    {
        return $this->members[$this->position];
    }

    public function next()
    {
        ++$this->position;
        while ($this->valid() && !$this->isMeetFilter($this->members[$this->position])) {
            ++$this->position;
        }
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->members[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
        while ($this->valid() && !$this->isMeetFilter($this->members[$this->position])) {
            ++$this->position;
        }
    }

    /**
     * устанавливает новый chainId для всех членов цепочки. Не подвержена фильтрации.
     * @param $newId
     */
    public function setChainId($newId)
    {
        for ($i = 0; $i < count($this->members); $i++) {
            $this->members[$i]->setChain($newId);
        }
    }

    public function count()
    {
        if ($this->timeFilter) {
            $filteredMembers = array_filter($this->members, function(\Application\AppointmentItem $member) {
                return $this->isMeetFilter($member);
            });
            return count($filteredMembers);
        } else {
            return count($this->members);
        }
    }

    public function applyFilter(\DateTime $time)
    {
        $this->timeFilter = $time;
    }
    public function dropFilter()
    {
        $this->timeFilter = null;
    }

    public function applyChange(\Application\BookingChange $bookingData)
    {
        $this->rewind();
        foreach($this as $member) {
            $member->setNewTime($bookingData->getStart(), $bookingData->getEnd());
            $member->setNotes($bookingData->getNotes());
            $member->setEmpId($bookingData->getEmpId());
        }
    }

    public function applyChangeToMember($empId, \Application\BookingChange $bookingData)
    {
        $this->rewind();
        foreach($this as $member) {
            if ($member->getId() == $empId) {
                $member->setNewTime($bookingData->getStart(), $bookingData->getEnd());
                $member->setNotes($bookingData->getNotes());
                $member->setEmpId($bookingData->getEmpId());
                break;
            }
        }
    }

    public function get($empId)
    {
        foreach($this->members as $member) {
            if ($member->getId() == $empId) {
                return $member;
            }
        }
        return null;
    }
}