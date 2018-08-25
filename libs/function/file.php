<?php

/**
 * 获取文件夹里面所有文件
 * @param $dir_t
 * @param array $extra 不读取文件后缀
 * @return array
 */
function get_dir_tree($dir_t, $extra = []) {
    $dir_tree = [];
    $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/";
    $dir = root_dir();
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
        $dir_tree[] = dir_tree_item($url, $name, $child_tree);
    }
    return $dir_tree;
}

function dir_tree_item($url = '#', $name = '', $child = []) {
    $count = count($child);

    return ['url' => $url, 'name' => $name, 'child' => $child, 'length' => $count, 'all_length' => $count + array_sum_by_key($child, 'length')];
}

/**
 * 获取文件详情 大小 类型
 */
function get_dir_info($dir_t, $extra = []) {
    $dir_tree = [];
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
        $child_tree = get_dir_info($filename, $extra);
        $dir_tree[] = [
            "name"     => $name,
            "filename" => $filename,
            "size"     => filesize(__DIR__ . "/../" . $filename),
        ];
        dir_tree_item($url, $name, $child_tree);
    }
    return $dir_tree;
}

function array_sum_by_key($data, $key = "length") {
    $count = 0;
    foreach ($data as $vo) {
        $count += $vo[$key];
    }
    return $count;
}

/** 根目录 */
function root_dir() {
    $dir = DOCUMENT_ROOT;
    return $dir;
}

/** 创建文件 */
function create_dir($file, $num = 0) {
    if ($num > 10)
        return true;
    $dirname = dirname($file);
    if (!file_exists($dirname)) {
        create_dir($dirname, ++$num);
        if (!in_array(substr($dirname, -2), [".", ".."])) {
            mkdir($dirname, 0777, true);
        }
    }
    return true;
}

function write($file, $data, $mode = "w") {
    $file = root_dir() . "runtime/" . $file;
    create_dir($file);
    $myfile = fopen($file, $mode) or die("Unable to open file! $file");
    fwrite($myfile, $data);
    fclose($myfile);
}

function read($file, $mode = "r", $opt = []) {
    $file = DOCUMENT_ROOT . "runtime/" . $file;
    if(!file_exists($file)){
        return false;
    }
    $myfile = fopen($file, $mode);
    if ($myfile === false) {
        return false;
    }
    $size = filesize($file);
    $content = fread($myfile, $size);
    fclose($myfile);
    if (!empty($opt['info'])) {
        $data = [$content, $size, pathinfo($file)];
    } else {
        $data = $content;
    }
    return $data;
}

function logs($log, $type = "log", $mode = "a+", $opt = []) {
    $_type = ($type == "log" || empty($type))? "": ".$type";
    $now = date("H:i:s");
    $log = "[$now] $log\n\n";
    $file = "log/" . date("Ymd") . "$_type.txt";
    write($file, $log, $mode);
}

function locks($file, $data = null) {
    $file = "lock/$file.lock";
    if ($data === null) {
        if(IS_CLEAR) {
            return '';
        }
        return read($file);
    } else {
        write($file, $data);
        return true;
    }
}

function help($file){
    $file = "../data/help/$file.txt";
    $txt = read($file);
    return $txt;
}

/* 下载文件 */
function down_file($file, $file_name = "") {
    //    $file_name = $file;
    $data = read($file, "r", ['info' => 1]);
    if ($data === false) {
        show_msg($file_name . "文件找不到");
        exit ();
    }
    list($content, $size, $base) = $data;
    $file_name = empty($file_name)? $base['basename']: $file_name;
    //打开文件
    //输入文件标签
    Header("Content-type: application/octet-stream");
    Header("Accept-Ranges: bytes");
    Header("Accept-Length: " . $size);
    Header("Content-Disposition: attachment; filename=" . $file_name);
    //输出文件内容
    //读取文件内容并直接输出到浏览器
    echo $content;
    exit ();
}