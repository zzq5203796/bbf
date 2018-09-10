<?php

/**
 * a = for action
 * id = user ID
 * is_ajax = is return ajax
 * extra = undo, will extra read file
 */
$action = $_GET['a'];
$auto_tree = [
    'phpinfo',
    'server'
];
foreach ($auto_tree as $key => $vo) {
    if ($action == $vo) {
        $vo();
        return;
    }
}
$iframe_url = empty($_GET['a'])? '/?a=phpinfo': $_GET['a'];
$host = get_url_info("name") . "/";

$menu = json_decode(file_get_contents('../data/menu.json'), true);
$menu_t = file_get_contents('../menu.json');
$menu_t = empty($menu_t)? ['menu' => [], 'dir' => []]: json_decode($menu_t, true);
$tree = $menu['menu'];
$dirs = $menu['dir'];

foreach ($menu_t['menu'] as $vo) {
    $tree[]=$vo;
}

foreach ($menu_t['dir'] as $key => $vo) {
    $name = is_numeric($key)? $vo: $key;
    foreach ($dirs as $k1 => $v1) {
        $name1 = is_numeric($k1)? $v1: $k1;
        if(empty($dirs[$name])){
            $dirs[$name] = $vo;
        }
    }
}

foreach ($menu['dir'] as $key => $value) {
    if ($action == $key || $action == $value) {
        $dirs = [$key => $value];
        break;
    }
}

$extra = $_GET['extra'];
$extra = $extra? $extra: [];

$extra = array_merge($extra, ['js', 'css', 'fonts', 'font', 'md', 'ss']);
readdirs($tree, $dirs);

if (IS_AJAX) {
    echo json_encode(['status' => 1, 'msg' => 'success', 'data' => $tree]);
    return;
}
$tree_str = show_tree($tree);

function show_tree($tree) {
    $str = '';
    foreach ($tree as $brech) {
        array_merge(['url' => "#", 'name' => "", 'child' => []], $brech);
        if (empty($brech['name']) || $brech['name'] == '.') {
            continue;
        }
        $has_child = !empty($brech['child']);
        $lable = $has_child? 'div': 'a';
        $href = $brech['url'];
        $title = $brech['name'] . ($brech['length'] > 0? "(" . $brech['length'] . ")": '');
        $class = $brech['hide'] || $brech['length'] > 8 || $brech['all_length'] > 16? 'hide': '';
        $next = $has_child? "<ul class='menu-next $class'>" . show_tree($brech['child']) . "</ul>": '';
        $icon = $has_child? "<i class='icon icon-cir' ></i>": "<i class='icon icon-link' style='position: relative;'><i style='width: 22px;height: 29px;display: block;position: absolute;top: -8px;left: -5px;'></i></i>";

        $str .= "<li class='menu-node'><$lable href='$href' target=\"myIframe\" class='menu-text' title='$title " . $brech['all_length'] . "' onclick='menuToggle(this);' data-length='" . $brech['length'] . "' data-alllength='" . $brech['all_length'] . "'>$icon$title</$lable>$next</li>";
    }
    return $str;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ZZQ BIG Home</title>
    <link rel="stylesheet" href="/css/bbf.css" media="all">
    <link rel="stylesheet" href="/css/menu.css" media="all">
    <style>
        html, body {
            width: auto;
            height: auto;
            margin: 0;
            background: #f8f8f8;
        }

        .div {
            width: 100%;
            padding: 0 5px;
            height: 54px;
            overflow: hidden;
            background: #f0f0f0;
            color: #555;
            font-size: 20px;
            font-weight: bolder;
            box-sizing: border-box;
        }

        .iframe-lable {
            height: 24px;
            overflow: hidden;
            border-bottom: 4px solid #b3b3b3;
        }

        .left-box {
            width: 208px;
        }

        .menu-level {
            width: 200px;
        }

        .main_nav {
            height: 1px;
        }

        .main_left {
            width: 1px;
            vertical-align: top;
        }

        .main_right_nav {
            height: 1px;
            background: #ecf0f5;
            padding: 0 0px;
            padding-top: 3px;
        }

        .main_right_nav .iframe-lable {
            padding: 0 10px 0 0;
        }

        #loader {
            display: block;
            position: relative;
            top: 0px;
            right: 4px;
            z-index: +1;
            width: 16px;
            height: 16px;
            border: 2px dashed #adadad;
            border-radius: 50%;
            text-align: center;
            line-height: 17px;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
        }

        .main-right-iframe, .main_right_nav {
            background: #ecf0f5;
            padding-left: 10px;
        }

        .hide {
            display: none;
        }
    </style>
</head>
<body>
<div class="bbf-box-full">
    <table border="0" class="bbf-box-full" style="position: fixed; top: 0;left: 0;z-index: 100;">
        <tr class="main_nav_td hide">
            <td colspan="2" class="main_nav">
                <div class="div">
                    <p>
                        hello welcome to 8081
                    </p>
                    <?php
                    echo "document root.  < index.php >";
                    ?>
                    <a href="/public/index.php"> Here We Go!</a>
                </div>
            </td>
        </tr>

        <tr>
            <td rowspan="2" class="main_left">
                <div class="left-box bbf-box-full scroll">
                    <div class="menu-level padded">
                        <?php echo $tree_str; ?>
                        <li class="menu-node">
                            <a href="/template/menu.html" target="_blank" class="menu-text" title="menu.html"
                               data-length="0" data-alllength="0">
                                <i class="icon icon-link" style="position: relative;"><i
                                            style="width: 22px;height: 29px;display: block;position: absolute;top: -8px;left: -5px;"></i></i>
                                完整版
                            </a>
                        </li>
                    </div>
                </div>
            </td>
            <td class="main_right_nav">
                <div class="iframe-lable">
                    <a href="#" target="myIframe" style="color: #555;">首页</a>
                    <span> > </span>
                    <a id="main_right_nav_a_2" href="<?php echo $iframe_url; ?>" target="myIframe"
                       style="color: #565;"><?php echo $iframe_url; ?></a>

                    <div style="float: right;">
                        <div class="button get-title"></div>
                        <div class="progress-bar">
                            <div class="bar"></div>
                        </div>
                        <div class="button set-title">title</div>
                        <div class="button sort">sort</div>
                        <div class="button full-window-btn">full</div>

                        <div class="ajax button"
                             url="/upload/get?method=menu&id=<?php echo $_GET['id']; ?>"
                             ajax-data="" ajax-cb="pullCb">pull
                        </div>
                        <div class="ajax ajax-post button"
                             url="/upload/json?method=menu&id=<?php echo $_GET['id']; ?>"
                             ajax-data="storeData()">push
                        </div>
                        <span style="float: right" id="loader" title="玩命加载中......"></span>
                        <input placeholder="重命名" id="re_name" name="re_name" value=""
                               style="float: right; border: 0; height: auto; margin-right: 10px; padding: 2px 5px; font-size: 16px;"/>
                    </div>
                </div>
            </td>
        </tr>
        <!--  frame  -->
        <tr>
            <td class="main-right-iframe" valign="top">
                <div style="height: 100%; width: 100%; display: table;" class="full-window">
                    <iframe id="myIframe" onload="onloadFrame()" name="myIframe"
                            src="<?php echo $iframe_url; ?>"></iframe>
                </div>
            </td>
        </tr>
    </table>
</div>
<script src="/js/libs/jquery.min.js"></script>
<script src="/js/common.js"></script>
</body>
<script>
    var checkobj;

    function onloadFrame() {
        var $mainFrame = $('#myIframe');
        $mainFrame.contents().attr("title");
        $(".get-title").text($mainFrame.contents().attr("title"));
    }

    $(".get-title").click(function () {
        onloadFrame();
    });

    function menuToggle(obj) {
        checkobj = obj;
        var next = obj.nextElementSibling;
        if (next) {
            $(next).toggle("fast");
        }
    }

    $(".menu-level").on('click', '.menu-node>.menu-text', function (e) {
        e.stopPropagation();
        if ($(this).attr('disabled') == 'disabled') {
            return false;
        }
        if ($(this).next(".menu-next").length > 0) {
            return false;
        }
        $("#main_right_nav_a_2").text($(this).text());
        $(".get-title").text("loading...");
        $("#main_right_nav_a_2").attr('href', $(this).attr('href'));
        $(".menu-node>.menu-text").removeClass('active');
        $(this).addClass('active');
        progressLoading(0);
    });
    $("#re_name").change(function () {
        var text = $(this).val();
        var obj = $(".menu-node>.menu-text.active");
        var title = obj.attr('title');
        text = text == '' ? title : text;
        obj.html(obj.children('i').prop("outerHTML") + text);
        $("#main_right_nav_a_2").text(text);
        setStore('re_name', title, text);
    });
    $(".menu-level").on('click', '.menu-node>.menu-text>i', function (e) {
        // e.stopPropagation();
        $(this).parent().attr("disabled", true);
        $(this).toggleClass("icon-blue");
        setStore('link_check', $(this).parent().attr('title'), $(this).hasClass("icon-blue"));
        setTimeout((function (obj) {
            return function () {
                obj.attr("disabled", false);
            }
        })($(this).parent()), 50);
        return false;
    });


    function pullCb(data) {
        console.log(data);
        for (var i in data) {
            setStore1(i, data[i], 1);
        }
        storeAuto();
    }

    auto();

    function auto() {
        $("[href=\"<?php echo $iframe_url; ?>\"").addClass('active');
        storeAuto();
    }

    function storeAuto() {
        var old = getStore('re_name');
        var obj;
        for (var i in old) {
            obj = $("[title='" + i + "']");
            obj.html(obj.children('i').prop("outerHTML") + old[i]);
        }
        old = getStore('link_check');
        for (var i in old) {
            obj = $("[title='" + i + "']").children('i');
            old[i] && obj.addClass('icon-blue');
        }
    }

    function storeData() {
        return {re_name: getStore('re_name'), link_check: getStore('link_check')};
    }
</script>
</html>
