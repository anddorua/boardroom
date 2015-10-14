<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 11.10.15
 * Time: 1:01
 */

namespace Core;


class Registry
{
    public function __construct()
    {
        $this->storage = array();;
    }

    function set($key, $var) {
        if (isset($this->storage[$key]) == true) {
            throw new \Exception('Unable to set var `' . $key . '`. Already set.');
        }
        $this->storage[$key] = $var;
        return true;
    }

    // получение данных
    function get($key) {
        if (isset($this->storage[$key]) == false) {
            return null;
        }
        return $this->storage[$key];
    }

    // удаление данных
    function remove($key) {
        unset($this->storage[$key]);
    }
}