<?php

require_once dirname(__FILE__) . '/../libs/config.php';
require_once LIBS_PATH . 'autoloader.php';

if (DEV_MODE === true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

$_route = isset($_REQUEST['route']) ? trim(filter_var($_REQUEST['route'], FILTER_SANITIZE_URL), '/') : '';

$router = new Router($_route);
$router->dispatch();