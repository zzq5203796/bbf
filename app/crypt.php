<?php

namespace app;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/7/14
 * Time: 15:09
 */

class Crypt
{
    public function encrypt() {
        form([
            ['keword', '加密内容', 'text', '', []],
        ]);

        $keword = input("keword");
        show_msg("结果.");
        $str = des_encrypt($keword);
        echo $str;
    }
    public function decrypt() {
        form([
            ['keword', '加密串', 'text', '', []],
        ]);

        $keword = input("keword");
        show_msg("结果.");
        $str = des_decrypt($keword);
        echo $str;
    }
}

