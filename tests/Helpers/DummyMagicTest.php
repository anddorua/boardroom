<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 17.10.15
 * Time: 17:49
 */

namespace Helpers;


use Utility\DependencyInjectionStorage;

class DummyMagicTest extends \PHPUnit_Framework_TestCase
{
    public function testDumy(){
        $m = new DummyMagic();
        $m->foo(array(1), new \DateTime());
        $inject1 = new \DBMappers\AppointmentItem();
        DependencyInjectionStorage::getInstance()->addInstance($inject1);
        $inject2 = (new \DateTime())->setDate(2010,1,1);
        DependencyInjectionStorage::getInstance()->addInstance($inject2);
        $inject3 = (new \DateTime())->setDate(2013,1,1);
        DependencyInjectionStorage::getInstance()->addInstance($inject3);
        $inject4 = new \DBMappers\EmpItem();
        DependencyInjectionStorage::getInstance()->addInstance($inject4);
        DependencyInjectionStorage::getInstance()->setPrefix('DII_');
        $m->DII_foo(array(2));
        $m->DII_bar();
        $m->DII_baz();
        print_r(DependencyInjectionStorage::getInstance()->getRegisteredInstance(get_class($inject1)));
    }

}
