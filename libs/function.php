<?php

function get_request_uri() {
    return $_SERVER['REQUEST_URI'];
}

function get_host() {
    return $_SERVER['HTTP_HOST'];
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

/**
 * 获取文件夹里面所有文件
 * @param $dir_t
 * @param array $extra 不读取文件后缀
 * @return array
 */
function get_dir_tree($dir_t, $extra = []) {
    $dir_tree = ['name' => '.'];
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
        if (in_array($name, ['..', '.']) || in_array(array_pop(explode('.', $name)), $extra)) {
            continue;
        }
        $filename = $dir_t . $name;
        $child = get_dir_tree($filename);
        $url = $host . $dir_t . $name;
        $dir_tree[] = ['url' => $url, 'name' => $name, 'child' => $child];
    }
    return $dir_tree;
}

function add_tree($url = '#', $name = '', $child = []) {
    return ['url' => $url, 'name' => $name, 'child' => $child];
}

function write($file, $data) {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/runtime/" . $file;
    $myfile = fopen($file, "w") or die("Unable to open file!");
    fwrite($myfile, $data);
    fclose($myfile);
}

function read($file, $data) {
    $file = $_SERVER['DOCUMENT_ROOT'] . "/runtime/" . $file;
    $myfile = fopen($file, "r") or die("Unable to open file!");
    $content = fread($myfile, filesize($file));
    fclose($myfile);
    return $content;

}