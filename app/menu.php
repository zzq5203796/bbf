<?php

namespace app;

/**
 * Created by PhpStorm.
 * User: fontke01
 * Date: 2018/7/14
 * Time: 15:09
 */

class Menu
{
    protected $file = "../data/test.json";
    public function __construct() {
    }

    public function index() {
        if(!empty(input('tree'))){
            $data = $this->postData(input('tree'));
            $tree = $this->toTree($data);
            write($this->file, json_encode($tree));
        }

        $menu = json_decode(read($this->file), true);
        $fields = [
            ['name', '标题', 'text'],
            ['url', '链接', 'text']
            // ['target', '新窗口', 'redeio'],
            // ['target', '新窗口', 'redeio'],
        ];
        $data = $menu;
        $data = $this->toList($data);
        view("tree-form", ["fields" => $fields, 'data' => $data]);
    }

    public function copy(){
        $menu = get_menu();
        $data = $menu;
        $data = $this->toList($data);
        $t = $this->toTree($data);
        write($this->file, json_encode($t));
    }

    private function toList($tree, $pid = 0, $level=1, $reset = false){
        $data = [];
        $i = $pid+1;
        foreach ($tree as $value) {
            $value['id'] = $reset? $i: default_empty_value($value, 'id', $i);
            $value['level'] = $level;
            $value['pid'] = $pid;
            $child = default_empty_value($value, 'child', []);
            unset($value['child']);
            $data[] = $value;
            $child = $this->toList($child, $i, $level+1);
            $data = array_merge($data, $child);
            $i += count($child) + 1;
        }
        return $data;
    } 
    private function toTree($data){
        $tree = [];
        foreach ($data as $vo) {
            $this->setPrev($data, $vo);
        }
        foreach ($data as $vo) {
            if($vo['pid'] == 0){
                $tree[] = $vo;
            }
        }
        return $tree;
    } 
    private function setPrev(&$data, $item){
        if($item['pid'] == 0){
            return ;
        }

        foreach ($data as &$vo) {
            if($vo['id'] == $item['pid']){
                $vo['child'][$item['id']] = $item;;
                break;
            }
            $this->setPrev($data, $vo);
        }
        $this->setPrev($data, $vo);
    }
    private function postData($post){
        $data = [];
        foreach ($post as $key => $item) {
            foreach ($item as $num => $vo) {
                $data[$num][$key] = $vo;
            }
        }
        return $data;
    }

}
