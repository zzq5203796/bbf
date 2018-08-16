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
    protected $starTime;

    public function __construct() {
        $this->model = new CPdo();
        $this->starTime = time();
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

    public function test() {
        set_time_limit(5);

        for ($i = 0; $i < 10; $i++) {
            echo date('Y-m-d H:i:s') . "n";
            //            sleep(1);
        }
        die();
    }

    public function book() {
        $book_id = empty($_GET['book'])? 1: $_GET['book'];
        $info = $this->model->query("books", "*", ['id' => $book_id])[0];
        if (empty($info)) {
            echo "no found.\r\n";
            return false;
        }

        $last_page = $this->model->query("article", "*", ['book_id' => $book_id], "", "id desc", "", 0, 1)[0];
        $url = $info['first_link'];
        $data = [
            'url'        => $url,
            "content"    => $info['preg_content'],
            "title"      => $info['preg_title'],
            "next"       => $info['preg_next'],
            "next_top"   => $info['link'],
            "cookie_top" => "book_" . $book_id,
            "book_id"    => $book_id,
        ];
        if ($last_page) {
            $data['url'] = $last_page['next_link'];
        }
        $save = empty($_GET['save'])? 0: $_GET['save'];
        $data['save'] = $save;
        $save && set_time_limit(300);
        $res = $this->ss($data);
        dump(count($res) . " 条");
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
        $new_time = time();
        //        if (($new_time - $this->starTime) > 289) {
        //            dump(date("Y-m-d H:i:s", $this->starTime));
        //            dump(date("Y-m-d H:i:s", $new_time));
        //            return [];
        //        }
        $res = $this->get($data);
        if (!$data['save']) {
            if ($res) {
                setcookie($data['cookie_top'] . "_link", $data['url']);
                setcookie($data['cookie_top'] . "_title", $res['title']);
                echo "<h1>" . $res['title'] . "</h1>";
                echo "<a href=\"?book_id=" . $data['book_id'] . "&p=" . $res['next'] . "\">" . $res['next'] . "</a>";
                echo "<div style='margin: 10px auto; width: 600px;'>" . $res['content'] . "</div>";
                echo "<a href=\"?book_id=" . $data['book_id'] . "p=" . $res['next'] . "\">下一章</a>";
            }
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
        $matches_list = [
        ];
        foreach ($pattern_list as $item) {
            $pattern = $data[$item];
            preg_match_all($pattern, $html, $matches);
            $match = $matches[1][0];
            //            $match = mb_convert_encoding($match, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
            $matches_list[$item] = $match;
        }
        $matches_list['next'] = $data['next_top'] . $matches_list['next'];
        return $matches_list;
    }

    public function dodo() {
        $art_id = empty($_COOKIE["run_book1"])? 0: $_COOKIE["run_book"];
        $success = 0;
        for ($i = 0; 1; $i++) {
            $page = $this->model->query("article", "*", [], "", "id asc", "", $i + $art_id, 1)[0];
            if (empty($page)) {
                echo ($art_id + $i) . " <br>\r\n.";
                break;
            }
            $content = $this->doContent($page['content']);
            $sql = "update `article` set content='$content' where id=" . $page['id'] . ";";
            $res = $this->model->exec($sql);
            dump((($art_id + $i) . ":" . ($res? " ok.": " fail.")));
            if ($res) {
                $success++;
                setcookie("run_book", $art_id + $i);
            }
        }

        dump($i);
    }

    private function doContent($content) {
        $content = trim($content);
        $content = preg_replace("/<script.*?<\/script>/", "", $content);
        $content = preg_replace("/&nbsp;/", "", $content);
        $arr = explode("<br/>", $content);
        $ds = "　";
//        $ds_1 = "&nbsp;";
        foreach ($arr as $key => $vo) {
            $item = trim($vo);
            $item = trim($item, $ds);
//            $item = trim($item, $ds_1);
            if (empty($item)) {
                unset($arr[$key]);
                continue;
            }
            $arr[$key] = $ds . $ds . $item;
        }
        unset($vo);
        //        dump($arr);
        $content = implode("<br/>", $arr);
        $content = trim($content, "<br/>");
        $content = trim($content);
        return $content;
    }
}