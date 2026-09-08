<?php 
include './vendor/autoload.php';

//Upload classes within your project automatically
spl_autoload_register(function ($class_name) {
    if(preg_match('/\\\\/', $class_name)) {
        $class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
    }
    if(file_exists("MVCFramework".DIRECTORY_SEPARATOR."{$class_name}.php")) {
        require_once "MVCFramework".DIRECTORY_SEPARATOR."{$class_name}.php";
    }
});

controllerframework\controllers\Controller::run();
