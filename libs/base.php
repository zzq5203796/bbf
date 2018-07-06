<?php


define('BBF_START_TIME', microtime(true));
define('BBF_START_MEM', memory_get_usage());
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('DS', "/");

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli'? true: false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);


require_once 'debug.php';
require_once 'loader.php';
require_once 'function.php';

$url_path = get_url_path();
try {
    if (empty($url_path['path'])) {
        go_auto_home();
        die;
    }

    define('CLASS_NAME', $url_path[0]);
    define('METHOD_NAME', $url_path[1]);
    $class = APPS . "\\" . $url_path[0];
    $method = $url_path[1];
    $object = new $class();
    $res = $object->$method();
} catch (\Exception $e) {
    echo $e->getMessage(), "\n";
    dump($e);
}