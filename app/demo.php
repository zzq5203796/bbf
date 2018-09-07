<?php

namespace app;

class Demo
{
    public function __construct() {

    }
    public function index() {

    }

    public function form() {
        form([
            ['keword', '加密内容', 'text', '', []],
            ['key', '秘钥', 'text', '', []],
            ['line_1', '', 'line', '', []],
            ['de_keword', '加密密文', 'text', '', []],
            ['de_key', '秘钥', 'text', '', []],
            ['line_1', '', 'line', '', []],
        ]);
    }
    public function item() {
        view("item/color", ['232525','3b3b3b ', '3a3c3e', '4d4d4d', '464242', '606366', '787878', '808080', 'a9b7c6']);
        view("item/colorText", [
            ['3B3B3B', '787878'],
            ['4D4D4D', 'ACACAC'],
        ]);
    }

    public function loading() {

        $max = 15;
        for($i = 0; $i<=$max;$i++){
            progress_bar($i, $max, [
                'info' => ("now is in run:".($i+10)),
                'msg'  => "hello word.".$i
                ]);
            sleep(1);
        }
    }

}