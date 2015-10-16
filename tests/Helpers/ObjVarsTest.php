<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 16.10.15
 * Time: 16:55
 */

namespace Helpers;


class ObjVarsTest implements \Iterator
{
    use \Helpers\StaticFieldIterator;
    protected static $dummy = array('a', 'b');
}