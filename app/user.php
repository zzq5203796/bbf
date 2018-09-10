<?php

namespace app;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/7/14
 * Time: 15:09
 */

class User
{
    public function __construct() {
    }

    public function index() {
    	$data = [
    		['username', '用户名', 'text'],
    		['nickname', '昵称', 'text'],
    		['six', '性别', 'text'],
    		['age', '年龄', 'text'],
    	];
    	form($data);
    }

}
