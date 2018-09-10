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
    public function index() {
        form([
            ['keword', '加密内容', 'text', '12345678', []],
            ['key', '秘钥', 'text', 'ABCDEFF', []],
            ['line_1', '', 'line', '', []],
            ['de_keword', '加密密文', 'text', 'mGjO+M+n+IHoidl+ii9HiA==', []],
            ['de_key', '秘钥', 'text', 'ABCDEFF', []],
            ['line_1', '', 'line', '', []],
        ]);

        $keword = input("keword", "12345678");
        $key = input("key");
        if (!empty($keword)) {
            $str = des_encrypt($keword, $key);
            view("item/code", ['title' => "加密结果", "code" => "$str"]);
        }

        $keword = input("de_keword", "mGjO+M+n+IHoidl+ii9HiA==");
        $key = input("de_key", "ABCDEFF");
        if (!empty($keword)) {
            $str = des_decrypt($keword, $key);
            view("item/code", ['title' => "解密结果", "code" => $str?$str:"这坨屎里藏着毒..."]);
        }
    }

    public function encrypt() {
        form([
            ['keword', '加密内容', 'text', '', []],
            ['key', '秘钥', 'text', '', []],
            ['type', ' ', 'hidden', 'encrypt', []],
        ]);

        $keword = input("keword");
        $key = input("key");
        if (empty($keword)) {
            return;
        }
        show_msg("结果.", 1, 0);
        $str = des_encrypt($keword, $key);
        echo $str;
    }

    public function decrypt() {
        form([
            ['de_keword', '加密密文', 'text', '', []],
            ['de_key', '秘钥', 'text', '', []],
            ['type', '秘钥', 'hidden', 'decrypt', []],
        ]);

        $keword = input("de_keword");
        $key = input("de_key");
        if (empty($keword)) {
            return;
        }
        show_msg("结果.");
        $str = des_decrypt($keword, $key);
        echo $str;
    }

    public function save(){
        $key = input("key", "12345678");
        $keword = input("keword", "12345678");
    }
}

