<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 15.10.15
 * Time: 20:45
 */
class BaseControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Exception
     */
    public function testAct()
    {
        $c = new \Controllers\BaseController();
        $c->act(new \Core\Registry(), array(),);
    }
}
