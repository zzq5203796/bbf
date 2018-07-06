<?php

namespace app;
/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/6/13
 * Time: 9:40
 */

class Upload
{
    public function __construct() {

    }

    public function index() {
        echo_line('welcome to  new ' . CLASS_NAME . '->' . METHOD_NAME . '()');
        return 123;
    }

    public function json() {
        $data = $_POST;
        $method = $_GET['method'];
        $id = $_GET['id'];
        $method = $method? $method: 'json_data';
        $id = $id? $id: 'auto';
        $str = json_encode($data);
        write("data/$method-$id.json", $str);

        echo json_encode(['status' => 1, 'msg' => 'success']);
        return 123;
    }

    public function get() {
        $method = $_GET['method'];
        $id = $_GET['id'];
        $method = $method? $method: 'json_data';
        $id = $id? $id: 'auto';
        $data = read("data/$method-$id.json");
        $data = json_decode($data?: "[]");
        echo json_encode([
            'status' => 1,
            'msg'    => 'success',
            'for'    => "$method-$id",
            'data'   => $data
        ]);
        return 123;
    }
}