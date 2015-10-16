<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 16.10.15
 * Time: 18:06
 */

namespace Helpers;


class TablesClearScript implements \Iterator
{
    use \Helpers\StaticFieldIterator;

    protected static $script = array(
        'delete from appointments',
        'delete from employees',
        'delete from rooms',
    );

}