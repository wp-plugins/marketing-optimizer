<?php

class mo_autoloader
{

    public function __construct(){
        spl_autoload_register(array(
            $this,
            'mo_loader'
        ));
    }

    public function mo_loader($class_name){
        if (! class_exists($class_name) && file_exists(__DIR__ . '/class.' . $class_name . '.php')) {
            include __DIR__ . '/class.' . $class_name . '.php';
        }
        if (! class_exists($class_name) && file_exists(__DIR__ . '/api/class.' . $class_name . '.php')) {
            include __DIR__ . '/api/class.' . $class_name . '.php';
        }
    }
}

$mo_autoloader = new mo_autoloader();