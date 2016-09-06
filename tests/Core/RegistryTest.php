<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 18:15
 */
class RegistryTest extends PHPUnit_Framework_TestCase
{
    public function testSettersGetters()
    {
        $r = new \Core\Registry();
        $key1 = 'key1';
        $key2 = 'key2';
        $data1 = 'data1';
        $data2 = 'data2';
        $this->assertNull($r->get($key1));
        $this->assertNull($r->get($key2));
        $r->set($key1, $data1);
        $r->set($key2, $data2);
        $this->assertEquals($data1, $r->get($key1));
        $this->assertEquals($data2, $r->get($key2));
    }

    /**
     * @expectedException Exception
     * @throws Exception
     */
    public function testDoubleSet()
    {
        $r = new \Core\Registry();
        $key1 = 'key1';
        $data1 = 'data1';
        $data2 = 'data2';
        $r->set($key1, $data1);
        $r->set($key1, $data2);
    }
}
