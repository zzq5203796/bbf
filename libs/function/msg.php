<?php

/**
 * @var array 区间时间信息
 */
$info = [];

/**
 * @var array 区间内存信息
 */
$mem = [];

/**
 * 记录时间（微秒）和内存使用情况
 * @access public
 * @param  string $name 标记位置
 * @param  mixed $value 标记值(留空则取当前 time 表示仅记录时间 否则同时记录时间和内存)
 * @return void
 */
function remark($name, $value = '') {
    $info[$name] = is_float($value)? $value: microtime(true);

    if ('time' != $value) {
        $mem['mem'][$name] = is_float($value)? $value: memory_get_usage();
        $mem['peak'][$name] = memory_get_peak_usage();
    }
}

/**
 * 统计某个区间的时间（微秒）使用情况 返回值以秒为单位
 * @access public
 * @param  string $start 开始标签
 * @param  string $end 结束标签
 * @param  integer $dec 小数位
 * @return string
 */
function getRangeTime($start, $end, $dec = 6) {
    if (!isset($info[$end])) {
        $info[$end] = microtime(true);
    }

    return number_format(($info[$end] - $info[$start]), $dec);
}

/**
 * 统计从开始到统计时的时间（微秒）使用情况 返回值以秒为单位
 * @access public
 * @param  integer $dec 小数位
 * @return string
 */
function getUseTime($dec = 6) {
    return number_format((microtime(true) - BBF_START_TIME), $dec);
}

/**
 * 获取当前访问的吞吐率情况
 * @access public
 * @return string
 */
function getThroughputRate() {
    return number_format(1 / getUseTime(), 2) . 'req/s';
}

/**
 * 记录区间的内存使用情况
 * @access public
 * @param  string $start 开始标签
 * @param  string $end 结束标签
 * @param  integer $dec 小数位
 * @return string
 */
function getRangeMem($start, $end, $dec = 2) {
    if (!isset($mem['mem'][$end])) {
        $mem['mem'][$end] = memory_get_usage();
    }

    $size = $mem['mem'][$end] - $mem['mem'][$start];
    $a = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pos = 0;

    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }

    return round($size, $dec) . " " . $a[$pos];
}

/**
 * 统计从开始到统计时的内存使用情况
 * @access public
 * @param  integer $dec 小数位
 * @return string
 */
function getUseMem($dec = 2) {
    $size = memory_get_usage() - THINK_START_MEM;
    $a = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pos = 0;

    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }

    return round($size, $dec) . " " . $a[$pos];
}

/**
 * 统计区间的内存峰值情况
 * @access public
 * @param  string $start 开始标签
 * @param  string $end 结束标签
 * @param  integer $dec 小数位
 * @return string
 */
function getMemPeak($start, $end, $dec = 2) {
    if (!isset($mem['peak'][$end])) {
        $mem['peak'][$end] = memory_get_peak_usage();
    }

    $size = $mem['peak'][$end] - $mem['peak'][$start];
    $a = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pos = 0;

    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }

    return round($size, $dec) . " " . $a[$pos];
}

/**
 * 获取文件加载信息
 * @access public
 * @param  bool $detail 是否显示详细
 * @return integer|array
 */
function getFile($detail = false) {
    $files = get_included_files();

    if ($detail) {
        $info = [];

        foreach ($files as $file) {
            $info[] = $file . ' ( ' . number_format(filesize($file) / 1024, 2) . ' KB )';
        }

        return $info;
    }

    return count($files);
}

/**
 * 浏览器友好的变量输出
 * @access public
 * @param  mixed $var 变量
 * @param  boolean $echo 是否输出(默认为 true，为 false 则返回输出字符串)
 * @param  string|null $label 标签(默认为空)
 * @param  integer $flags htmlspecialchars 的标志
 * @return null|string
 */
function dump($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE) {
    $label = (null === $label)? '': rtrim($label) . ':';

    ob_start();
    var_dump($var);
    $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', ob_get_clean());

    if (IS_CLI) {
        $output = PHP_EOL . $label . $output . PHP_EOL;
    } else {
        if (!extension_loaded('xdebug')) {
            $output = htmlspecialchars($output, $flags);
        }

        $output = '<pre>' . $label . $output . '</pre>';
    }

    if ($echo) {
        echo($output);
        return;
    }

    return $output;
}

/**
 * 调试信息注入到响应中
 * @access public
 * @param  Response $response 响应实例
 * @param  string $content 返回的字符串
 * @return void
 */
function inject(Response $response, &$content) {
    $config = Config::get('trace');
    $type = isset($config['type'])? $config['type']: 'Html';
    $class = false !== strpos($type, '\\')? $type: '\\think\\debug\\' . ucwords($type);

    unset($config['type']);

    if (!class_exists($class)) {
        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }

    /** @var \think\debug\Console|\think\debug\Html $trace */
    $trace = new $class($config);

    if ($response instanceof Redirect) {
        // TODO 记录
    } else {
        $output = $trace->output($response, Log::getLog());

        if (is_string($output)) {
            // trace 调试信息注入
            $pos = strripos($content, '</body>');
            if (false !== $pos) {
                $content = substr($content, 0, $pos) . $output . substr($content, $pos);
            } else {
                $content = $content . $output;
            }
        }
    }
}

function echo_line($var, $lable = '') {
    echo ($lable? $lable . ': ': '') . (is_array($var)? dump($var, false): $var) . '<br>';
}

/**
 * 换行符
 * @param int $num
 * @return string
 */
function get_br($num = 1) {
    $br = IS_CLI? "\n": "<br/>";
    $brs = "";
    for ($i = 0; $i < $num; $i++) {
        $brs .= $br;
    }
    return $brs;
}

/**
 * 空格
 * @param int $num
 * @return string
 */
function get_space($num = 1) {
    $br = IS_CLI? " ": "&nbsp;";
    $brs = "";
    for ($i = 0; $i < $num; $i++) {
        $brs .= $br;
    }
    return $brs;
}

/**
 * 输出当前时间
 * @param bool $echo 是否打印
 * @return false|string
 */
function show_now($echo = true) {
    $str = date("Y-m-d H:i:s");
    if ($echo) {
        echo "北京时间:" . get_space(2) . $str . get_br();
    }
    return $str;
}

/**
 * 打印字符串
 * @param $msg
 * @param bool $echo 是否打印
 * @param bool $time 是否打印时间
 * @param bool $num 空格数
 * @return string
 */
function show_msg($msg, $echo = true, $time = true, $num = 0) {
    $call_line = debug_backtrace()[0];
    $str = show_now(0);

    $top = $time? "[$str] " . setlength(array_pop(explode("/", $call_line['file'])), 10) . get_space(1) . $call_line['line'] . get_space(2): "";
    $body = $top . get_space($num) . $msg . get_br();
    if ($echo) {
        echo $body;
    }
    return $body;
}

function setlength($str, $num = 10) {
    $len = $num - strlen($str) + 1;
    $len = $len > 0? $len: 0;

    return $str . get_space($len);
}

function show_msgs($data, $type = "array", $num = 0, $opt = []) {
    $opt = array_merge(["key" => ""], $opt);
    $temp = $num * (IS_CLI? 4: 1);
    $echo = true;
    if ($type == "dump") {
        dump($data);
        return true;
    } elseif ($type == "table") {
        show_table($data);
        return true;
    }
    $mark = [
        "dump"  => ["{", "}", " => ", "[", "]"],
        "array" => ["[", "],", " => ", "'", "'"],
        "json"  => ["{", "},", ": ", "", "'"],
        "help"  => ["", "", "", "", ""],
    ];

    empty($mark[$type]) && $type = "array";
    $top = $opt['key'];

    $mark = $mark[$type];
    $mark_start = $mark[0];
    $mark_end = $mark[1];
    $mark_tmp = $mark[2];
    $mark_key_str = is_numeric($top)? "": $mark[3];
    $mark_value_str_end = $mark_value_str = is_numeric($data)? "": $mark[4];
    $mark_value_str_end = $mark_value_str_end . ",";

    $top = ($top !== "")? ($mark_key_str . $top . $mark_key_str . $mark_tmp): "";
    $body = "";
    if (is_array($data)) {
        if (!IS_CLI && $num == 0 && $echo)
            echo("<pre>");
        $body .= show_msg($top . $mark_start, $echo, 0, $temp);
        foreach ($data as $key => $vo) {
            $opt["key"] = $type == "help"? "": $key;
            $body .= show_msgs($vo, $type, $num + 1, $opt);
        }
        $body .= show_msg($mark_end, $echo, 0, $temp);
        if (!IS_CLI && $num == 0 && $echo)
            echo("</pre>");
    } else {
        $top !== "" && ($data = $top . $mark_value_str . $data . $mark_value_str_end);
        $body .= show_msg($data, $echo, ($top === "" && $num == 0), $temp);
    }
    if (!$echo && $num == 0) {
        echo "<pre>$body</pre>";
    }
    return $body;
}

function show_table($data, $keys = []) {
    is_array($data[0]) || $data = [$data];
    $temp_keys = empty($keys)? array_keys($data[0]): $keys;
    list($tr, $td) = IS_CLI? [get_br(), get_space(2)]: ["tr", "td"];
    $body = "";
    $line = "";
    $keys = [];
    $tables = [];
    foreach ($temp_keys as $key => $vo) {
        $vo = is_array($vo)? $vo: (is_numeric($key)? ['key' => $vo, 'name' => $vo]: ['key' => $key, 'name' => $vo]);
        $keys[] = $vo;
        $name = $vo['name'];
        $tables[0][] = $name;
        IS_CLI && $line .= "$td$name";
        IS_CLI || $line .= "<$td class=\"item-$name\">$name</$td>";
    }
    IS_CLI && $body .= "$tr$line$tr";
    IS_CLI || $body .= "<$tr>$line</$tr>";

    foreach ($data as $key_data => $item) {
        $line = "";
        foreach ($keys as $key_value) {
            $key = $key_value['key'];
            $vo = isset($item[$key])? $item[$key]: "";

            $tables[$key_data + 1][] = $vo;
            IS_CLI && $line .= "$td$vo";
            IS_CLI || $line .= "<$td><div>$vo</div></$td>";
        }

        IS_CLI && $body .= "$tr$line$tr";
        IS_CLI || $body .= "<$tr>$line</$tr>";
    }
    $style = "<style>table.show-msg tr td > div{padding:2px 4px; display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 3; overflow: hidden;}</style>";

    IS_CLI || $body = $style . "<table class='show-msg'>$body</table>";
    echo $body;
    return $body;
}

function cli_input($title) {
    if (IS_CLI) {
        $str = "Please input $title:";
        fwrite(STDOUT, $str);
        $value = fgets(STDIN);
        $str = "you input $title:" . $value;
        echo $str;
    }
    return trim($value);
}

function progress_bar($num, $max = 500, $opt = []) {
    global $progressNum;
    $id = default_empty_value($opt, 'id', 0);

    $is_new = empty(PROGRESS_BAR) || PROGRESS_BAR === 'PROGRESS_BAR';
    $progressNum++;
    $num = $num > $max? $max: $num;

    $tem = 1; // 清空间隔
    $is_end = $num == $max; // 是否结束

    $maxLen = 0.001; // 精确位
    $value = (ceil($num / $max / $maxLen * 100) * $maxLen) . "%";

    $data = array_merge($opt, [
        'id'     => $id,
        'is_new' => $is_new,
        'type'   => "create",
        'value'  => $value,
        'num'    => $num,
        'max'    => $max,
        'clear'  => ($progressNum % $tem == 0),
        'end'    => $is_end
    ]);

    if ($is_new) {
        ob_start();
        define(PROGRESS_BAR, true);
        header("Content-Encoding: none\r\n");
        view("progress", $data);
    } else {
        $data['type'] = "push";
        view("progress", $data);
    }

    echo str_repeat(" ", 1024 * 64);
    ob_flush();
    flush();
    if ($is_end && $id === 0) {
        ob_end_flush();//输出并关闭缓冲
    }
}

function show_icon($str) {
    echo '<link rel="shortcut icon" href="/runtime/img/' . $str . '.png">';
}