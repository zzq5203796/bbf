<?php
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:36
 */

/**
 * 自动加载类
 * @param $class
 */

spl_autoload_register(function ($class_name) {
    $base_path = DOCUMENT_ROOT . DS ;
    $class_name = str_replace('\\',DS, $class_name);
    if (file_exists($base_path . $class_name . ".php")) {
        require_once ($base_path . $class_name . ".php");
    } else {
        throw new Exception("Unable to load Class $class_name. file no found $base_path.$class_name.", "123");
    }
});
