<?php
spl_autoload_register(function ($class_name) {
    if(preg_match('/\\\\/', $class_name)) {
        $class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
    }
    if(file_exists("vendor".DIRECTORY_SEPARATOR."samoscon".DIRECTORY_SEPARATOR."controller-framework".DIRECTORY_SEPARATOR."{$class_name}.php")) {
        require_once "vendor".DIRECTORY_SEPARATOR."samoscon".DIRECTORY_SEPARATOR."controller-framework".DIRECTORY_SEPARATOR."{$class_name}.php";
    }
    if(file_exists("MVCFramework".DIRECTORY_SEPARATOR."{$class_name}.php")) {
        require_once "MVCFramework".DIRECTORY_SEPARATOR."{$class_name}.php";
    }
});
