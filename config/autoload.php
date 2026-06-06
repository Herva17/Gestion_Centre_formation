<?php
/**
 * Autoloader pour les classes
 */

function autoload($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('autoload');
