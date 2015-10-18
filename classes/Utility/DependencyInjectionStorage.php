<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 17.10.15
 * Time: 23:29
 */

namespace Utility;


class DependencyInjectionStorage
{
    private static $instance = null;

    private $classInstances = array();
    private $prefix = 'DI_';

    private function __construct()
    {
    }
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($value)
    {
        $this->prefix = $value;
    }
    public function addInstance($instance)
    {
        for ($i = 0; $i < count($this->classInstances); $i++) {
            $registeredInstanceClassName = get_class($this->classInstances[$i]);
            if ($instance instanceof $registeredInstanceClassName) {
                $this->classInstances[$i] = $instance;
                return;
            }
        }
        $this->classInstances[] = $instance;
    }

    public function getRegisteredInstance($className)
    {
        for ($i = 0; $i < count($this->classInstances); $i++) {
            if ($this->classInstances[$i] instanceof $className) {
                return $this->classInstances[$i];
            }
        }
        return null;
    }

    public function removeInstance($className)
    {
        for ($i = 0; $i < count($this->classInstances); $i++) {
            if ($this->classInstances[$i] instanceof $className || $className instanceof $this->classInstances[$i]) {
                array_splice($this->classInstances, $i, 1);
                return true;
            }
        }
        return false;
    }
}