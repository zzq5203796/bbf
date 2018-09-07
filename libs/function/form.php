<?php

function form($data) {
    Form::getInstance()->set($data)->show();
}
function formAdd($data) {
    return Form::getInstance()->add($data);
}

class Form
{
    static protected $ins = null;

    protected $fields;

    final protected function __construct() {
        $this->fields = [];
    }

    static public function getInstance() {
        if (self::$ins instanceof self) {
            return self::$ins;
        }
        self::$ins = new self();
        return self::$ins;
    }

    public function set($data) {
        $this->fields = [];
        foreach ($data as $item) {
            $this->fields[] = $this->deal_filed($item);
        }
        return $this;
    }

    public function add($item) {
        $this->fields[] = $this->deal_filed($item);
        return $this;
    }

    public function get() {
        return $this->fields;
    }

    protected function deal_filed($item) {
        // key title type value opts
        if (empty($item[1]))
            $item[1] = $item[0];

        $item[1] = default_empty_value($item, 1, $item[0]);
        $item[2] = default_empty_value($item, 2, 'text');
        $item[3] = default_key_value($item, 3, '');
        $item[4] = default_key_value($item, 4, []);

        $item = [
            'name'  => $item[0],
            'title' => $item[1],
            'type'  => $item[2],
            'value' => input($item[0], $item[3]),
            'opts'  => [],
        ];
        return $item;
    }

    public function show($is_echo = true) {
        $fields = $this->fields;
        view("form", $fields);
    }
}