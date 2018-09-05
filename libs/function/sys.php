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
    if(IS_WIN){
        echo "If can not see Chinese in Window:
    You Can Run [chcp 65001] And 
    Choose CMD [Right Click] -> [ATTR] -> [Font] -> use [Lucida Console]\n\n";
    }

    if (!empty($argv[1]) && ($argv[1] == "?" || $argv[1] == "--help" || $argv[1] == "-h")) {
        show_cli_help();
    }
    if(in_array('-c', $argv) || in_array('-C', $argv)){
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
function get_url_info($type=''){
    $data = [
        "host" => $_SERVER['HTTP_HOST'],
        'name'=>$_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'],
        'path'=>$_SERVER['REDIRECT_URL'],
        'query'=>$_SERVER['QUERY_STRING'],
    ];
    return empty($type)?$data:$data[$type];
}
function get_url_path() {
    $path = $_GET['s']? : get_url_info('path');
    $path = trim($path, '/');
    $res = explode('/', $path);
    $res = [
        0 => empty($res[0])? '': $res[0],
        1 => empty($res[1])? 'index': $res[1]
    ];
    $res['path'] = $path;
    return $res;
}

function go_auto_home() {
    if(IS_CLI){
        show_cli_help();
    }else{
        require_once '../index.php';
    }
}

function server() {
    dump($_SERVER);
    die();
}

function default_key_value($data, $key, $value=null){    
    return isset($data[$key])? $data[$key]: $value;
}

function default_empty_value($data, $key='', $value=null){
    if($key===''){
        return empty($data)? $value: $data;
    }else{ 
        return empty($data[$key])? $value: $data[$key];
    }
}

if(!function_exists('input')){
    function input($key, $value=null){
        return default_key_value($_GET, $key, $value);
    }   
}