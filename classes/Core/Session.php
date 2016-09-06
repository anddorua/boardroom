<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 11.10.15
 * Time: 9:58
 */

namespace Core;


class Session
{
    public function __construct()
    {
        $this->open();
    }

    public function open()
    {
        $sStatus = session_status();
        if ($sStatus == PHP_SESSION_DISABLED) {
            throw new \Exception("sessions disabled, check php server settings");
        }
        if ($sStatus == PHP_SESSION_NONE) {
            if (!session_start()) {
                throw new \Exception("session didn't started");
            }
        }
    }

    public function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public function get($key)
    {
        return $_SESSION[$key];
    }

    public function keyExists($key)
    {
        return isset($_SESSION[$key]);
    }

    public function drop($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function close()
    {
        session_destroy();
        session_commit();
    }
}