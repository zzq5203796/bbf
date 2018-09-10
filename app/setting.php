<?php

namespace app;
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */

class Setting
{
    public function __construct() {

    }

    public function index() {
        echo "welcome.";
    }

    public function menu() {
        $menu = json_decode(file_get_contents('../menu.json'), true);
        $menu['date'] = date("Y-m-d H:i;s");
        $str = $this->evalfor($menu);
        write("../menu1.json", $str);
        show_now();
        echo "try .";
    }

    public function evalfor($menu, $space = '') {
        $br = "\r\n";
        $old_space = $space;
        $space .= "    ";
        $obj_str = ['[', ']', '{', '}'];
        if (is_string($menu) || is_bool($menu)) {
            return '"' . $menu . '"';
        }
        $str = '';
        $is_obj = 0;
        foreach ($menu as $key => $item) {
            $top = is_numeric($key)? '': '"' . $key . '": ';
            $is_obj = $is_obj || is_string($key)? 1: 0;
            $str .= $space . $top . $this->evalfor($item, $space). "," . $br;
        }
        $str = rtrim($str, "," . $br) . $br;
        $str = $obj_str[$is_obj * 2] . (empty($menu)? '': $br . $str . $old_space) . $obj_str[$is_obj * 2 + 1];
        return $str;
    }

    //
    public function hosts(){
        include "host.php";
    }

}