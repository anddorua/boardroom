<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 17.10.15
 * Time: 22:55
 */

namespace Utility;

/**
 * Class DependencyInjection.
 * Injects in public function call trailing type hinted parameters.
 * Usage:
 * class definition:
 * class Foo {
 *     use DependencyInjection;
 *     public function foo($a, OtherClass $b){
 *         // do some things
 *     }
 * }
 *
 * here we calling it:
 *
 * DependencyInjectionStorage::getInstance()->addInstance(new OtherClass());
 * $foo = new Foo();
 * $foo->DI_foo('only first parameter');
 * ... and in fact foo('only first parameter', <OtherClass instance>) will be called.
 *
 * restrictions:
 * 1) calls only public methods,
 * 2) injects only type hinted parameters (must read 'objects')
 * @package Utility
 */
trait DependencyInjection
{
    private function DI_PRIVATE_make_trace($msg_caption, array $trace)
    {
        $trace = array_reverse($trace);
        array_pop($trace);
        $items = array();
        foreach($trace as $item) {
            $items[] = (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()';
        }
        return $msg_caption . ":\n" . join("\n", $items);
    }

    public function __call($name, $arguments)
    {
        $prefix = DependencyInjectionStorage::getInstance()->getPrefix();
        if (substr($name, 0, strlen($prefix)) == $prefix) {
            $originalMethodName = substr($name, strlen($prefix));
            $r = new \ReflectionObject($this);
            $methods = $r->getMethods(\ReflectionMethod::IS_PUBLIC);
            for ($i = 0; $i < count($methods); $i++) {
                if ($methods[$i]->name == $originalMethodName) {
                    break;
                }
            }
            if ($i < count($methods)) {
                $pars = $methods[$i]->getParameters();
                $call_parameters = $arguments;
                for ($i = count($arguments); $i < count($pars); $i++) {
                    if ($par_class = $pars[$i]->getClass()) {
                        if (is_null($call_parameters[$i] = DependencyInjectionStorage::getInstance()->getRegisteredInstance($par_class->name))) {
                            throw new EDINoInstanceInjected('No registered class ' . $par_class->name .' in call to ' . $name);
                        }
                    } else {
                        throw new EDINoTypeHint('Unable to provide ' . $i .'th parameter in call to ' . $name . ', it`s type hint undefined');
                    }
                }
                return call_user_func_array(array($this, $originalMethodName), $call_parameters);
            } else {
                throw new EDINoOriginalMethod('Can`t find original method ' . $originalMethodName .' in call to ' . $name);
            }
        } else {
            throw new \Exception('Call to undefined method ' . $name);
        }
    }

}