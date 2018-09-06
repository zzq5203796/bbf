<?php

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli'? true: false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

define('IS_POST', $_SERVER['REQUEST_METHOD'] == "POST");
define('IS_AJAX', !empty($_SERVER['HTTP_X_REQUESTED_WITH']));


define("IS_CLEAR", false); 

define('BBF_START_TIME', microtime(true));
define('BBF_START_MEM', memory_get_usage());
define('DS', "/");

$DR = IS_CLI? __DIR__ . DS . "..": $_SERVER['DOCUMENT_ROOT'];
rtrim($DR, DS);
define('DOCUMENT_ROOT', $DR.DS);

require_once 'function/sys.php';
require_once 'function/json.php';
require_once 'function/file.php';
require_once 'function/msg.php';
require_once 'function/cli.php';
require_once 'function/matches.php';
require_once 'function/form.php';

require_once 'loader.php';
require_once '../app/common/request.php';
do_cli($argv);
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
    $lines = $e->getTrace();
    show_table($lines, ["file" => "文件", "line" => "行数"]);
    dump($e);
}