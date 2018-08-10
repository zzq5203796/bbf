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

}