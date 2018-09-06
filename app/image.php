<?php

namespace app;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/7/14
 * Time: 15:09
 */

class Image
{
    public function __construct() {
        //        show_msg('welcome to class ' . get_class());
    }

    public function index() {
        $url = "http://sy.5173.com/api/Game/GetBuyGameList";
        $path = "upload/down/";
        $data = file_get_contents($url);
        write("data/game.json", $data);
        $data = json_decode($data, true);
        foreach ($data as $key => $letter) {
            foreach ($letter as $vo) {
                dump($vo['Image']);
                curl_file($vo['Image'], $path);
            }
        }
        //        dump($data);
    }

    public function logo() {
        $data = ["竹", "飞", "朱", "智", "青", "哥", "快", "哒"];
        $file = "runtime/img/img_";
        create_dir(root_dir() . $file);
        foreach ($data as $key => $vo) {
            $this->buildImage($vo, $file . $key);
            show_msg("create $vo.");
        }
    }

    private function buildImage($text, $file) {
        $content = $text;
        $radius = 200;
        $rate = 0.8;
        $myImage = ImageCreate($radius, $radius); //参数为宽度和高度
        $transparentclolor = ImageColorAllocate($myImage, 0, 0, 0);
        $bgcolor = ImageColorAllocate($myImage, 93, 38, 255);
        $textcolor = ImageColorAllocate($myImage, 255, 255, 255);

        //        imagefill($myImage, 0, 0, $blue);
        imagefilledarc($myImage, $radius / 2, $radius / 2, $radius / $rate, $radius / $rate, 0, 360, $bgcolor, IMG_ARC_PIE);

        $boxrate1 = 4;
        $boxrate = 4;
        $allrate = 1 / 2 / $boxrate;
        $maxrate = (1 / $allrate - 1) * $allrate;
        $data = [
            ['x' => $allrate, 'y' => $allrate],
            ['x' => $maxrate, 'y' => $allrate],
            ['x' => $allrate, 'y' => $maxrate],
            ['x' => $maxrate, 'y' => $maxrate],
        ];
        foreach ($data as $vo) {
            imagefilledarc($myImage, $radius * $vo['x'], $radius * $vo['y'], $radius / $boxrate1, $radius / $boxrate1, 0, 360, $bgcolor, IMG_ARC_PIE);
        }

        imagecolortransparent($myImage, $transparentclolor);

        $fontsize = 140;
        $offsetx = $radius / 2 - $fontsize / 4 * (8 / 3);
        $offsety = $radius / 2 + $fontsize / 4 * (5.4 / 3);
        imagettftext($myImage, $fontsize, 0, $offsetx, $offsety, $textcolor, "../css/regular.ttf", $content);
        imagepng($myImage, "../$file.png");

        echo '<style>body{background: #999;}</style><img src="/' . $file . '.png" />' . get_br();
    }
    public function t(){
        $encrypt = "http://resources.tongyinet.com/img2/p_";
        $key = "zzq5203796";
        $str = base64_encode(openssl_encrypt($encrypt, "DES-ECB", $key, OPENSSL_RAW_DATA));
        echo $str;
    }
    public function t1(){
        $encrypt = "thaFqsfrEsz9Rp7Ld0cf0HkvZAXcd/CxZWv33T2fWUTF4XZNllMmwg==";
        $key = "zzq520379";
        $str = des_decrypt($encrypt);
        echo $str;

    }
    private function get_xuange_url(){
        $encrypt = "thaFqsfrEsz9Rp7Ld0cf0HkvZAXcd/CxZWv33T2fWUTF4XZNllMmwg==";
        $str = des_decrypt($encrypt);
        if(empty($str)){
            show_msg("please input key.");
            die();
        }
        return $str;
    }
    public function see() {
        $page = input("page");
        $temp = input("temp", 1);
        $temp = $temp>0? $temp: 1;
        form([
            ['page', '页码', 'text', '', []],
            ['temp', '间隔', 'text', $temp, []],
        ]);
        if (!is_numeric($page) || $page < 0 || $page > 999999) {
            show_msg("页码必须为 0-999999 的 整数");
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
            show_msg("页码必须为 0-40 的 整数");
            return;
        }
        show_msg("请在 upload/manhua/dongxuange/m$page 查看结果.");
        $this->manhua_xuange(0, $page, false);
    }

    private function manhua_xuange($num, $tem, $is_inc=true, $must = false) {
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
     * @param $space  int  页间隔  一页多少数量
     * @param $is_inc  boolen  递增？
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
        show_msg("<br/>success: $success | total: $total | run: $i | start: $num | end: $max | " . ($num - $i * $rate));
    }
}

