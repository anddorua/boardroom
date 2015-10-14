<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 13.10.15
 * Time: 14:31
 */

namespace Utility;


class HtmlHelper
{
    public static function HideIfEmpty($value)
    {
        echo empty($value) ? " style=\"display:none\"" : '';
    }
    public static function HideIfEmptyOrNull($arr, $key)
    {
        $defined = $arr && is_array($arr) && isset($arr[$key]) && !empty($arr[$key]);
        echo !$defined ? " style=\"display:none\"" : '';
    }
    public static function EchoIfPresent($arr, $key)
    {
        $defined = $arr && is_array($arr) && isset($arr[$key]) && !empty($arr[$key]);
        echo $defined ? $arr[$key] : '';
    }
    public static function ValueOrFalse($arr, $key)
    {
        $defined = $arr && is_array($arr) && isset($arr[$key]);
        return $defined ? $arr[$key] : false;
    }
    public static function EchoOptions(array $arr, $selectValue = null)
    {
        foreach($arr as $key => $value) {
            echo "<option value=\"" . $key . "\"" . ($selectValue && $key == $selectValue ? ' selected' : '') . ">" . $value . "</option>";
        }
    }
    public  static function MakeCrossingMessage(array $crossings, \DBMappers\EmpItem $empMapper, \Core\Database $db)
    {
        $message = 'Can\'t add appointment, it crosses existing appointments: ';
        foreach($crossings as $cross) {
            $empItem = $empMapper->getById($cross->getEmpId(), $db);
            $message .= $empItem->getName();
            $message .= ' ' . $cross->getTimeStart()->format('M-j-Y H:i');
            $message .= '-' . $cross->getTimeEnd()->format('H:i') . ';';
        }
        return $message;
    }
}