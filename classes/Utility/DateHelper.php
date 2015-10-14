<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 12.10.15
 * Time: 17:16
 */

namespace Utility;


class DateHelper
{
    public static function GetLastDayInMonth(\DateTime $date)
    {
        return self::GetLastDateInMonth($date)->format('j');
    }
    public static function GetLastDateInMonth(\DateTime $date)
    {
        $next_per = (new \DateTime())->setTimeStamp($date->getTimestamp());
        $next_per->add(new \DateInterval('P1M'));
        $next_per->sub(new \DateInterval('P1D'));
        return $next_per;
    }
    public static function GetNextDay(\DateTime $date)
    {
        $next_per = (new \DateTime())->setTimeStamp($date->getTimestamp());
        $next_per->add(new \DateInterval('P1D'));
        return $next_per;
    }
    public static function DateOfDay(\DateTime $period, $day_number)
    {
        return (new \DateTime())->setDate($period->format('Y'), $period->format('n'), $day_number);
    }
    public static function GetFirstDateInMonth(\DateTime $period)
    {
        return (new \DateTime())->setDate($period->format('Y'), $period->format('n'), 1);
    }
    public static function IsDateInSamePeriod(\DateTime $d1, \DateTime $d2)
    {
        return $d1->format('Y') == $d2->format('Y') && $d1->format('m') == $d2->format('m');
    }
    public static function FormatTimeAccordingRule(\DateTime $time, $hour_mode)
    {
        return $time->format($hour_mode == \Application\EmpItem::MODE_DAY_12 ? 'g:ia' : 'G:i');
    }
}