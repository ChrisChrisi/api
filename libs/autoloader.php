<?php

spl_autoload_register(function ($class) {
    $class = strtolower($class);
    $file = MD_PATH . $class . '_model.php';
    $valid = file_exists($file);
    if ($valid === false) {
        $file = LIBS_PATH . $class . '_class.php';
        $valid = file_exists($file);
    }
    if ($valid === false) {
        $file = CN_PATH . $class . '_controller.php';
        $valid = file_exists($file);
    }
    if ($valid !== false) {
        require_once $file;
    } else {
       return -1;
    }
    return true;
});