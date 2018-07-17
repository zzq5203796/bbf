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
        echo_line('welcome to class ' . get_class());
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
                down_file($vo['Image'], $path);
            }
        }
//        dump($data);
    }
}


/**
 * @param $file_url
 * @param $save_to
 */
function down_file($file_url, $save_to) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_URL, $file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file_content = curl_exec($ch);
    curl_close($ch);
    $filename = pathinfo($file_url, PATHINFO_BASENAME);
    $downloaded_file = fopen($save_to . $filename, 'w');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);
}
