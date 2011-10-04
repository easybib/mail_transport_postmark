<?php

/**
 * Autoloader for test suite.
 *
 * @param string $class
 * @return boolean
 */
function Post_testAutoloader($class) {
    try {
        include_once 'Zend/Mail/Transport/Abstract.php';
        include_once 'Zend/Mail.php';
        include_once 'Zend/Http/Client.php';
        include_once 'Zend/Db/Table/Abstract.php';
    } catch (Exception $e) {
        throw new Exception('Zend Framework should be in your include path.');
    }

    return false;
}

spl_autoload_register('Post_testAutoloader');
set_include_path(dirname(__DIR__) . '/library:' . get_include_path());
