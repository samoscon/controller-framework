<?php 
/*
 * Make sure to disable the display of errors in production code! You can enable in the test mode
 */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include './vendor/autoload.php';
include './autoload.php'; //Autoload in the root of your to facilitate the link between your php files and your classes

controllerframework\controllers\Controller::run();
