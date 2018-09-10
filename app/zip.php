<?php

namespace app;
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */

class Zip
{
    protected $path;

    public function __construct() {
        $this->path = "../public/min/";
    }

    public function index() {
        echo "welcome.";
    }

    public function menu() {
        $this->js();
        $this->css();
        show_msg("end.");
    }

    public function js() {
        $data = [
            "js/menu",
            "js/common" => [
                "js/common",

                "js/plug/auto",
                "js/plug/ajax",
                "js/plug/full-window",
                "js/plug/keydown",
                "js/plug/message",
                "js/plug/progress-bar",
                "js/plug/runscroll",
                "js/plug/store",
                "js/plug/string",
                "js/plug/tabs",
                "js/plug/sound",
            ],
        ];
        foreach ($data as $vo) {
            $this->parse_js($vo, ['time' => 1]);
        }
        show_msg("run js success.");
    }

    public function parse_js($file, $option = []) {
        $option = array_merge(['name' => '', 'time' => false, 'check' => false], $option);
        $ext = "js";
        $content = '';
        if (is_array($file)) {
            foreach ($file as $vo) {
                $content .= $this->parse_js($vo, ['write' => false]);
            }
            $file = $file[0];
        } else {
            $content = read("../$file.$ext");
            $content = '/* '.$file.' */; '.$this->parse_js_content($content);
        }
        if (!isset($option['write']) || $option['write']) {
            $now = date("Y-m-d H:i:s");
            $content = ";var version='$now';$content";
            if ($option['time']) {
                $content = '/* ' . $now . " */\r\n" . $content;
            }

            write($this->path."$file.min.$ext", $content);
        }
        return $content;
    }

    public function css() {
        $data = [
            "css/menu"
        ];
        foreach ($data as $vo) {
            parse_css($vo, ['time' => 1]);
        }
        show_msg("run css success.");
    }

    protected function parse_js_content($js) {
        $h1 = 'http://';
        $s1 = '【:??】';    //标识“http://”,避免将其替换成空
        $h2 = 'https://';
        $s2 = '【s:??】';    //标识“https://”
        //        preg_match_all('#include\("([^"]*)"([^)]*)\);#isU', $js, $arr);
        //        if (isset($arr[1])) {
        //            foreach ($arr[1] as $k => $inc) {
        //                $path = "http://www.xxx.com/";          //这里是你自己的域名路径
        //                $temp = file_get_contents($path . $inc);
        //                $js = str_replace($arr[0][$k], $temp, $js);
        //            }
        //        }
        //        $js = preg_replace('#function include([^}]*)}#isU', '', $js);//include函数体
        $js = preg_replace('#\/\*.*\*\/#isU', '', $js);//块注释
        $js = str_replace($h1, $s1, $js);
        $js = str_replace($h2, $s2, $js);
        $js = preg_replace('#\/\/[^\n]*#', '', $js);//行注释
        $js = str_replace($s1, $h1, $js);
        $js = str_replace($s2, $h2, $js);
        $js = str_replace("\t", "", $js);//tab
        $js = preg_replace('#\s*(=|>=|\?|:|==|\+|\|\||\+=|>|<|\/|\-|,|{|}|;|\(|\))\s*#', '$1', $js);//字符前后多余空格
        $js = str_replace("\t", "", $js);//tab
        $js = str_replace("\r\n", "", $js);//回车
        $js = str_replace("\r", "", $js);//换行
        $js = str_replace("\n", "", $js);//换行
        $js = trim($js, " ");
        return $js;
    }

}

/**
 *  合并压缩css
 * @param $files
 * @param array $option [name|time]
 * @return string
 */
function parse_css($files, $option = []) {
    $path = "../public/min/";
    $ext = "css";
    $option = array_merge(['name' => '', 'time' => false, 'check' => false], $option);
    $files = is_array($files)?: [$files];
    $filename = $option['name'];
    $filename = empty($filename)? (count($files) == 1? $files[0] . ".min": "$ext/" . md5(implode(',', $files))): "$filename.min";
    $css_url = $path . $filename . ".$ext";
    if ($option['check'] && file_exists($css_url)) {
        return $css_url;
    }
    $content = '';
    foreach ($files as $file) {
        $content .= read("../$file.$ext");
    }
    $content = parse_css_content($content);
    if ($option['time']) {
        $content = '/* ' . date("Y-m-d H:i:s") . " */\r\n" . $content;
    }
    write($css_url, $content);
    return $css_url;
}

function parse_css_content($content) {
    $content = preg_replace('#\/\*.*\*\/#isU', '', $content);//清除块注释;
    //    $content = str_replace(["\r\n", "\n", "\t"], '', $content); //清除换行符 制表符 空格

    $content = preg_replace('#\s*(:|;|{|}|,)\s*#', '$1', $content);//字符前后多余空格
    return $content;
}

