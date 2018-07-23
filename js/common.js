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
                    showMsg(res.msg);
                    return;
                }
                showMsg(res.msg);
                if (cb) {
                    eval(cb + "(res.data)");
                }
            }
        })(cb),
        error: function (xhr, status, error) {
            console.log(xhr, status, error);
            showMsg("require error.");
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
$(".reload").on('click', function (e) {
    reloadDom(this);
});

$(".change-back").on('click', function (e) {
    $(this).toggleClass("on");
    if ($(this).hasClass("on")) {
        $("#con").hide();
        ballStop();
    } else {
        $("#con").show();
        ballStart();
    }
});

function reloadDom(obj) {
    if (obj == 'f5') {
        try {
            document.myIframe.location.reload();
            showMsg(lang.get("iframe reload"));
        } catch (e) {
            showMsg(lang.get("disable F5"));
        }
        return false;
    }
    if ($(obj).attr("data-for")) {
    } else {
        window.location.reload(true);
        // setTimeout(function () {
        // }, 100);
    }
}

var startTime = Math.ceil(new Date().getTime() / 1000), //单位秒
    getDuration = function () {
        var time = '',
            hours = 0,
            minutes = 0,
            seconds = 0,
            endTime = Math.ceil(new Date().getTime() / 1000),
            duration = endTime - startTime;

        hours = Math.floor(duration / 3600); //停留小时数
        minutes = Math.floor(duration % 3600 / 60); //停留分钟数
        seconds = Math.floor(duration % 3600 % 60); //停留秒数

        time = (hours < 10 ? '0' + hours : hours) + ':' + (minutes < 10 ? '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds : seconds);

        return time;
    };

window.onbeforeunload = function (event) {
    var duration = getDuration();
    // showMsg(lang.get("wait:") + duration);
    showMsg('clear cache success.');
    $.ajax({
        url: '/zip/menu',
        dataType: "json",
        async: false,
        data: "",
        beforeSend: function () {
        },
        success: function (data) {
            // showMsg('clear cache success.');
        }
    });
    // return true;
};
$(document).keydown(function (e) {
    var code = e.keyCode;
    if (!group_key(e)) {
        return false;
    }
    if (code != 40 && code != 38) { //38-40 left up tight down
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

function group_key(e) {
    var code = e.keyCode;
    this.ctrl = {
        shiftKey: 16,
        ctrlKey: 17,
        altKey: 18,
    };
    this.checkKey = function (data) {
        console.log(code);
        for (var i in data) {
            if (e[i] && code == data[i]) {
                return true;
            }
        }
        return false;
    };
    this.checkKey(this.ctrl);
    if ((e.ctrlKey && code == 17) || (e.altKey && code == 18) || (e.shiftKey && code == 16)) {
        return true;
    }
    $(".keycontent").children().length > 35 && $(".keycontent").children().eq(1).remove();
    $(".keycontent").append("<p>CTRL: " + e.ctrlKey + "; &nbsp;&nbsp;&nbsp;&nbsp; ALT:  " + e.altKey + "; &nbsp;&nbsp;&nbsp;&nbsp;  Code: " + code + "</p>");
    if (code == 116) {
        reloadDom("f5");
        return false;
    }
    return true;
}

var progressLoadingTimeOut;

function progressLoading(num) {
    clearTimeout(progressLoadingTimeOut);
    progressLoadingTimeOut = setTimeout(function () {
        num = num ? num : 0;
        num = num > 100 ? 100 : num;
        progressBar(num);
        if (num < 100) {
            // num += 1;
            num += Math.ceil(Math.random() * 5) + 1;
            progressLoading(num);
        }
    }, 9);
}

function progressBar(num) {
    $(".progress-bar .bar").width(num + "%").attr('data-afterContent', num + "%");
}

function setStore(name, key, value) {
    return store.setKey(name, key, value);
}

function setStore1(name, obj, is_tran) {
    store.merge(name, obj, is_tran);
}

function getStore(name) {
    return store.get(name);
}

store = (function () {
    function setKey(name, key, value) {
        var store = getStore(name);
        if (typeof (key) == "Object") {
            store = Object.assign(store, key);
        } else {
            store[key] = value;
        }
        set(name, store);
    }

    function push(name, obj) {
        var store = getStore(name);
        store.push(obj);
        set(name, store);
    }

    function get(name) {
        var store = localStorage.getItem(name);
        store = store ? store : '{}';
        try {
            return JSON.parse(store);
        } catch (e) {
            showMsg(lang.get("not found STORE")+" [" + name + "]");
            return {};
        }
    }

    function set(name, store) {
        store = JSON.stringify(store); //转化为JSON字符串
        localStorage.setItem(name, store);
    }

    function merge(name, obj, is_tran) {
        var store = getStore(name);
        store = is_tran ? Object.assign(obj, store) : Object.assign(store, obj);

        set(name, store);
    }

    this.set = set;
    this.setKey = setKey;
    this.push = push;
    this.merge = merge;
    this.get = get;
    return this;
})();

(fullWindow = function () {
    var fullData = {
        autoBox: '.full-window',
        class: 'in-full',
        attr: 'data-for',
        fullBtn: '.full-window-btn',
        close: '.close-window',
        closeClass: 'close-window',
    };

    $(fullData.fullBtn).on('click', function (e) {
        toggleWindow(this);
    });
    $("body").on('click', fullData.close, function (e) {
        toggleWindow(this);
    });

    function toggleWindow(obj) {
        var box = $(obj).attr(fullData.attr);
        var box_obj = $(box ? "#" + box : fullData.autoBox);
        if (box_obj.length == 0) {
            showMsg(lang.get("can't found full window.") + box);
            return false;
        }
        if ($(fullData.close).length == 0) {
            $("body").append('<div class="' + fullData.closeClass + '" ' + fullData.attr + '="' + (box ? box : '') + '"><i class="layui-icon layui-close"></i></div>');
        }
        $(fullData.close).toggle();
        box_obj.toggleClass(fullData.class);
    }
})();

(bbfTabs = function () {
    var tabsData = {
        active: 'active',
        box: '.bbf-tabs',
        selectBox: '.bbf-tabs-select',
        listBox: '.bbf-tabs-list',
    };
    $(tabsData.box + " .bbf-tabs-item").on('click', function (e) {
        e.stopPropagation();
        switchTab(this);
    });
    $(tabsData.box + " .bbf-tabs-item.over").mousemove(function (e) {
        e.stopPropagation();
        switchTab(this);
    });
    $(tabsData.box + " .bbf-tabs-next").on('click', function (e) {
        e.stopPropagation();
        nextTab(this);
    });
    $(tabsData.box + " .bbf-tabs-prev").on('click', function (e) {
        prevTab(this);
    });

    function switchTab(obj, num) {
        num = num ? num : 0;
        var chechBox = num == 0 ? $(obj) : selectBox(obj).children().eq((selectActiveBox(obj).index() + num) % selectBox(obj).children().length);
        chechBox.addClass(tabsData.active).siblings().removeClass(tabsData.active);
        listBox(obj).children().eq(selectActiveBox(obj).index()).show().siblings().hide();
    }

    function selectBox(obj) {
        return $(obj).parents(tabsData.box).children(tabsData.selectBox);
    }

    function selectActiveBox(obj) {
        return selectBox(obj).children("." + tabsData.active);
    }

    function listBox(obj) {
        return $(obj).parents(tabsData.box).children(tabsData.listBox);
    }

    function nextTab(obj) {
        selectBox(obj);
        switchTab(obj, 1);
    }

    function prevTab(obj) {
        switchTab(obj, -1);
    }
})();

function showMsg(msg) {
    layer.msg(lang.get(msg));
}