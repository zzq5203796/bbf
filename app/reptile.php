<?php

namespace app;

class Reptile
{
    public function __construct() {
    }

    public function index() {
        form([
            ['link', '链接', 'text', '', []],
            ['tag', '标签', 'text', '', []],
            ['attr', '属性', 'text', '', []],
            ['attr_value', '属性值', 'text', '', []],
        ]);
        $url = input('link', '');
        if(empty($url)){
            return;
        }
        $tag = input('tag', 'body');
        $attr = input('attr', '');
        $attr_value = input('attr_value', '');

        $pattern = m_get_tag_dom_pattern($tag, "$attr:$attr_value");

        $html = curl_get($url);
        // view("table", ['fields' => ]);
        if (!$html) {
            show_msg("未不到目标.");
            return false;
        }
        show_msg("链接:".$url, 1, 0);
        show_msg("规则:".$pattern, 1, 0);

        show_msg("已找到目标，处理结果如下.");
        preg_match_all($pattern, $html, $matches);
        $match = $matches[1][0];
        dump($match);
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

    public function rand($url, $path){
        $keys = "qwertyuiopasdfghjklzxcvbnm1234567890";
        $keyLen = 26;
        $maxLength = 6;
        $res = false;
        $mode = "manhua";
        $key = "char";
        $data = mode_locks($key, $mode);
        $start = 0;
        if(!empty($data)){
            $data = json_decode($data, true);
            if($data['url'] == $url){
                $start = $data['num'];
            }
        }
        for($k=$start; $k < 1000000; $k++){
            $str = '';
            $num = $k;
            for($i=0; $i < $maxLength; $i++){
                $str .= $keys[$num%$keyLen];
                $num = intval($num/$keyLen);
            }

            $uri = $url."_".$str;
            $res = curl_file($uri, $path, "File not found.", "jpg");


            mode_locks($key, $mode, json_encode(['url' => $url, 'num' => $k]));
            if($k%100 == 0){
                progress_bar($k, 1000000, [
                    'msg' => substr($uri, 36) . " - " . ($res?'ok':'fail'), 
                    'info' => "child: " . $k
                ]);
            }

            if($res){
                break;
            }

            if($num > 0){
                break;
            }
        }

        progress_bar(1, 100, [
            'msg' => substr($uri, 36) . " - " . ($res?'ok':'fail'), 
            'info' => "child: " . $i
        ]);
        return $res;
    }

    public function md5(){
        // http://resources.tongyinet.com/img2/p_1121593_549610
        //p_1121593_549610
        $str = "p_113347";
        show_msg(md5($str));
    }
    public function test(){
        // http://resources.tongyinet.com/static/serimgs/comics/6367/vol/f5/84ef617f81e737227359ea5722a21a.jpg
        $strs = "84ef617f81e737227359ea5722a21a";

        set_time_limit(3600);

        $max = 10000000;
        $pushTemp = 10000;
        $maxLength = 6;
        $startLength = 1;
        $top = "";
        for($k=$startLength; $k <= $maxLength; $k++){
            $max = pow(10, $k);
            for ($i=0; $i < $max; $i++) {
                $str = substr($max+$i, 1);
                $c = md5($top . $str);

                $opt = ['id' => 2, 'info' => "M: $max | D: $k | $str | $i" ];
                if(strpos($c, $strs)){
                    $opt['msg'] = "D：$k | $str | $c";
                    progress_bar($i, $max, $opt);
                    continue;
                }elseif($i%$pushTemp == 0){
                    progress_bar($i, $max, $opt);
                }
            }
        }
    }


    public function seetest() {
        show_icon('画'); //1121315
        $page = input("page");
        $c = input("c");
        $temp = input("temp", 1);
        $temp = $temp > 0? $temp: 1;
        form([
            ['page', '页码', 'text', '', []],
            ['c', 'c', 'text', '', []],
            ['temp', '间隔', 'text', $temp, []],
        ]);
        if (!is_numeric($page) || $page < 0 || $page > 999999) {
            show_msg("页码必须为 0-999999 的 整数", 1, 0);
            return;
        }
        $num = 1000000 + $page; // 48986
        $url = $this->get_xuange_url();
        $data = [];
        $max = $temp==1? 50: 400;
        if($c){

        }
        for ($i = 0; $i < $max; $i++) {
            $uri = $url . $num;
            $data[] = [
                'url' => $uri,
                'num' => substr($num,0, 3). " " . substr($num,3),
                'i' => $i
            ];
            $num += $temp;
        }
        view('imgbox', $data);
    }

    public function see() {
        show_icon('画'); //1121315
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
        $num = 1000000 + $page; // 48986
        $url = $this->get_xuange_url();
        $data = [];
        $max = $temp==1? 50: 400;
        for ($i = 0; $i < $max; $i++) {
            $uri = $url . $num;
            $data[] = [
                'url' => $uri,
                'num' => substr($num,0, 3). " " . substr($num,3),
                'i' => $i
            ];
            $num += $temp;
        }
        view('imgbox', $data);
    }

    public function manhua() {
        show_icon('漫');
        form([
            ['page', '页码', 'text', '', []],
            ['btn', '看画', 'btn', '/reptile/see', []],
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
        set_time_limit(40000);
        $path = "upload/manhua/$web/m" . $tem . "/";
        $success = 0;
        for ($i = 0; $rate * $num < $rate * $max; $num += $rate) {
            mode_locks($key, $mode, $num);
            $uri = $url . $num;
            $res = curl_file($uri, $path, "File not found.", "jpg");
            $res || $this->rand($uri, $path);
            $i++;
            progress_bar($i, $total, ['msg' => substr($uri, 36)." - ".($res?'ok':'fail'), 'info' => $i]);
            if (!$res) {
                mode_logs($uri, $mode, $key);
            }
            $success++;
        }
        show_msg("<br/>success: $success | total: $total | run: $i | start: $num | end: $max | " . ($num - $i * $rate), 1, 0);
    }
}

