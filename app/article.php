<?php

namespace app;

use \libs\CPdo;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */
class Article
{

    protected $model;

    public function __construct() {
        $this->model = new CPdo();
        //        $res = $this->model->query("user");
    }

    public function index() {
        $url = "https://www.qu.la/book/34892/2494615.html";
        //        header("Content-Type:text/html;charset=gb2312");
        $html = curl_get($url);
        //        $html = mb_convert_encoding($html, "gb2312", "utf-8");

        //        $pattern = "/<div id=\"nr1\">(.*?)<\/div>/is";
        $pattern = "/<div id=\"content\">(.*?)<\/div>/is";
        preg_match_all($pattern, $html, $matches);
        $html = $matches[1][0];
        echo($html);
    }

    public function book1() {
        $url = "https://www.qu.la/book/34892/2494615.html";
        $url = empty($_GET['p'])? $url: $_GET['p'];
        $data = [
            'url'        => $url,
            "content"    => "/<div id=\"content\">(.*?)<\/div>/is",
            "title"      => "/<div class=\"bookname\">.*?<h1>(.*?)<\/h1>.*?<\/div>/is",
            "next"       => "/<a id=\"pager_next\" href=\"(.*?)\" target=\"_top\" class=\"next\">下一章<\/a>/is",
            "next_top"   => "https://www.qu.la/book/34892/",
            "cookie_top" => "book1",
            "book_id"    => 1,
        ];
        if (empty($_GET['p'])) {
            if (!empty($_COOKIE[$data['cookie_top'] . "_link"])) {
                echo "last read: <a href=\"?p=" . $url . "\">" . $_COOKIE[$data['cookie_top'] . "_title"] . "</a>";
            }
        }

        $save = empty($_GET['save'])? 0: $_GET['save'];
        $data['save'] = $save;
        set_time_limit(30);
        ob_start();
        $res = $this->ss($data);
        ob_end_flush();
        dump("===============");
    }

    private function ss($data) {
        $res = $this->get($data);
        if (!$data['save']) {
            return $res;
        }
        if (empty($res)) {
            return $res;
        }
        if ($res['next'] == $data['url']) {
            return [];
        }
        $title = $res['title'];
        $content = str_replace("'", "\'", $res['content']);
        $next = $res['next'];
        $book_id = $data['book_id'];
        $link = $data['url'];
        $sql = "insert into `article` (book_id, title, content, link, next_link) value ($book_id, '$title','$content','$link', '$next');";
        dump($title);
        ob_flush();
        $res = $this->model->exec($sql);
        $data_list[] = $title;
        if ($res) {
            $data['url'] = $next;
            $res = $this->ss($data);
            if (!empty($res)) {
                $data_list = array_merge($data_list, $res);
            }
        } else {
            dump($res);
        }

        return $data_list;
    }

    private function get($data) {
        $data = array_merge([
            "url"        => 'https://www.qu.la/book/34892/2494615.html',
            "header"     => 'Content-Type:text/html;charset=utf-8',
            "content"    => "/<div id=\"content\">(.*?)<\/div>/is",
            "title"      => "/<title>(.*?)<\/title>/is",
            "next"       => "/<a id=\"A3\" href=\"(.*?)\" target=\"_top\" class=\"next\">下一章</a>/",
            "next_top"   => "https://www.qu.la/book/34892/",
            "cookie_top" => "book",
            "save"       => 0,
        ], $data);
        $url = $data["url"];
        $html = curl_get($url);
        if (!$html) {
            return [];
        }
        $pattern_list = ["title", "next", "content"];
        $matches_list = [];
        foreach ($pattern_list as $item) {
            $pattern = $data[$item];
            preg_match_all($pattern, $html, $matches);
            $match = $matches[1][0];
            //            $match = mb_convert_encoding($match, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
            $matches_list[$item] = $match;
        }
        setcookie($data['cookie_top'] . "_link", $url);
        setcookie($data['cookie_top'] . "_title", $matches_list['title']);
        $matches_list['next'] = $data['next_top'] . $matches_list['next'];
        //        echo "<h1>" . $matches_list['title'] . "</h1>";
        //        echo "<div>" . $matches_list['content'] . "</div>";
        //        echo "<a href=\"?p=" . $matches_list['next'] . "\">下一章</a>";
        return $matches_list;
    }


}