<?php

namespace app;

class Reptile
{
    public function __construct() {
    }

    public function index() {

    }

    private function get_xuange_url() {
        $encrypt = "thaFqsfrEsz9Rp7Ld0cf0HkvZAXcd/CxZWv33T2fWUTF4XZNllMmwg==";
        $str = des_decrypt($encrypt);
        if (empty($str)) {
            show_msg("please input key.");
            die();
        }
        return $str;
    }

    public function see() {
        $page = input("page");
        $temp = input("temp", 1);
        $temp = $temp > 0? $temp: 1;
        form([
            ['page', '页码', 'text', '', []],
            ['temp', '间隔', 'text', $temp, []],
        ]);
        if (!is_numeric($page) || $page < 0 || $page > 999999) {
            show_msg("页码必须为 0-999999 的 整数", 1, 0);
            return;
        }
        $num = 1000000 + $page;
        $url = $this->get_xuange_url();
        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $uri = $url . $num;
            $data[] = ['url' => $uri];
            $num += $temp;
        }
        view('imgbox', $data);
    }

    public function manhua() {
        form([
            ['page', '页码', 'text', '', []],
        ]);

        $page = input("page");
        if (!is_numeric($page) || $page < 0 || $page > 40) {
            show_msg("页码必须为 0-40 的 整数", 1, 0);
            return;
        }
        show_msg("请在 upload/manhua/dongxuange/m$page 查看结果.", 1,0);
        $this->manhua_xuange(0, $page, true);
    }

    private function manhua_xuange($num, $tem, $is_inc = true, $must = false) {
        $url = $this->get_xuange_url();
        $web = "dongxuange";
        $base_num = 1000000;
        $space = 5000;
        $this->manhua_run($num, $tem, $must, $url, $web, $base_num, $space, $is_inc);
    }

    /**
     * @param $num int 开始位置
     * @param $tem int 码数 类似页码
     * @param bool $must 是否一定按 开始位置 开始，默认系统自己计算
     * @param $url string 固定链接
     * @param $web string 站名 用于区分
     * @param $base_name  int 基本位置
     * @param $space int  页间隔  一页多少数量
     * @param $is_inc boolean  递增？
     */
    private function manhua_run($num = 0, $tem, $must = false, $url, $web, $base_num, $space, $is_inc = true) {
        $key = "dec_" . $tem;
        $mode = "manhua/$web";
        $num1 = mode_locks($key, $mode);
        $rate = $is_inc? 1: -1;
        $start = $rate * $tem * $space + $base_num;
        $max = $start + $rate * $space;

        if ($num1 && !$must) {
            $num = $num1;
        }

        if (empty($num)) {
            $num = empty($num1)? $start: $num1;
        }
        if ($start*$rate > $num*$rate) {
            $num = $start;
        }

        $total = ($max - $num) * $rate;
        set_time_limit(1800);
        $path = "upload/manhua/$web/m" . $tem . "/";
        $success = 0;
        for ($i = 0; $rate * $num < $rate * $max; $num += $rate) {
            mode_locks($key, $mode, $num);
            $uri = $url . $num;
            $res = curl_file($uri, $path, "File not found.", "jpg");
            $i++;
            progress_bar($i, $total);
            if (!$res) {
                mode_logs($uri, $mode, $key);
            }
            $success++;
        }
        show_msg("<br/>success: $success | total: $total | run: $i | start: $num | end: $max | " . ($num - $i * $rate), 1, 0);
    }
}

