<?php

namespace app;
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */

class Article
{

    public function index() {
        $url = "https://m.88dus.com/book/44115-13843246/";
        header("Content-Type:text/html;charset=gb2312");
        $html = curl_get($url);
//        $html = mb_convert_encoding($html, "gb2312", "utf-8");

        $pattern = "/<div id=\"nr1\">(.*?)<\/div>/is";
        preg_match_all($pattern, $html, $matches);
        $html = $matches[1][0];
        dump($html);
    }

}