<?php

function form($data) {
    Form::getInstance()->set($data)->show();
}

class Form
{
    static protected $ins = null;

    protected $fields;

    final protected function __construct() {

    }

    static public function getInstance() {
        if (self::$ins instanceof self) {
            return self::$ins;
        }
        self::$ins = new self();
        return self::$ins;
    }

    public function set($data) {
        foreach ($data as $item) {
            $this->fields[] = $this->deal_filed($item);
        }
        return $this;
    }

    public function add() {

    }

    public function get() {

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
        $html = '';
        $fields = $this->fields;
        foreach ($fields as $item) {
            $t_html = '';
            switch ($item['type']) {
                case 's':
                    break;
                default:
                    $t_html = $item['title'] . ": <input name='" . $item['name'] . "' type='text' value='" . $item['value'] . "'/>";
                    break;
            }
            $html .= "<div class='form-item'>$t_html</div>";
        }
        $html .= "<div style='clear:both'></div><div class='form-item'style='float: right;'><input type='submit' value='提交'></div>";

        $html = "<style>.form-item{padding: 2px 8px 2px 4px;  float: left;}.form-item input{padding: 2px 4px;}</style><form action='' method='get'>$html</form>";
        if ($is_echo)
            echo $html;
        return $html;
    }
}