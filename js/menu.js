function onloadFrame(obj) {
    var $mainFrame = $('#myIframe');
    try {
        var title = $('#myIframe').contents()[0].title;
        title = '^_^' + title;
        // var url = obj.contentWindow.location.href;
    } catch (e) {
        showMsg("not allow.");
        console.log(e);
        return;
    }
    $(".get-title").text(title);
    $("title").html($("title").html().split("^_^")[0] + title);
}

_menu = {
    box: "#xxx",
    link: {
        pull: "",
    },
    left: {
        width: 0,
        status: true
    },
    data: [],
    init: function(data){
        var that = this;
        $(".pull_action").val(getStore("pull")['pull_action']);
        $(".pull").click();

        that.store.init();
        that.event();

        $(".menu-text").next().toggle();
        that.left.width = $("#left").width();
        that.left.status = true;
        $("#left .main").width(that.left.width);
        $("#left .top-tip").width(that.left.width - 20);
        $("#left .top-tip").height($("#left .top-tip").height() + "px");
        $("#left .main").css('top', $("#left .top-tip").outerHeight() + "px");
        $("#right .main").css('top', $("#right .top-tip").outerHeight() + "px");

    },
    event: function(){
        var that = this;

        // 点击
        $(".menu-level").on('click', '.menu-text', function (e) {
            e.stopPropagation();
            if ($(this).attr('disabled') == 'disabled') {
                return false;
            }
            if ($(this).next().length > 0) {
                $(this).children(".icon-xiajiantou").toggleClass('icon-youjiantou');
                $(this).next().toggle('fast');
                return false;
            } else {
                // console.log("menu go to url");
            }

            $(".get-title").text("loading...");
            $("#menu_nav_link").text($(this).text());
            $("#menu_nav_link").attr('href', $(this).attr('href'));
            $(".menu-node>.menu-text").removeClass('active');
            $(".nav-icon").text($(this).text().slice(0,1).toUpperCase());
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
            _menu.toggleAll(1);
        });
        $(".menu-hide-all").click(function () {
            _menu.toggleAll(0);
        });

        // toggle left box
        $(".toggle-box").click(function () {
            var t = 500, that = this,
                width = _menu.left.status > 0 ? 0 : _menu.left.width;
            _menu.left.status = !_menu.left.status;
            $("#left").animate({width: width}, t, animateEnd);

            function animateEnd() {
                $(that).children('.toggle-icon')[width == 0 ? 'removeClass' : 'addClass']("icon-zuojiantou");
            }
        });

        $(".full-window-btn").click(function () {
            var t = 500, that = this;
            $("#left").animate({width: 0}, t);
            $(".toggle-box").children('.toggle-icon').removeClass("icon-zuojiantou");
        });

        $(".new-iframe").change(function () {
            $("#book").toggle();
        });

        $(".getbook").click(function () {
            $("#book").attr("src", "/article/book?book="+$(".book_id").val()+"&save="+($(".book_save").is(":checked")?1:0)+"&p="+$(".page_link").val());
            $(".new-iframe").prop('checked', true);
            $("#book").show();
        });

        $(".sort").click(function () {
            that.sort();
        });

        $(".fastsearch").bind(' input propertychange ',function (e) {
            e.stopPropagation();
            _menu.toggleAll(0);
            var keyword = $(this).val(),
                lists = $(".menu-text"), item;
                if(keyword == ''){
                    return;
                }
            for(var i=0; i < lists.length; i++){
                item = lists.eq(i);
                if(item.text().indexOf(keyword) != -1){
                    item.addClass("search-item");
                    _menu.showItem(item);
                }else{
                    item.removeClass("search-item");
                }
            }
        });

    },
    toggleAll: function(is_show){
        if(is_show){
            $(".menu-text").children(".icon-xiajiantou").removeClass('icon-youjiantou');
            $(".menu-text").next().show();
        }else{
            $(".menu-text").children(".icon-xiajiantou").addClass('icon-youjiantou');
            $(".menu-text").next().hide();
        }

    },
    showItem: function(item){
        item.parents(".menu-next").show();
        item.parents(".menu-next").prev().children(".icon-xiajiantou").removeClass('icon-youjiantou');
    },
    has_hide: function(){ 
        var obj = $(".menu-text");
        for (var i = 0; i < obj.length; i++) {
            if (i > 300)
                break;
            if (!this.is_show($(obj[i]))) {
                return true;
            }
        }
        return false;
    },
    is_show: function(obj) {
        return obj.next().css('display') != 'none';
    },
    tree: function(){

    },
    pull: {
        data: function() {
            var a = $(".pull_action").val();
            setStore("pull", 'pull_action', a);
            return {a: a}
        },
        cb: function(data) {
            var that = _menu;
            var html = that.html(data);
            that.data = data;
            $(".menu-level").html(html);
            for (var i in data) {
                setStore1(i, data[i], 1);
            }
            that.store.init();
        }

    },
    store: {
        init: function() {
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
        },
        data: function() {
            return {re_name: getStore('re_name'), link_check: getStore('link_check')};
        },
    },
    sort: function(){
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
    },
    html: function(data){
        var str = '';
        var child, icon, item;
        for (var i in data) {
            if (!data[i].name) {
                continue;
            }
            item  = Object.assign({
                child: []
            }, data[i]);
            // console.log(data[i]);
            var count = getAutoData(item.child.length, 0);
            var all_count = getAutoData(item.all_length, '');
            var title = item.name + getAutoData(count, '', "(" + count + ")");

            child = count == 0 ? '' : '<ul class="menu-next" style="display: none;">' + _menu.html(item.child) + '</ul>';
            icon = count == 0 ? '<i class="icon iconfont icon-lianjie" ><i style="width: 22px;height: 29px;display: block;position: absolute;top: -8px;left: -5px;"></i></i>' : '<i class="icon iconfont icon-xiajiantou icon-youjiantou"></i>';

            str += '<li class="menu-node">' +
                '<a class="menu-text" title="' + title + all_count + '" href="' + item.url + '" target="myIframe">' +
                icon + title +
                '</a>' +
                child +
                '</li>';
        }
        return str;
    }
};
_menu.init();


function getAutoData(name, value, new_value) {
    return name ? (new_value ? new_value : name) : value;
}
// sound("start");