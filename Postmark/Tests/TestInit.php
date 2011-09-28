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
    } catch (Exception $e) {
        throw new Exception('Zend Framework should be in your include path.');
    }

    include_once dirname(__DIR__) . '/Mail/Transport/Postmark.php';
    include_once dirname(__DIR__) . '/Services/PostmarkApp.php';
    return false;
}

spl_autoload_register('Post_testAutoloader');
