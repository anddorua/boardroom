<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 10.10.15
 * Time: 22:35
 */

namespace Application;


class EmpItem extends \Core\ArrayCapable
{
    const MODE_DAY_24 = 24;
    const MODE_DAY_12 = 12;
    const FIRST_DAY_SUNDAY = 0;
    const FIRST_DAY_MONDAY = 1;

    protected $id = null;
    protected $login;
    protected $email;
    protected $pwd_hash = null;
    protected $is_admin = 0;
    protected $hour_mode = self::MODE_DAY_24;
    protected $first_day = self::FIRST_DAY_MONDAY;
    protected $name;

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

    public function getIdFieldName()
    {
        return 'id';
    }

    public function isAdmin()
    {
        return $this->is_admin == 1;
    }

    public static function HashPwd ($src)
    {
        if (is_null($src) || $src == '') {
            return null;
        } else {
            return sha1('kjndvlkjadnvadv' . $src);
        }
    }

    public function setPwd($pwd)
    {
        $this->pwd_hash = self::HashPwd($pwd);
    }

    public function dropPwd()
    {
        $this->pwd_hash = null;
    }

    public function isPasswordEqual($passToTest)
    {
        if (is_null($this->pwd_hash) || $this->pwd_hash == '') {
            return $passToTest == '' || is_null($passToTest);
        } else {
            return $this->pwd_hash == self::HashPwd($passToTest);
        }
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getHourMode()
    {
        return $this->hour_mode;
    }

    /**
     * @return mixed
     */
    public function getFirstDay()
    {
        return $this->first_day;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


}