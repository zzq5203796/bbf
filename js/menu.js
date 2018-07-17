function load_menu() {
    // 点击
    $(".menu-level").on('click', '.menu-text', function (e) {
        e.stopPropagation();
        if ($(this).attr('disabled') == 'disabled') {
            return false;
        }
        if ($(this).next().length > 0) {
            $(this).next().toggle('fast');
            return false;
        } else {
            // console.log("menu go to url");
        }

        $(".get-title").text("loading...");
        $("#menu_nav_link").text($(this).text());
        $("#menu_nav_link").attr('href', $(this).attr('href'));
        $(".menu-node>.menu-text").removeClass('active');
        $(this).addClass('active');
        progressLoading(0);
    });
    // 重命名
    $("#re_name").change(function () {
        var text = $(this).val();
        var obj = $(".menu-node>.menu-text.active");
        var title = obj.attr('title');
        text = text == '' ? title : text;
        obj.html(obj.children('i').prop("outerHTML") + text);
        $("#main_right_nav_a_2").text(text);
        setStore('re_name', title, text);
    });
    // toggle check
    $(".menu-level").on('click', '.menu-node>.menu-text>i', function (e) {
        $(this).parent().attr("disabled", true);
        $(this).toggleClass("icon-blue");
        setStore('link_check', $(this).parent().attr('title'), $(this).hasClass("icon-blue"));
        setTimeout((function (obj) {
            return function () {
                obj.attr("disabled", false);
            }
        })($(this).parent()), 50);
    });

    $(".get-title").click(function () {
        onloadFrame();
    });

    // toggle menu box
    $(".menu-show-all").click(function () {
        $(".menu-text").next().show();
    });
    $(".menu-hide-all").click(function () {
        $(".menu-text").next().hide();
    });

    $(".menu-text").next().toggle();
    var left_width = $("#left").width(),
        left_status = true;
    $("#left .main").width(left_width);
    $("#left .top-tip").width(left_width - 20);
    $("#left .top-tip").height($("#left .top-tip").outerHeight() + "px");
    $("#left .main").css('top', $("#left .top-tip").outerHeight() + "px");
    $("#right .main").css('top', $("#right .top-tip").outerHeight() + "px");

    // toggle left box
    $(".toggle-box").click(function () {
        var t = 500,
            width = left_status > 0 ? 0 : left_width;
        left_status = !left_status;
        $("#left").animate({width: width}, t);
    });
}

load_menu();
auto();

function auto() {
    $(".pull").click();
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

function has_hide() {
    var obj = $(".menu-text");
    for (var i = 0; i < obj.length; i++) {
        if (i > 300)
            break
        if (!is_show($(obj[i]))) {
            return true;
        }
    }
    return false;
}

function is_show(obj) {
    return obj.next().css('display') != 'none';
}

function pullData() {
    return {a: 'RMT0714'}
}

function pullCb(data) {
    var html = getMenuHtml(data);
    $(".menu-level").html(html);
    for (var i in data) {
        setStore1(i, data[i], 1);
    }
    storeAuto();
}

function getMenuHtml(data) {
    var str = '';
    var child, icon;
    for (var i in data) {
        if (!data[i].name) {
            continue;
        }
        // console.log(data[i].child.length > 0)
        child = data[i].child.length == 0 ? '' : '<ul class="menu-next">' + getMenuHtml(data[i].child) + '</ul>';
        icon = data[i].child.length == 0 ? '<i class=\'icon icon-link\' ><i style=\'width: 22px;height: 29px;display: block;position: absolute;top: -8px;left: -5px;\'></i></i>' : '<i class=\'icon icon-cir\' ></i>';

        str += '<li class="menu-node">' +
            '<a class="menu-text" title="' + data[i].name + '" href="' + data[i].url + '" target="myIframe">' +
            icon + data[i].name +
            '</a>' +
            child +
            '</li>';
    }
    return str;
}

function onloadFrame(obj) {
    var $mainFrame = $('#myIframe');
    try {
        $mainFrame.contents().attr("title");
        var url = obj.contentWindow.location.href;
    } catch (e) {
        showMsg("not allow.");
        return;
    }
    $(".get-title").text($mainFrame.contents().attr("title"));
}