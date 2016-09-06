<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 16.10.15
 * Time: 9:18
 */

namespace Core;


class Http
{
    public function post()
    {
        return $_POST;
    }
    public function get()
    {
        return $_GET;
    }
    public function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    /**
     * @param $url
     * @param $siteRoot string
     * @param int $statusCode
     */
    public function redirect($url, $siteRoot = '', $statusCode = 303)
    {
        $newLocation = rtrim($siteRoot, '/') . '/' . ltrim($url, '/');
        //error_log("\nredirect to" . print_r($newLocation, true), 3, 'my_errors.txt');
        header('Location: ' . $newLocation, true, $statusCode);
        die();
    }

    public function setResponseBody($content)
    {
        echo $content;
    }
}