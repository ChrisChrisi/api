<?php

spl_autoload_register(function ($class) {
    $class = strtolower($class);
    $file = LIBS_PATH . $class . '_class.php';
    $valid = file_exists($file);
    if ($valid === false) {
        $file = CN_PATH . $class . '.php';
        $valid = file_exists($file);
    }
    if ($valid !== false) {
        require_once $file;
    } else {
       return -1;
    }
    return true;
});