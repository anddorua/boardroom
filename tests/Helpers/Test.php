<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 16.10.15
 * Time: 16:59
 */

class Test extends \PHPUnit_Framework_TestCase
{
    public function testObjVars()
    {
        $o = new \Helpers\ObjVarsTest();
        $o->rewind();
        $this->assertEquals('a', $o->current());
        $o->next();
        $this->assertEquals('b', $o->current());
        $o->next();
        $this->assertFalse($o->valid());
    }
}
