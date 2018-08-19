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
        show_msg('welcome to class ' . get_class());
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

        echo '<style>body{background: #999;}</style><img src="/' . $file . '.png" />'.get_br() ;
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
