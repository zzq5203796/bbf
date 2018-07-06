<?php

namespace app;
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */

class index
{
    public function __construct() {
        echo_line('welcome to class ' . get_class());
    }

    public function index() {
        echo_line('welcome to  new ' . CLASS_NAME . '->' . METHOD_NAME.'()');
        return 123;
    }
}