<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 13.10.15
 * Time: 19:07
 */

namespace Application;


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

    public function setChainId($newId)
    {
        foreach($this as $app) {
            $app->setChain($newId);
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

    public function applyChange(array $bookingData)
    {
        $this->rewind();
        foreach($this as $member) {
            $member->applyChange($bookingData);
        }
    }

    public function applyChangeToMember($empId, array $bookingData)
    {
        $this->rewind();
        foreach($this as $member) {
            if ($member->getId() == $empId) {
                $member->applyChange($bookingData);
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