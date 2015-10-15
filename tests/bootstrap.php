<?php
/**
 * Created by PhpStorm.
 * User: Такси
 * Date: 14.10.15
 * Time: 23:52
 */
//include_once('AutoLoader.php');
// Register the directory to your include files
//AutoLoader::registerDirectory('classes/');
include_once('SplClassLoader.php');
$classLoader = new SplClassLoader('classes');
$classLoader->register();