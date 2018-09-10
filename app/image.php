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
        form([
            ['name', '页码', 'text', '', []],
        ]);

        $name = input("name", "竹");

        $data = str_split_utf8($name);
        $file = "runtime/img/";
        create_dir(root_dir() . $file);
        foreach ($data as $key => $vo) {
            $this->buildImage($vo, $file . $vo);
            show_msg("create $vo.",1,0);
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
        $ttf = root_dir()."/css/regular.ttf";
        imagettftext($myImage, $fontsize, 0, $offsetx, $offsety, $textcolor, $ttf, $content);
        imagepng($myImage, root_dir()."/$file.png");

        echo '<style>body{background: #999;}</style><img src="/' . $file . '.png" />' . get_br();
    }

}
function str_split_utf8($str)
{
    $split = 1;
    $array = array();
    for ($i = 0; $i < strlen($str);) {
        $value = ord($str[$i]);
        if ($value > 127) {
            if ($value >= 192 && $value <= 223) {
                $split = 2;
            } elseif ($value >= 224 && $value <= 239) {
                $split = 3;
            } elseif ($value >= 240 && $value <= 247) {
                $split = 4;
            }
        } else {
            $split = 1;
        }
        $key = null;
        for ($j = 0; $j < $split; $j++, $i++) {
            $key .= $str[$i];
        }
        array_push($array, $key);
    }
    return $array;
}
