$(".ajax").on('click', function (e) {
    var url = $(this).attr("url"),
        data = {A: 1},
        data_fun = $(this).attr("ajax-data"),
        method = $(this).hasClass("ajax-post") ? 'POST' : 'GET',
        cb = $(this).attr("ajax-cb");
    if (data_fun) {
        data = eval(data_fun);
    }
    url = url ? url : '/upload/index';

    $.ajax({
        url: url,
        async: true,
        type: method,
        data: data,
        dataType: 'json',
        success: (function (cb) {
            return function (res) {
                if (res.status != 1) {
                    alert(res.msg);
                    return;
                }
                if (cb) {
                    eval(cb + "(res.data)");
                }
            }
        })(cb),
        error: function (xhr, status, error) {
            console.log(xhr, status, error);
            alert("请求失败.");
        },
        timeout: 30000
    });
    return false;
});
$(".sort").click(function () {
    var box = $(".menu-text.active").parent().parent(),
        sort_status = $(this).hasClass("desc") ? 1 : ($(this).hasClass("asc") ? 2 : 0);
    var menu = box.children();
    $(this).removeClass("desc").removeClass("asc");
    sort_status == 0 && $(this).addClass("desc");
    sort_status == 1 && $(this).addClass("asc");

    menu = menu.sort(
        function compareFunction(obj1, obj2) {
            var t1 = $(obj1).text(),
                t2 = $(obj2).text();
            if (sort_status == 0) {
                t1 = $(obj1).text();
                t2 = $(obj2).text();
            }
            if (sort_status == 1) {
                t1 = $(obj2).text();
                t2 = $(obj1).text();
            }
            if (sort_status == 2) {
                t1 = $(obj1).children(".menu-text").attr("title");
                t2 = $(obj2).children(".menu-text").attr("title");
            }
            return t1.localeCompare(t2, "zh");
        }
    );
    var str = '';
    for (var i = 0; i < menu.length; i++) {
        str += $(menu[i]).prop("outerHTML");
    }
    box.html(str);
});
$(".set-title").on('click', function (e) {
    if ($(this).attr("data-title") == undefined || $(this).attr("data-title") == '') {
        $(this).attr("data-title", $("title").html());
    }
    $("title").html($(".menu-text.active").text() + " | " + $(this).attr("data-title"));
});
$(document).keydown(function (e) {
    var code = e.keyCode;
    if (code != 40 && code != 38) {
        return true;
    }
    var node = code == 40 ? 'next' : 'prev';
    var obj = $(".menu-text.active");
    if (obj.parent()[node]().length > 0) {
        var height = obj.height(),
            scroll = obj.parents('.scroll'),
            top = obj.parent()[node]().offset().top;
        if (top < 0) {
            scroll.scrollTop(scroll.scrollTop() + top);
        }
        var jump = top + height * 2.9 - scroll.height();
        if (jump > 0) {
            scroll.scrollTop(scroll.scrollTop() + jump);
        }
        obj.parent()[node]().children(".menu-text")[0].click();
        return false;
    }
});


function setStore(name, key, value) {
    var store = getStore(name);
    var obj = {};
    obj[key] = value;
    store = Object.assign(store, obj);
    store = JSON.stringify(store); //转化为JSON字符串
    localStorage.setItem(name, store);
}

function setStore1(name, obj, is_tran) {
    var store = getStore(name);
    store = is_tran ? Object.assign(obj, store) : Object.assign(store, obj);
    store = JSON.stringify(store); //转化为JSON字符串
    localStorage.setItem(name, store);
}

function getStore(name) {
    var store = localStorage.getItem(name);
    store = store ? store : '{}';
    try {
        return JSON.parse(store);
    } catch (e) {
        console.log("not found.");
        return {};
    }
}

$(".full-window-btn,.close-window").click(function (e) {
    toggleWindow(this);
});
$("body").on('click', ".close-window", function (e) {
    toggleWindow(this);
});

function toggleWindow(obj) {
    var box = $(obj).attr("data-for");
    var box_obj = $(box ? "#" + box : '.full-window');
    if (box_obj.length == 0) {
        console.log("can't found full window.", box_obj);
        return false;
    }
    if ($(".close-window").length == 0) {
        $("body").append('<div class="close-window" data-for="' + (box ? box : '') + '"><i class="layui-icon layui-close"></i></div>');
    }

    $(".close-window").toggle();
    box_obj.toggleClass("in-full");
}