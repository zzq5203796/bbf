<?php

namespace app;

class Jingdain
{
    public function __construct() {
        //        show_msg('welcome to class ' . get_class());
    }

    public function index() {
    }

    public function lunyu() {
        $data = $this->read("lunyu");
        dump($data);
    }

    private function read($file) {
        return read("../data/jingdain/$file.txt");
    }

}
