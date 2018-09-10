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
        $max_auto = 20;
        $tmp_auto = 5;
        $max = input("max", $max_auto);
        $temp = input("temp", $temp_auto);
        $max = $max > $max_auto? $max: $max_auto;
        $temp = $temp > $tmp_auto? $temp: $tmp_auto;
        form([
            ['max', '结束值', 'text', $max, []],
            ['temp', '间隔', 'text', $max, []],
        ]);

        $num1 = $num = 0;
        for($i = 0; $i <= $max && $num < $max; $i++){
            $num += rand(1, $temp);
            progress_bar($num, $max, [
                'id' => 0,
                'info' => ("now is in run:".$num),
                'msg'  => "count:".$i
                ]);

            $num1 += rand(1, $temp);
            // progress_bar($num1, $max, [
            //     'id' => 1,
            //     'info' => ("now is in run:".$num1),
            //     'msg'  => "count:".$i
            //     ]);
            sleep(1);
        }
    }

}