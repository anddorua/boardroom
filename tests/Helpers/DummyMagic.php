<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 17.10.15
 * Time: 17:45
 */

namespace Helpers;


class DummyMagic
{
    use \Utility\DependencyInjection;

    public function foo(array $a, \DateTime $b)
    {
        $c = '';
        $c .= print_r("\nfoo called: a = ", true);
        $c .= print_r($a, true);
        $c .= print_r("\nfoo called: b = ", true);
        $c .= print_r($b, true);
        //print_r($c, false);
        return $b;
    }

    public function bar(\DBMappers\EmpItem $emp)
    {
        $c = '';
        $c .= print_r("\nbar called: emp = ", true);
        $c .= print_r($emp, true);
        return $c;
    }

    public function baz(\DBMappers\AppointmentItem $app)
    {
        $c = '';
        $c .= print_r("\nbaz called: app = ", true);
        $c .= print_r($app, true);
        return $c;
    }

    public function yarr($a, $b)
    {
        $c = '';
        $c .= print_r("\nyarr called: a = ", true);
        $c .= print_r($a, true);
        $c .= print_r("\nyarr called: b = ", true);
        $c .= print_r($b, true);
        return $c;
    }
}