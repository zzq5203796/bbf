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
    protected $bookId;
    protected $starTime;
    protected $temp;
    protected $setting;

    public function __construct() {
        $this->model = new CPdo();
        if(IS_CLI && empty($_GET['book'])){
            while (1) {
                 $book = cli_input("Book");
                 if(is_numeric($book)) 
                    break;
                 else{
                    show_msg("book must be a int.", 1, 0);
                 }
            }
            $_GET['book'] = $book;
            show_now();
        }

        $this->bookId = empty($_GET['book'])? 1: $_GET['book'];;
        $this->starTime = time();
        $this->temp = 0;
        $this->setting = [
            "checklock" => true
        ];
        //        $res = $this->model->query("user");
    }

    public function index() {
        if (!IS_CLI) {
            $this->helpWin();
        }
        if (IS_CLI) {
            $this->helpCli();
            $total = 20;
            for ($i = 1; $i <= $total; $i++) {
                printf("进度条: [%-50s] %d%%.%s\r", str_repeat('=',$i/$total*50), $i/$total*100, "【第 $i 章】");
                sleep(1);
            }
            echo "\n";
            echo "Done!\n";
        }
    }

    private function helpCli() {
        $html = <<<EOD
    article/book  爬虫书本
        book  int     书本
        save  0|1     记录
        p     string  路径 save 1生效
        
    article/down  生成文档 
        book :id  书本
        
    article/redo  重新格式化content


EOD;
        echo $html;

    }

    private function helpWin() {
        $fastlink = [
            ["book", "爬虫", "book=1&save=0|1&p=url"],
            ["down", "TXT", "book=1"],
        ];

        $str = "";
        foreach ($fastlink as $vo) {
            list($method, $tite, $param) = $vo;
            $str .= "<div class='fast-link'><a href='/?s=articel/$method'>$tite</a><span>$param</span></div>";
        }
        echo $str;
    }


    public function book() {
        $book_id = empty($_GET['book'])? 1: $_GET['book'];
        $info = $this->model->query("books", "*", ['id' => $book_id])[0];
        if (empty($info)) {
            echo "[book|save|p],no found.\r\n";
            return false;
        }

        $save = empty($_GET['save'])? 0: $_GET['save'];
        $last_page = $this->model->query("article", "*", ['book_id' => $book_id], "", "id desc", "", 0, 1)[0];
        $url = $last_page? $last_page['next_link']: $info['first_link'];
        if (!$save && !empty($_GET['p'])) {
            $url = $_GET['p'];
        }
        $data = [
            'url'        => $url,
            "content"    => $info['preg_content'],
            "title"      => $info['preg_title'],
            "next"       => $info['preg_next'],
            "next_top"   => $info['link'],
            "cookie_top" => "book_" . $book_id,
            "book_id"    => $book_id,
            "save"       => $save,
        ];

        if (!$save && !empty($_COOKIE[$data['cookie_top'] . "_link"])) {
            echo "last read: <a href=\"?p=" . $url . "\">" . $_COOKIE[$data['cookie_top'] . "_title"] . "</a>";
        }
        $lock = $data["cookie_top"];
        if ($save) {
            if (!empty(locks($lock))) {
                echo "[INRUN] Book is running elsewhere. [$lock]\r\n";
                return false;
            }
            IS_CLI || set_time_limit(120);
            locks($lock, json_encode($data));
        }
        $res = $this->runGet($data);
        if ($save) {
            locks($lock, 0);
        }
        $save && show_msg("total:" . count($res) );
    }

    private function runGet($data) {
        $this->temp++;
        $data_list = [];
        if ($this->temp % 50 == 0) {
            $lock = $data["cookie_top"];
            if (empty(locks($lock))) {
                return [];
            }
        }
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
        if ($res['next'] == $data['url'] || empty($res['content'])) {
            return [];
        }
        $title = $res['title'];
        $content = str_replace("'", "\'", $res['content']);
        $next = $res['next'];
        $book_id = $data['book_id'];
        $link = $data['url'];
        $sql = "insert into `article` (book_id, title, content, link, next_link) value ($book_id, '$title','$content','$link', '$next');";
        $res = $this->model->exec($sql);
        if ($res) {
            $data_list[] = $title;
            logs($title . " \n[info] $book_id | $link | $next", "book");
            if(IS_CLI){
                show_msg($title);
            }
            $data['url'] = $next;
            $res = $this->runGet($data);
            if (!empty($res)) {
                $data_list = array_merge($data_list, $res);
            }
        } else {
            dump(date("Y-m-d H:i:s"));
            dump($this->model->errorInfo());
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
        $html= mb_convert_encoding($html, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

        $pattern_list = ["title", "next", "content"];
        $matches_list = [
        ];
        foreach ($pattern_list as $item) {
            $pattern = $data[$item];
            preg_match_all($pattern, $html, $matches);
            $match = $matches[1][0];
            // $match = mb_convert_encoding($match, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');
            $matches_list[$item] = $match;
        }
        $matches_list['next'] = $data['next_top'] . $matches_list['next'];
        $matches_list['content'] = $this->doContent($matches_list['content']);
        return $matches_list;
    }

    public function redo() {
        $art_id = empty($_COOKIE["run_book1"])? 0: $_COOKIE["run_book"];
        $success = 0;
        for ($i = 0; 1; $i++) {
            $page = $this->model->query("article", "*", [], "", "id asc", "", $i + $art_id, 1);
            if (empty($page)) {
                show_msg(($art_id + $i) . get_br() . ".");
                break;
            }
            $page = $page[0];
            $content = $this->doContent($page['content']);
            $sql = "update `article` set content='$content' where id=" . $page['id'] . ";";
            $res = $this->model->exec($sql);
            show_msg((($art_id + $i) . ":" . ($res? " ok.": " fail.")));
            if ($res) {
                $success++;
                setcookie("run_book", $art_id + $i);
            }
        }

        show_msg($i);
    }

    private function doContent($content) {
        $ds = "　";

        $content = trim($content);
        $content = preg_replace("/<script.*?<\/script>/", "", $content);
        $content = str_replace(["\n", "\r", "&nbsp;", $ds], "", $content);
        $content = str_replace(["<br />", "</br>", "</ br>"], "<br/>", $content);

        $arr = explode("<br/>", $content);
        foreach ($arr as $key => $vo) {
            $item = trim($vo);
            if (empty($item)) {
                unset($arr[$key]);
                continue;
            }
            $arr[$key] = $ds . $ds . $item;
        }
        unset($vo);
        $content = implode("<br/>", $arr);
        $content = trim($content, "<br/>");
        $content = trim($content);
        return $content;
    }

    public function down() {
        $book_id = $this->bookId;
        $book = $this->txtPack($book_id, 0);
        IS_CLI || down_file($book["file"], $book['name'] . ".txt");
    }

    /**
     * 打包TXT
     * @param $book_id
     * @return mixed
     */
    private function txtPack($book_id, $reTxt = false) {
        $lock = "book_down_$book_id";
        $lock_info = locks($lock);
        if (!empty($lock_info)) {
            $lock_info = json_decode($lock_info, true);
            if ($lock_info['end'] == 0 || !$reTxt) {
                return $lock_info;
            }
        }
        $info = $this->model->query("books", "*", ['id' => $book_id])[0];
        $name = $info['title'];
        $file = "book/$name.txt";
        $book = ["file" => $file, "name" => $name, "id" => $book_id, "count" => 0, "end" => 0];
        locks($lock, json_encode($book, JSON_UNESCAPED_UNICODE));

        $list = $this->model->query("article", "*", ['book_id' => $book_id], "", "id asc");
        $onebr = "\r\n";
        $br = $onebr . $onebr;
        $str = "声明：本书为 $br $name $br 作者：**** $br 简介: ****** $br";
        foreach ($list as $vo) {
            $title = $vo["title"];
            $title = str_replace(["〇"], "零", $title);
            $content = $vo["content"];
            $content = str_replace(["<br/>", "</br></br>"], $br, $content);
            $str .= "$title$br$onebr$content$br";
        }
        write($file, $str);
        $book["count"] = count($list);
        $book["end"] = 1;
        locks($lock, json_encode($book, JSON_UNESCAPED_UNICODE));
        return $book;
    }

}