<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 18.10.15
 * Time: 13:28
 */

namespace Utility;


class DependencyInjectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Exception
     */
    public function testCallToUndefinedFunction()
    {
        $obj = new \Helpers\DummyMagic();
        \Utility\DependencyInjectionStorage::getInstance()->setPrefix('DI_');
        $obj->undefinedMethod();
    }
    /**
     * @expectedException \Utility\EDINoOriginalMethod
     */
    public function testNoOriginalMethod()
    {
        $obj = new \Helpers\DummyMagic();
        \Utility\DependencyInjectionStorage::getInstance()->setPrefix('DI_');
        $obj->DI_undefinedMethod();
    }
    /**
     * @expectedException \Utility\EDINoTypeHint
     */
    public function testNoTypeHint()
    {
        $obj = new \Helpers\DummyMagic();
        \Utility\DependencyInjectionStorage::getInstance()->setPrefix('DI_');
        $obj->DI_yarr(1);
    }
    /**
     * @expectedException \Utility\EDINoInstanceInjected
     */
    public function testNoInstance()
    {
        $obj = new \Helpers\DummyMagic();
        \Utility\DependencyInjectionStorage::getInstance()->setPrefix('DI_');
        \Utility\DependencyInjectionStorage::getInstance()->addInstance($obj);
        \Utility\DependencyInjectionStorage::getInstance()->addInstance(new \Helpers\ObjVarsTest());
        $obj->DI_foo(array(1));
    }
    public function testSuccessfulCall()
    {
        $obj = new \Helpers\DummyMagic();
        \Utility\DependencyInjectionStorage::getInstance()->setPrefix('DI_');
        \Utility\DependencyInjectionStorage::getInstance()->addInstance($obj);
        $dateObj = (new \DateTime())->setDate(2015,1,1)->setTime(10,01);
        \Utility\DependencyInjectionStorage::getInstance()->addInstance($dateObj);
        \Utility\DependencyInjectionStorage::getInstance()->addInstance(new \Helpers\ObjVarsTest());
        $this->assertEquals($dateObj, $obj->DI_foo(array(1)));
    }
}
