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
    if (!empty($argv[1]) && ($argv[1] == "?" || $argv[1] == "--help" || $argv[1] == "-h")) {
        show_cli_help();
    }
    for ($i = 1; $i < count($argv); $i = $i + 2) {
        $_GET[$argv[$i]] = $argv[$i + 1];
    }
    if (!empty($_GET['params'])) {
        parse_str($_GET['params'], $arr);
        foreach ($arr as $key => $vo) {
            $_GET[$key] = $vo;
        }
    }

    return true;
}

function show_cli_help() {
    $file = __DIR__ . "/cli_help.txt";
    $myfile = fopen($file, "r") or die("Unable to open file! $file");
    $content = fread($myfile, filesize($file));
    fclose($myfile);
    echo $content;
    exit();
}

function get_url_path() {
    $path = $_GET['s']?: '';

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
    require_once '../index.php';
}

function server() {
    dump($_SERVER);
}

function show_now() {
    echo date("Y-m-d H:i:s") . " <br/>\r\n";
}

/**
 * 获取文件夹里面所有文件
 * @param $dir_t
 * @param array $extra 不读取文件后缀
 * @return array
 */
function get_dir_tree($dir_t, $extra = []) {
    $dir_tree = [];
    $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/";
    $dir = __DIR__ . DS . ".." . DS;
    $dir_t .= DS;

    $full_dir = $dir . $dir_t;
    if (!is_dir($full_dir)) {
        return [];
    }
    //获取也就是扫描文件夹内的文件及文件夹名存入数组 $filesnames
    $filesnames = scandir($full_dir);
    foreach ($filesnames as $name) {
        $ext = trim(array_pop(explode('.', $name)));
        if (in_array($name, ['..', '.']) || in_array($ext, $extra)) {
            continue;
        }
        $filename = $dir_t . $name;
        $child_tree = get_dir_tree($filename, $extra);
        $url = $host . $dir_t . $name;
        $dir_tree[] = add_tree($url, $name, $child_tree);
    }
    return $dir_tree;
}

function add_tree($url = '#', $name = '', $child = []) {
    $count = count($child);

    return ['url' => $url, 'name' => $name, 'child' => $child, 'length' => $count, 'all_length' => $count + array_sum_by_key($child, 'length')];
}

function array_sum_by_key($data, $key = "length") {
    $count = 0;
    foreach ($data as $vo) {
        $count += $vo[$key];
    }
    return $count;
}

function write($file, $data) {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/runtime/" . $file;
    $myfile = fopen($file, "w") or die("Unable to open file! $file");
    fwrite($myfile, $data);
    fclose($myfile);
}

function read($file, $data = []) {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/runtime/" . $file;
    $myfile = fopen($file, "r") or die("Unable to open file! $file");
    $content = fread($myfile, filesize($file));
    fclose($myfile);
    return $content;

}