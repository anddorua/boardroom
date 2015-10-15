<?php

/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 14.10.15
 * Time: 22:58
 */
class EmpItemTest extends PHPUnit_Framework_TestCase
{
    protected static $arrayToTest = array(
        'id' => 1,
        'login' => 'some_login',
        'email' => 'some_email',
        'pwd_hash' => 'some_hash',
        'is_admin' => 1,
        'hour_mode' => 12,
        'first_day' => 1,
        'name' => 'some_name'
    );

    public function testCreation()
    {
        $emp = new \Application\EmpItem(self::$arrayToTest);
        $this->assertEquals(1, $emp->getId());
        $this->assertEquals('some_email', $emp->getEmail());
        $this->assertEquals(1, $emp->getFirstDay());
        $this->assertEquals('some_login', $emp->getLogin());
        $this->assertEquals(12, $emp->getHourMode());
        $this->assertEquals('some_name', $emp->getName());
        $this->assertTrue($emp->isAdmin());
        return $emp;
    }

    /**
     * @depends testCreation
     **/
    public function testPassword(\Application\EmpItem $emp)
    {
        $this->assertFalse($emp->isPasswordEqual('right_pass'));
        $emp->setPwd('right_pass');
        $this->assertTrue($emp->isPasswordEqual('right_pass'));
        $this->assertFalse($emp->isPasswordEqual('wrong_pass'));
        $this->assertFalse($emp->isPasswordEqual(null));
        $this->assertFalse($emp->isPasswordEqual(''));
        $emp->dropPwd();
        $this->assertFalse($emp->isPasswordEqual('right_pass'));
        $this->assertTrue($emp->isPasswordEqual(null));
        $this->assertTrue($emp->isPasswordEqual(''));
        $emp->setPwd('right_pass');
        return $emp;
    }

    /**
     * @depends testPassword
     * @param \Application\EmpItem $emp
     */
    public function testArrayFunctions(\Application\EmpItem $emp)
    {
        $arr = $emp->toArray();
        $subset = array_merge(array(), self::$arrayToTest);
        unset($subset['pwd_hash']);
        $this->assertArraySubset($subset, $arr);
    }
}
