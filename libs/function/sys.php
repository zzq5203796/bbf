<?php

function get_request_uri() {
    return $_SERVER['REQUEST_URI'];
}

function get_host() {
    return $_SERVER['HTTP_HOST'];
}

function do_cli($argv) {
    if (!IS_CLI) {
        return false;
    }
    if (IS_WIN) {
        echo "If can not see Chinese in Window:
    You Can Run [chcp 65001] And 
    Choose CMD [Right Click] -> [ATTR] -> [Font] -> use [Lucida Console]\n\n";
    }

    if (!empty($argv[1]) && ($argv[1] == "?" || $argv[1] == "--help" || $argv[1] == "-h")) {
        show_cli_help();
    }
    if (in_array('-c', $argv) || in_array('-C', $argv)) {
        define("IS_CLEAR", true);
        $argv = array_merge(array_diff($argv, ["-c", "-C"]));
    }
    $params = [];
    for ($i = 1; $i < count($argv); $i = $i + 2) {
        $params[$argv[$i]] = $argv[$i + 1];
    }
    if (!empty($params['-p'])) {
        parse_str($params['-p'], $arr);
        foreach ($arr as $key => $vo) {
            $params[$key] = $vo;
        }
    }

    empty($params['-s']) || $params['s'] = $params['-s'];
    unset($params['-s']);
    unset($params['-p']);
    foreach ($params as $key => $value) {
        $_GET[$key] = $value;
    }

    return true;
}

function show_cli_help() {
    $content = help("cli_help");
    echo $content;
    exit();
}

function get_url_info($type = '') {
    $data = [
        "host"  => $_SERVER['HTTP_HOST'],
        'name'  => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'],
        'path'  => $_SERVER['REDIRECT_URL'],
        'query' => $_SERVER['QUERY_STRING'],
    ];
    return empty($type)? $data: $data[$type];
}

function get_url_path() {
    $path = $_GET['s']?: get_url_info('path');
    $path = trim($path, '/');
    $res = explode('/', $path);
    $res = [
        0 => empty($res[0])? '': $res[0],
        1 => empty($res[1])? 'index': $res[1]
    ];
    $res['path'] = $path;
    return $res;
}

function get_http_host($url) {
    $link = parse_url($url);
    return $link['scheme'] . "://" . $link["host"] . "/";
}

function go_auto_home() {
    if (IS_CLI) {
        show_cli_help();
    } else {
        require_once '../index.php';
    }
}

function server() {
    dump($_SERVER);
    die();
}

function default_key_value($data, $key, $value = null) {
    return isset($data[$key])? $data[$key]: $value;
}

function default_empty_value($data, $key = '', $value = null) {
    if ($key === '') {
        return empty($data)? $value: $data;
    } else {
        return empty($data[$key])? $value: $data[$key];
    }
}

function inputs($data) {
    $res = [];
    foreach ($data as $key => $vo) {
        if (is_array($vo)) {
            $key1 = $vo['name'];
        } else {
            $key1 = $vo;
        }
        $name = is_numeric($key)? $key1: $key;
        $auto = '';
        $res[$key1] = input($name, $auto);
    }
    return $res;
}

if (!function_exists('input')) {
    function input($key, $value = null) {
        $data = default_key_value($_POST, $key);
        $data === null && $data = default_key_value($_GET, $key, $value);
        if ($data === '') {
            $data = $value;
        }
        return $data;
    }
}

/**
 * PHP DES 加密程式 BY openssl
 *
 * @param $key string 秘钥
 * @param $encrypt string 要加密内容
 * @return string 密文
 */
function des_encrypt($encrypt, $key = "") {
    empty($key) && $key = read("../data/auto.key", "r");
    empty($key) && $key = "ABCDEFG";
    return base64_encode(openssl_encrypt($encrypt, "DES-ECB", $key, OPENSSL_RAW_DATA));
}

/**
 * PHP DES 解密程式 BY openssl
 *
 * @param $key string 秘钥 1-8
 * @param $decrypt string 密文
 * @return string 明文
 */
function des_decrypt($decrypt, $key = "") {
    empty($key) && $key = read("../data/auto.key", "r");
    empty($key) && $key = "ABCDEFG";
    return openssl_decrypt(base64_decode($decrypt), "DES-ECB", $key, OPENSSL_RAW_DATA);
}

function array_merge_two() {
    $args = func_get_args();
    $keys = $args[0];
    $data = [];
    $first = $args[1];
    foreach ($first as $key => $vo) {
        for ($i = 1; $i < func_num_args(); $i++) {          //使用for循环
            $item = func_get_arg($i);
            $name = isset($keys[$i - 1])? $keys[$i - 1]: $i - 1;
            $value = isset($item[$key])? $item[$key]: '';
            $data[$key][$name] = $value;
        }
    }
    return $data;
}

function array_sort($array, $keys, $type = 'asc') {
    //$array为要排序的数组,$keys为要用来排序的键名,$type默认为升序排序
    $keysvalue = $new_array = [];
    foreach ($array as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $array[$k];
    }
    return $new_array;
}



