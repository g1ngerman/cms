<?php



function loadClass($class) {

    // SET CONFIGURATION PARAMETERS
    include_once"sys/core/class.Config.php";
    Config::defineConstants();

    // SEARCH FOR NEEDED CLASS
    switch($class)
    {

        case file_exists('sys/core/class.' . $class . '.php') : include_once 'sys/core/class.' . $class . '.php';
        break;

        case file_exists('sys/core/interfaces/class.' . $class . '.php') : include_once 'sys/core/interfaces/class.' . $class . '.php';
        break;

        case file_exists('sys/ext/class.' . $class . '.php') : include_once 'sys/ext/class.' . $class . '.php';
        break;

// GET CLASS FROM SUBDIRECTORY (LIKE 'SITE.COM/ADMIN' ETC)

        case file_exists('../sys/core/class.' . $class . '.php') : include_once '../sys/core/class.' . $class . '.php';
        break;

        case file_exists('../sys/core/interfaces/class.' . $class . '.php') : include_once '../sys/core/interfaces/class.' . $class . '.php';
        break;

        case file_exists('../sys/ext/class.' . $class . '.php') : include_once '../sys/ext/class.' . $class . '.php';
        break;

// GET CLASS FOR MODULES

        case file_exists(MOD_PATH.'/mod_'.$class.'/class.' . $class . '.php') : include_once MOD_PATH.'/mod_'.$class.'/class.' . $class . '.php';
        break;


        default: exit("Front-end Loader:  _CLASS '<strong>$class</strong>' NOT FOUND_");
    }


 }


spl_autoload_register('loadClass');