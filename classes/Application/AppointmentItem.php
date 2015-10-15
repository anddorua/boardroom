<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 10.10.15
 * Time: 23:24
 */

namespace Application;


class AppointmentItem extends ArrayCapable
{
    protected $id = null;
    protected $emp_id;
    protected $time_start;
    protected $time_end;
    protected $notes;
    protected $creator_id;
    protected $chain = null;
    protected $room_id;
    protected $submitted = null;

    public function __construct($param)
    {
        $this->fromArray($param);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTimeStart()
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->time_start);
    }

    /**
     * @param mixed $time_start
     */
    public function setTimeStart(\DateTime $time_start)
    {
        $this->time_start = $time_start->format('Y-m-d H:i:s');
    }

    /**
     * @param mixed $time_end
     */
    public function setTimeEnd(\DateTime $time_end)
    {
        $this->time_end = $time_end->format('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getTimeEnd()
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->time_end);
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return null
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * @return mixed
     */
    public function getRoomId()
    {
        return $this->room_id;
    }

    /**
     * @return mixed
     */
    public function getEmpId()
    {
        return $this->emp_id;
    }

    /**
     * @return null
     */
    public function getSubmitted()
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $this->submitted);
    }

    /**
     * @param null $chain
     */
    public function setChain($chain)
    {
        $this->chain = $chain;
    }


    /**
     * Shows if object's period crossing with test period
     * right side not includes to period
     * @param \DateTime $testTimeStart
     * @param \DateTime $testTimeEnd
     * @return bool
     */
    public function isCrossing(\DateTime $testTimeStart, \DateTime $testTimeEnd)
    {
        $myStartStamp = $this->getTimeStart()->getTimestamp();
        $myEndStamp = $this->getTimeEnd()->getTimestamp();
        $testStartStamp = $testTimeStart->getTimestamp();
        $testEndStamp = $testTimeEnd->getTimestamp();
        return $testStartStamp < $myEndStamp && $myStartStamp < $testEndStamp;
    }

/*    public function applyChange($bookingData)
    {
        $t_start = date_parse($bookingData['start']);
        $new_date = (new \DateTime())->setTimestamp($this->getTimeStart()->getTimestamp());
        $new_date->setTime($t_start['hour'], $t_start['minute']);
        $this->setTimeStart($new_date);
        $t_end = date_parse($bookingData['end']);
        $new_date->setTime($t_end['hour'], $t_end['minute']);
        if ($this->getTimeStart()->getTimestamp() > $new_date->getTimestamp()) {
            $new_date->add(new \DateInterval('P1D'));
        }
        $this->setTimeEnd($new_date);
        $this->notes = $bookingData['notes'];
        $this->emp_id = $bookingData['employee'];
    }*/

    /**
     * sets new time, leaving date the same
     * @param $timeStart string like '07:15'
     * @param $timeEnd string like '08:15'
     */
    public function setNewTime($timeStart, $timeEnd)
    {
        $t_start = date_parse($timeStart);
        $new_date = (new \DateTime())->setTimestamp($this->getTimeStart()->getTimestamp());
        $new_date->setTime($t_start['hour'], $t_start['minute']);
        $this->setTimeStart($new_date);
        $t_end = date_parse($timeEnd);
        $new_date->setTime($t_end['hour'], $t_end['minute']);
        if ($this->getTimeStart()->getTimestamp() > $new_date->getTimestamp()) {
            $new_date->add(new \DateInterval('P1D'));
        }
        $this->setTimeEnd($new_date);
    }
    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @param mixed $emp_id
     */
    public function setEmpId($emp_id)
    {
        $this->emp_id = $emp_id;
    }

}