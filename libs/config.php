<?php

define('LIBS_PATH',  dirname(__FILE__) . '/');
define('PATH', LIBS_PATH . '../');
define('CN_PATH', PATH . 'controllers/');

if (isset($_SERVER["SERVER_NAME"]) && strpos($_SERVER["SERVER_NAME"], 'localhost')) {
    define('DEV_MODE', true);

    ## DB vars
    define('DBHOST', 'localhost');
    define('DBUSER', 'root');
    define('DBPASS', '');
    define('DBNAME', 'api');

} else {
    define('DEV_MODE', false);

    ## DB vars
    define('DBHOST', 'localhost');
    define('DBUSER', 'root');
    define('DBPASS', '');
    define('DBNAME', 'api');
}
