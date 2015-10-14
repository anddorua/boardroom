<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 13.10.15
 * Time: 12:29
 */

namespace Utility;


class Validator
{
    public static function IsFieldNotEmpty(array $formValues, $fieldName)
    {
        return isset($formValues[$fieldName]) && !empty($formValues[$fieldName]);
    }

    public static function IsDateValid($year, $month, $day)
    {
        return checkdate($month, $day, $year);
    }

}