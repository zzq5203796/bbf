<?php

namespace app;
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */

class Test
{

    public function index() {
        echo '["status"=>1, "msg" =>  "ok"]';
    }

    public function wait() {
        sleep(10);
        echo '["status"=>1, "msg" =>  "ok"]';
    }

    public function inrun() {
        ignore_user_abort();
        set_time_limit(20);
        $interval = 3;
        $max = 30;
        do {
            write("inrun", show_now(0));
            sleep($interval); // 等待
            if ($max-- < 0)
                break;
        } while (true);
    }

    public function s1() {

    }
    public function __destruct() {
        dump(123);
    }

    public function icon() {
        $str = $this->ss();
        $data = explode("\r\n", $str);
        $pa = "/\.icon-(.*?):before.*?\"(.*?)\"/";
        preg_match_all($pa, $str, $ma);
        dump($ma);
        $icons = $ma[1];
        $chars = $ma[2];
        $res = "";
        foreach ($icons as $key => $vo) {
            $char = $chars[$key];
            $res .= " <div class=\"item\">
        <i class=\"iconfont icon-$vo\"></i>
        <p>icon-$vo</p>
        <p>$char</p>
        </div>";
        }
        dump($res);
    }

    public function ss() {
        return '
    .icon-cuowu:before { content: "\e641"; }

    .icon-erweima:before { content: "\e642"; }

    .icon-lajixiang:before { content: "\e645"; }

    .icon-lianjie:before { content: "\e646"; }

    .icon-shangfan:before { content: "\e64a"; }

    .icon-shezhi:before { content: "\e64b"; }

    .icon-shouhuodizhi:before { content: "\e64e"; }

    .icon-shouye:before { content: "\e64f"; }

    .icon-shuaxin:before { content: "\e650"; }

    .icon-sousuo:before { content: "\e651"; }

    .icon-suo:before { content: "\e652"; }

    .icon-tishi:before { content: "\e653"; }

    .icon-wancheng:before { content: "\e654"; }

    .icon-wodedingdan:before { content: "\e655"; }

    .icon-xiafan:before { content: "\e65b"; }

    .icon-xiala:before { content: "\e65c"; }

    .icon-xiangshangjiantou:before { content: "\e65d"; }

    .icon-xiangxiajiantou:before { content: "\e65e"; }

    .icon-xiangyoujiantou:before { content: "\e65f"; }

    .icon-xiangzuojiantou:before { content: "\e660"; }

    .icon-yijianfankui:before { content: "\e662"; }

    .icon-zhengque:before { content: "\e664"; }

    .icon-xiaoxizhongxin:before { content: "\e665"; }

    .icon-new:before { content: "\e667"; }

    .icon-liwu:before { content: "\e685"; }

    .icon-fanhui:before { content: "\f0343"; }

    .icon-gengduo:before { content: "\f0344"; }

    .icon-shangjiantou:before { content: "\f034d"; }

    .icon-xiajiantou:before { content: "\f034e"; }

    .icon-youjiantou:before { content: "\f034f"; }

    .icon-zuojiantou:before { content: "\f0350"; }

    .icon-accessory:before { content: "\e6dd"; }

    .icon-activity:before { content: "\e6de"; }

    .icon-activity_fill:before { content: "\e6df"; }

    .icon-add:before { content: "\e6e0"; }

    .icon-addition_fill:before { content: "\e6e1"; }

    .icon-addition:before { content: "\e6e2"; }

    .icon-addpeople_fill:before { content: "\e6e3"; }

    .icon-addpeople:before { content: "\e6e4"; }

    .icon-addressbook_fill:before { content: "\e6e5"; }

    .icon-addressbook:before { content: "\e6e6"; }

    .icon-barrage_fill:before { content: "\e6e7"; }

    .icon-barrage:before { content: "\e6e8"; }

    .icon-businesscard_fill:before { content: "\e6e9"; }

    .icon-businesscard:before { content: "\e6ea"; }

    .icon-clock_fill:before { content: "\e6eb"; }

    .icon-clock:before { content: "\e6ec"; }

    .icon-close:before { content: "\e6ed"; }

    .icon-collection_fill:before { content: "\e6ee"; }

    .icon-collection:before { content: "\e6ef"; }

    .icon-delete_fill:before { content: "\e6f2"; }

    .icon-delete:before { content: "\e6f3"; }

    .icon-document:before { content: "\e6f4"; }

    .icon-document_fill:before { content: "\e6f5"; }

    .icon-editor:before { content: "\e6f6"; }

    .icon-empty:before { content: "\e6f7"; }

    .icon-empty_fill:before { content: "\e6f8"; }

    .icon-enter:before { content: "\e6f9"; }

    .icon-enterinto:before { content: "\e6fa"; }

    .icon-enterinto_fill:before { content: "\e6fb"; }

    .icon-flag_fill:before { content: "\e6fc"; }

    .icon-flag:before { content: "\e6fd"; }

    .icon-fullscreen:before { content: "\e6fe"; }

    .icon-group:before { content: "\e6ff"; }

    .icon-group_fill:before { content: "\e700"; }

    .icon-homepage_fill:before { content: "\e702"; }

    .icon-homepage:before { content: "\e703"; }

    .icon-like_fill:before { content: "\e707"; }

    .icon-like:before { content: "\e708"; }

    .icon-lock_fill:before { content: "\e709"; }

    .icon-lock:before { content: "\e70a"; }

    .icon-mail:before { content: "\e70b"; }

    .icon-mail_fill:before { content: "\e70c"; }

    .icon-manage_fill:before { content: "\e70d"; }

    .icon-manage:before { content: "\e70e"; }

    .icon-message:before { content: "\e70f"; }

    .icon-message_fill:before { content: "\e710"; }

    .icon-mine:before { content: "\e711"; }

    .icon-mine_fill:before { content: "\e712"; }

    .icon-more:before { content: "\e713"; }

    .icon-narrow:before { content: "\e714"; }

    .icon-offline_fill:before { content: "\e715"; }

    .icon-offline:before { content: "\e716"; }

    .icon-order_fill:before { content: "\e717"; }

    .icon-order:before { content: "\e718"; }

    .icon-other:before { content: "\e719"; }

    .icon-picture_fill:before { content: "\e71a"; }

    .icon-picture:before { content: "\e71b"; }

    .icon-play:before { content: "\e71c"; }

    .icon-play_fill:before { content: "\e71d"; }

    .icon-praise_fill:before { content: "\e71e"; }

    .icon-praise:before { content: "\e71f"; }

    .icon-qrcode_fill:before { content: "\e720"; }

    .icon-qrcode:before { content: "\e721"; }

    .icon-refresh:before { content: "\e722"; }

    .icon-remind_fill:before { content: "\e723"; }

    .icon-remind:before { content: "\e724"; }

    .icon-return:before { content: "\e725"; }

    .icon-right:before { content: "\e726"; }

    .icon-scan:before { content: "\e727"; }

    .icon-send:before { content: "\e728"; }

    .icon-setup_fill:before { content: "\e729"; }

    .icon-setup:before { content: "\e72a"; }

    .icon-share_fill:before { content: "\e72b"; }

    .icon-share:before { content: "\e72c"; }

    .icon-smallscreen_fill:before { content: "\e72d"; }

    .icon-smallscreen:before { content: "\e72e"; }

    .icon-success_fill:before { content: "\e72f"; }

    .icon-success:before { content: "\e730"; }

    .icon-switch:before { content: "\e731"; }

    .icon-systemprompt_fill:before { content: "\e732"; }

    .icon-systemprompt:before { content: "\e733"; }

    .icon-tailor:before { content: "\e734"; }

    .icon-text:before { content: "\e735"; }

    .icon-time_fill:before { content: "\e736"; }

    .icon-time:before { content: "\e737"; }

    .icon-translation_fill:before { content: "\e738"; }

    .icon-translation:before { content: "\e739"; }

    .icon-trash:before { content: "\e73a"; }

    .icon-trash_fill:before { content: "\e73b"; }

    .icon-undo:before { content: "\e73c"; }

    .icon-unlock_fill:before { content: "\e73d"; }

    .icon-unlock:before { content: "\e73e"; }

    .icon-warning_fill:before { content: "\e73f"; }

    .icon-warning:before { content: "\e740"; }

    .icon-publishgoods_fill:before { content: "\e746"; }

    .icon-shop_fill:before { content: "\e747"; }

    .icon-transaction_fill:before { content: "\e748"; }

    .icon-packup:before { content: "\e749"; }

    .icon-unfold:before { content: "\e74a"; }

    .icon-financial_fill:before { content: "\e74b"; }';
    }

}