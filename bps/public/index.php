<?php
// define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// define application url
defined('APPLICATION_URL')
    || define('APPLICATION_URL', (getenv('APPLICATION_URL') ? getenv('APPLICATION_URL') : ''));

// define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// ensure ../library is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
//    get_include_path()
)));

// using autoloader
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// create application, bootstrap and run
require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/application.ini'
);
$application->bootstrap()
            ->run();
