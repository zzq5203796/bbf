<?php

function get_menu($opts=[], $extra=[]){
    $menu = json_decode(file_get_contents(root_dir().'/data/menu.json'), true);
    $menu_t = file_get_contents(root_dir() . '/menu.json');
    $menu_t = empty($menu_t)? ['menu' => [], 'dir' => []]: json_decode($menu_t, true);
    $tree = $menu['menu'];
    $dirs = $menu['dir'];
    foreach ($menu_t['dir'] as $key => $vo) {
        $name = is_numeric($key)? $vo: $key;
        foreach ($dirs as $k1 => $v1) {
            $name1 = is_numeric($k1)? $v1: $k1;
            if(empty($dirs[$name])){
                $dirs[$name] = $vo;
            }
        }
    }

    foreach ($menu['dir'] as $key => $value) {
        if ($action == $key || $action == $value) {
            $dirs = [$key => $value];
            break;
        }
    }
    $extra = array_merge($extra, ['js', 'css', 'fonts', 'font', 'md', 'ss']);
    if(empty($opts['read_dir'])){
        $tree[] = $dirs;
    }else{
        readdirs($tree, $dirs);
    }
    return $tree;
}


function readdirs(&$tree, $dirs){
    foreach ($dirs as $key => $name) {
        if(is_array($name)){
            $dir_tree = [];
            readdirs($dir_tree, $name);
            $tree[] = dir_tree_item($key, $key, $dir_tree);
        }else{
            $dir = is_numeric($key)? $name: $key;
            $dir_tree = get_dir_tree($dir, ['js', 'css']);
            $tree[] = dir_tree_item($key, $name, $dir_tree);
        }
    }
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
    $myfile = @fopen($file, $mode) or die("Unable to open file! $file");
    fwrite($myfile, $data);
    fclose($myfile);
}

function read($file, $mode = "r", $opt = []) {
    $file = DOCUMENT_ROOT . "runtime/" . $file;
    if (!file_exists($file)) {
        return false;
    }
    $myfile = @fopen($file, $mode);
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

function auth($file){
    if(!file_exists($file)){
        return false;
    }
    $data = [
        'is_dir' =>is_dir($file),
        'r' => is_readable($file),
        'w' => is_writable($file)
    ];
    return $data;
}

function logs($log, $type = "log", $mode = "a+", $opt = []) {
    empty($mode) && $mode = "a+";
    $_type = ($type == "log" || empty($type))? "": ".$type";

    $now = date("H:i:s");
    $log = "[$now] $log\n\n";
    $file = "log/" . date("Ymd") . "$_type.txt";
    if (is_array($opt) && !empty($opt["type"])) {
        switch ($opt["type"]) {
            case 'mode':
                $file = $opt["name"] . "/log/" . date("Ymd") . "$_type.txt";
                break;
        }
    }
    write($file, $log, $mode);
}

function mode_logs($log, $mode, $type = "log") {
    logs($log, $type, "a+", ['type' => 'mode', 'name' => $mode]);
}

function mode_locks($file, $mode, $data = null) {
    return locks($file, $data, ['type' => 'mode', 'name' => $mode]);
}

function locks($file, $data = null, $opt = []) {
    $file = "lock/$file.lock";
    if (is_array($opt) && !empty($opt["type"])) {
        switch ($opt["type"]) {
            case 'mode':
                $file = $opt["name"] . "/" . $file;
                break;
        }
    }
    if ($data === null) {
        if (IS_CLEAR) {
            return '';
        }
        return read($file);
    } else {
        write($file, $data);
        return true;
    }
}

function help($file) {
    $file = "../data/help/$file.txt";
    $txt = read($file);
    return $txt;
}

function view($file, $fields) {
    $file = DOCUMENT_ROOT . "view/" . $file . ".html";
    require($file);
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

/**
 * 抓取图片 文件
 * @param $file_url
 * @param $save_to
 * @return bool
 */
function curl_file($file_url, $save_to, $fail = '', $ext = '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_URL, $file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file_content = curl_exec($ch);
    curl_close($ch);
    $check = trim($file_content);
    if ($check == $fail || !$file_content) {
        return false;
    }
    $filename = pathinfo($file_url, PATHINFO_BASENAME);

    $pathfile = $save_to . $filename;
    empty($ext) || $pathfile .= ".$ext";

    create_dir($pathfile);
    $downloaded_file = fopen($pathfile, 'w');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);
    return true;

}