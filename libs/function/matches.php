<?php

// 获取 链接 标题
function m_get_link($str) {
    $pattern = "/<a.*?href=[\'\"]{1}(.*?)[\'\"]{1}.*?>(.*?)<\/a>/is";
    preg_match_all($pattern, $str, $matches);
    $data = [];
    foreach ($matches[1] as $key => $vo) {
        $data[] = [
            'link'  => $vo,
            'title' => $matches[2][$key],
        ];
    }
    return $data;
}

// 获取 html body
function m_get_body($str) {
    return m_get_tag_dom($str, "body", "", true);
}

// 获取 正则截取
function m_get($str, $pattern) {
    preg_match_all($pattern, $str, $matches);
    return $matches;
}

// 获取 html 标签 《选择器》
function m_get_tag_dom($str, $tag = "div", $dom = "", $first = false) {
    $dom = m_get_dom_pattern($dom);
    $pattern = "/<$tag.*?$dom>(.*?)<\/$tag>/is";
    preg_match_all($pattern, $str, $matches);
    return $first? $matches[1][0]: $matches[1];
}

// 获取 html 首个 标签 《选择器》
function m_get_first_tag_dom($str, $tag = "div", $dom = "", $first = false) {
    return m_get_tag_dom($str, $tag, $dom, true);
}

// 获取 《选择器》 类型、正则表达式内容
function m_get_dom_pattern($dom, $pattern=true){
    $top = substr($dom, 0, 1);
    $arr = explode(":", $dom);
    $type = "";
    $pa = "[\'\"]{1}";
    if (in_array($top, [".", "#"])) {
        $type = $top = $top == "#"? "id": "class";
        $dom = "$top=$pa" . substr($dom, 1) . $pa;
    }elseif(isset($arr[1])){
        $type = $arr[0];
        $dom = "$type=$pa" . $arr[1] . $pa;
    }
    $dom !== "" && $dom.".*?";

    return $pattern? $dom: $type;
}
function m_get_tag_dom_pattern($tag, $dom=''){
    $dom = m_get_dom_pattern($dom);
    $pattern = "/<$tag.*?$dom>(.*?)<\/$tag>/is";
    return $pattern;
}

//清除字符串两边空格 等空白字符
function c_value($str) {
    $ds = "　";
    $str = trim($str);
    $str = str_replace(["&nbsp;", "<br />", "</br>", "</ br>", $ds], "", $str);

    return $str;
}

//清除字符串 script标签
function c_script($content) {
    return c_tag($content, "script");
}

//清除字符串 HTML标签
function c_tag($content, $tag) {
    $content = preg_replace("/<$tag.*?<\/$tag>/", "", $content);
    return $content;
}


// 统一换行符
function d_br($content) {
    $content = str_replace(["<br />", "</br>", "</ br>"], "<br/>", $content);
    return $content;
}