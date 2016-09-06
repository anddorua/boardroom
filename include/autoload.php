<?php
set_include_path(get_include_path() . PATH_SEPARATOR . CLASS_ROOT);
spl_autoload_extensions('.php');
spl_autoload_register();
spl_autoload_register(function($class){
    $fileToFind = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    include $fileToFind . '.php';
});
