<?php

namespace app;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/7/14
 * Time: 15:09
 */

class Menu
{
    public function __construct() {
    }

    public function index() {
        $menu = get_menu();
        $fields = [
            ['name', '标题', 'text'],
            ['url', '链接', 'text']
            // ['target', '新窗口', 'redeio'],
            // ['target', '新窗口', 'redeio'],
        ];
        $data = $menu;
        view("tree-form", ["fields" => $fields, 'data' => $data]);
    }

}
