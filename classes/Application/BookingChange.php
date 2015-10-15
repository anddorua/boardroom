<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 16:30
 */

namespace Application;


class BookingChange
{
    private $start = null;
    private $end = null;
    private $notes = null;
    private $empId = null;
    private $applyChain = null;
    private $errorMessage = '';

    /**
     * BookingChange constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return null
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return null
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return null
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return null
     */
    public function getEmpId()
    {
        return $this->empId;
    }

    /**
     * @param null $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @param null $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @param null $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @param null $empId
     */
    public function setEmpId($empId)
    {
        $this->empId = $empId;
    }

    /**
     * @return boolean
     */
    public function isApplyChain()
    {
        return $this->applyChain == 1;
    }

    /**
     * @param null $applyChain
     */
    public function setApplyChain($applyChain)
    {
        $this->applyChain = $applyChain;
    }

    public function isTimeValid()
    {
        $t_start = date_parse($this->start);
        if ($t_start['error_count'] > 0) {
            $this->errorMessage = implode(',', $t_start['errors']);
            return false;
        }
        $t_end = date_parse($this->end);
        if ($t_end['error_count'] > 0) {
            $this->errorMessage = implode(',', $t_end['errors']);
            return false;
        }
        $t_start_min = $t_start['hour'] * 60 + $t_start['minute'];
        $t_end_min = $t_end['hour'] * 60 + $t_end['minute'];
        if ($t_start_min < $t_end_min || ($t_end['hour'] == 0 && $t_end['minute'] == 0)) {
            $this->errorMessage = '';
            return true;
        } else {
            $this->errorMessage = 'start time should be less then end time';
            return false;
        }
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }


}