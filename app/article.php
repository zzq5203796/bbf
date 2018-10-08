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
        if (IS_CLI && empty($_GET['book'])) {
            while (1) {
                $book = cli_input("Book");
                if (is_numeric($book))
                    break;
                else {
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
                printf("进度条: [%-50s] %d%%.%s\r", str_repeat('=', $i / $total * 50), $i / $total * 100, "【第 $i 章】");
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

        $list = $this->model->query("books", "*", [], "", "id asc");
        $str = "";
        foreach ($list as &$vo) {
            $vo['num'] = $this->model->query("article", "count(id) as num", ['book_id' => $vo['id']], "", "id asc")[0]['num'];
            $vo['lock_book'] = $this->locks('book_' . $vo['id']);
            $vo['lock_down'] = $this->locks('book_down_' . $vo['id']);
        }

        if (IS_AJAX) {
            ajax_page('', $list);
        }
        foreach ($list as $vo) {
            $title = $vo['title'];
            $link = $vo['link'];
            $link = $vo['link'];
            $id = $vo['id'];
            $first_link = $vo['first_link'];
            $str .= "<div class='fast-link' style='padding: 4px 0;'>
            <div style='width:100px;text-align:right;display: inline-block;'>$title: </div>
            <a href='/article/book?is_check=1&book=$id' target='_blank'>检查</a> |
            <a href='/article/book?save=1&book=$id' target='_blank'>爬他</a> |
            <a href='/article/down?book=$id'>下载</a> |
            <a href='/article/book?book=$id' target='_blank'>阅读</a> |
            <a href='$link' target='_blank'>源文</a> |
            <a href='$first_link' target='_blank'>源首页</a> |
            <a href='$link' target='_blank'>$link</a>
            <span></span>
            </div>";
        }

        foreach ($fastlink as $vo) {
            list($method, $tite, $param) = $vo;
            $str .= "<div class='fast-link'><a href='/?s=articel/$method' target='_blank'>$tite</a><span>$param</span></div>";
        }
        echo $str;
    }

    public function read() {

    }

    public function search() {
        $keyword = input("keyword", "图书馆");
        $url = "https://sou.xanbhx.com/search?siteid=qula&q=$keyword";
        $html = curl_get($url);
        $pattern = "/<li>.*?(<span.*?>.*?<\/span>)+.*?<\/li>/is";

        $html = m_get_body($html);
        preg_match_all($pattern, $html, $matches);

        $data = [];
        $keys = ['type', 'title', 'new', 'author', 'click', 'update', "status"];
        $mlist = $matches[0];
        $more = [];
        foreach ($mlist as $k => $matche) {
            $span = m_get_tag_dom($matche, "span");
            $item = [];
            foreach ($span as $key => $mvo) {
                $tk = default_key_value($keys, $key, $key);

                $link = m_get_link($mvo);

                $item[$tk] = $mvo;
                if (!empty($link)) {
                    IS_AJAX && $item[$tk] = trim($link[0]['title']);
                    $item[$tk . "_link"] = trim($link[0]['link']);
                    $more[$tk . "_link"] = 1;
                }
            }
            $data[] = $item;
        }

        foreach ($data as &$vo) {
            foreach ($more as $k => $v) {
                $vo[$k] = isset($vo[$k])? trim($vo[$k]): "";
            }
        }

        if (IS_AJAX) {
            return ajax_success("查询成功", ['html' => $html, 'list' => $data]);
        } else {
            show_table($data);
        }
    }

    public function book() {
        $book_id = empty($_GET['book'])? 1: $_GET['book'];
        $is_check = empty($_GET['is_check'])? 0: $_GET['is_check'];
        $info = $this->model->query("books", "*", ['id' => $book_id])[0];
        if (empty($info)) {
            echo "[book|save|p],no found.\r\n";
            return false;
        }

        $save = empty($_GET['save'])? 0: $_GET['save'];
        $last_page = $this->model->query("article", "*", ['book_id' => $book_id], "", "id desc", "", 0, 1)[0];
        $total_page = $this->model->query("article", "count(*) as num", ['book_id' => $book_id])[0]['num'];
        $url = $last_page && !$is_check? $last_page['next_link']: $info['first_link'];
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
            "link"       => $info['link'],
            "total"       => $total_page,
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
            IS_CLI || set_time_limit(0);
            locks($lock, json_encode($data));
        }
        $res = $this->runGet($data);
        if ($save) {
            locks($lock, 0);
        }
        $save && show_msg("共成功采集total: " . count($res) . " 条.");
    }

    public function join() {
        // $data = [
        // 'title' => '/<div class="bookname">.*?<h1>(.*?)<\/h1>.*?<\/div>/is',
        // 'content' => '/<div id="content">(.*?)<\/div>/is',
        // 'next'=> '/<a id="A3" href="(.*?)" target="_top" class="next">下一章<\/a>/is'
        // ];
        // dump(json_encode($data), JSON_UNESCAPED_UNICODE);

        $a = [
            'title',
            'title_link' => 'link',
            'new_link'   => 'first_link',
        ];
        $data = inputs($a);

        $map_book = ['title' => $data['title']];
        $map_web = ['link' => get_http_host($data['link'])];

        $book = $this->model->query("books", "*", $map_book, "", "id desc", "", 0, 1)[0];
        if ($book) {
            ajax_error('书本已存在');
        }

        $webs = $this->model->query("webs", "*", $map_web, "", "id desc", "", 0, 1)[0];
        $preg = json_decode($webs['matchs'], true);
        if (empty($preg)) {
            ajax_error('暂无该网站解析方式');
        }
        $data['preg_title'] = $preg['title'];
        $data['preg_content'] = $preg['content'];
        $data['preg_next'] = $preg['next'];
        $data['header'] = $webs['header'];
        $data['preg'] = $webs['matchs'];
        $data['first_link'] = trim(get_http_host($data['link']), "/") . reset($this->catalog($data['link']))['link'];
        $data['source'] = '';
        $res = $this->model->add("books", $data);
        ajax_return_res($res);
    }

    public function delete() {
        $id = input('id');
        $res = $this->model->delete("books", ['id' => $id]);
        ajax_return_res($res);

    }

    public function checkdir() {
        $url = input('title_link');
        $data = $this->catalog($url);
        if (is_array($data) && count($data) > 0) {
            ajax_success('目录检查成功，正在检测页面', $data);
        } else {
            ajax_error('目录检查失败');
        }
    }

    public function chek() {
        $url = input('title_link');
        $data = $this->catalog($url);
        if (is_array($data) && count($data) > 0) {
            ajax_success('目录检查成功，正在检测页面', $data);
        } else {
            ajax_error('目录检查失败');
        }
    }

    public function first() {
        $url = input('title_link');
        $data = $this->catalog($url);
        dump($data);
    }

    //目录
    protected function catalog($url) {
        $html = curl_get($url);
        $html = m_get_body($html);
        $pattern = m_get_tag_dom_pattern('div', "id=\"list\"");
        preg_match_all($pattern, $html, $matches);
        $mlist = $matches[1][0];

        $pattern = "/<dd>.*?<a.*?href=\"(.*?(\d*?)(\.html)?)\".*?>(.*?)<\/a>.*?<\/dd>/is";
        preg_match_all($pattern, $mlist, $matches);

        $data = array_merge_two(['title', 'id', 'link'], $matches[4], $matches[2], $matches[1]);
        $data = array_sort($data, 'id');
        $old = '';
        $nuique_key = "id";
        $check_key = "link";
        foreach ($data as $key => $vo) {
            if ($old !== '') {
                if ($data[$old][$nuique_key] == $vo[$nuique_key]) {
                    $name = strlen($data[$old][$check_key]) > strlen($vo[$check_key])? $key: $old;
                    unset($data[$name]);
                }

            }
            $old = $key;
        }
        return $data;

    }

    private function runGet($data) {
        $this->temp++;
        $data_list = [];

        $book_id = $data['book_id'];
        $link = $data['link'];
        $has_total = $data['total'];
        $link_len = strlen($link);
        $is_save = $data['save'];
        $lock = $data["cookie_top"];
        $max = $is_save? 10000: 1;
        $wait = 0;

        if($is_save){
            $cates = $this->catalog($link);
            $max = count($cates) - $has_total;
        }
        
        for ($i = 0; $i < $max; $i++) {
            if (strlen($data['url']) < $link_len + 4) {
                show_msg("检测到【链接】结束.");
                break;
            }
            if ($i % 50 == 0 && empty(locks($lock)) && $is_save) {
                show_msg("检测到【锁】结束.");
                break;
            }

            if ($wait > 10) {
                break;
            }

            $res = $this->get($data);
            if (empty($res)) {
                $wait++;
                progress_bar($this->temp, $max, [
                    'msg' => "<a href='" . $data['url'] . "'></a>加载失败，重试 第 $wait 次"
                ]);
                continue;
            }
            $title = $res['title'];
            $next = $res['next'];
            $content = $res['content'];

            if (!$is_save) {
                if ($res) {
                    setcookie($data['cookie_top'] . "_link", $data['url']);
                    setcookie($data['cookie_top'] . "_title", $title);
                    echo "<div style='margin: 10px auto; width: 600px;'><h1>" . $res['title'] . "</h1>";
                    echo "<a href=\"?book=$book_id&p=$next\">$next</a>";
                    echo "<div>$content</div>";
                    echo "<a href=\"?book=$book_id&p=$next\">下一章</a></div>";
                }
                return $res;
            }
            if ($res['next'] == $data['url'] || empty($res['content'])) {
                show_msg("乏力，感觉不在爱了.");
                break;
            }

            $link = $data['url'];
            $res['book_id'] = $book_id;
            $res['link'] = $link;
            $res['next_link'] = $next;
            $res = $this->model->add('article', $res);


            if ($res) {
                $this->temp++;
                $msg = $title;
                $wait = 0;
                $data_list[] = $title;
                logs($title . " \n[info] $book_id | $link | $next", "book");
                if (IS_CLI) {
                    show_msg($title);
                }
                $data['url'] = $next;
            } else {
                $msg = $title . " 保存失败， 重试 $wait 次";
                $wait++;
            }
            progress_bar($this->temp, $max, [
                'msg' => $msg
            ]);
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
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

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
        $type = input('type', 'down');
        $book = $this->txtPack($book_id, 0);

        IS_CLI || $type != 'down' || down_file($book["file"], $book['name'] . ".txt");
        IS_AJAX && ajax_success('');
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
        $titlebr = $br . $onebr;
        $pagebr = $br;
        $str = "声明：本书为 $br $name $br 作者：**** $br 简介: ****** $br";
        foreach ($list as $vo) {
            $title = $this->doTitle($vo["title"]);

            $content = $vo["content"];
            $content = str_replace(["<br/>", "</br></br>"], $br, $content);
            $str .= "$title$titlebr$content$pagebr";
        }
        write($file, $str);
        $book["count"] = count($list);
        $book["end"] = 1;
        locks($lock, json_encode($book, JSON_UNESCAPED_UNICODE));
        return $book;
    }

    private function doTitle($title) {
        $title = str_replace(["〇"], "零", $title);
        $title = explode(" ", $title);
        if (count($title) == 1) {
            $title = explode(".", $title[0]);
        }
        if (is_numeric($title[0])) {
            $title[0] = "第" . $title[0] . "章";
        }
        $title = implode(" ", $title);
        return $title;
    }

    private function locks($lock, $value = null) {
        $res = locks($lock, $value);
        return ($value === null? !empty($res): $res);
    }

    /**
     * 清除锁
     */
    public function unlocks() {
        $type = input('type', 'down');
        $id = input('id', 'down');
        switch ($type) {
            case 'book':
                $name = 'book_';
                break;
            default:
                $name = "book_down_";
        }
        $name .= $id;
        $this->locks($name, 0);
        ajax_success('');
    }

    public function test() {
        $max = 100;
        $num = 0;
        for ($i = 0; $i < $max && $num < $max; $i++) {
            $num += rand(1, 20);
            progress_bar($num, $max);
            sleep(1);
        }
    }

    public function allrun() {

        $lock = "allrun";
        locks($lock, 1);
        ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
        set_time_limit(0); // 执行时间为无限制，php默认执行时间是30秒，可以让程序无限制的执行下去
        $interval = 5; // 每隔30秒运行一次
        $status = 1;
        $i = 0;
        while ($status) {

            if (empty(locks($lock))) {
                break;
            }

            logs($i, $lock);
            sleep($interval);
            $i++;
            echo "123";
            ob_flush();//把数据从PHP的缓冲（buffer）中释放出来。
            flush(); //把不在缓（buffer）中的或者说是被释放出来的数据发送到浏览器。
        }
        locks($lock, 0);
        exit();
    }

    public function web() {
        if (IS_POST) {
            $subtype = $_POST['subtype'];
            if ($subtype == 'delete') {
                $res = $this->model->delete("webs", $_POST);
            } elseif ($subtype == 'add') {
                $res = $this->model->add("webs", $_POST);
            } else {
                $res = $this->model->update("webs", $_POST);
            }
            echo $res? "操作成功.": "操作失败.";
        }
        $fields = [
            ['id', 'ID', 'text', ['width' => "40px"]],
            ['title', '标题', 'text'],
            ['link', '链接', 'text'],
            ['search_link', '搜索链接', 'text'],
            ['preg', '表达式', 'textarea'],
            ['header', '头部', 'textarea'],
            // ['target', '新窗口', 'redeio'],
            // ['target', '新窗口', 'redeio'],
        ];
        $data = $this->model->query("webs");
        view("table-form", ['fields' => $fields, 'data' => $data]);
    }
}
