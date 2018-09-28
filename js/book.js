var book = {
    url: {
        book: "/article/index",
        check: "/article/book?is_check=1&book=",
        run: "/article/book?save=1&book=",
        read: "/article/book?book=",
        down: "/article/down?book=",
        searc: "/article/search",
    },
    obj: {},
    box: {
        body: ".book-box > tbody"
    },
    init: function (data) {
        var that = this;
        that.get();

    },

    get: function () {
        var that = this;
        _ajax.post(that.url.book, [], function (data) {
            that.setHtml(data);
        });
    },
    clear: function () {
        var that = this;
        console.log(that);
        $(that.box.body).append('');
    },
    setHtml: function (data) {
        var that = this;

        var html = template('bookItem', {obj: that, list: data});
        $(that.box.body).append(html);
    },
    search: function () {

    }
}
// book.init();
bookv = tableView({
    box: ".book-table",
    name: "bookv",
    tmpl: "bookItem",
    data: [
        {key: 'id', title: 'ID'},
        {key: 'id', title: '标题'},
        {key: 'id', title: '本地'},
        {key: 'id', title: '数量'},
        {key: 'id', title: '锁'},
        {key: 'id', title: '来源'},
        {key: 'id', title: '其他'},
        {key: 'id', title: '<div class="btn line-1" onclick="bookv.goPage(0);">刷新</div>'},
    ],
    url: "/article/index",
    link: {
        book: "/article/index",
        check: "/article/book?is_check=1&book=",
        run: "/article/book?save=1&book=",
        read: "/article/book?book=",
        down: "/article/down?book=",
        searc: "/article/search",
        unlock: "/article/unlocks?book=",
    },
});
function bookRefresh(){
    bookv.goPage(0);
}
searchv = tableView({
    box: ".search-table",
    name: "searchv",
    tmpl: "searchItem",
    first: false,
    select: {
        show: false,
    },
    data: [
        {key: 'id', title: 'ID'},
        {key: 'id', title: '作品分类'},
        {key: 'id', title: '作品名称'},
        {key: 'id', title: '最新章节'},
        {key: 'id', title: '作者'},
        {key: 'id', title: '点击'},
        {key: 'id', title: '更新时间'},
        {key: 'id', title: '状态'},
        {key: 'id', title: '<div class="btn">刷新</div>'},
    ],
    show_page: false,
    url: "/article/search",
    click: {
        add: function (item) {
            ajax_post('', item)
        }
    }
});

$(document).on("click", ".search-keyword-box .btn", function () {
    _ajax.get("/article/search", {keyword: $(this).prev().val()}, function (data) {
        searchv.push(data.list);
    });
});
$(".search-keyword-box .keyword").on("keyup", function (e) {
    if (e.keyCode == 13) {
        $(".search-keyword-box .btn").click();
    }
});
$(document).on("click", ".do-pro", function () {
    show_progress('/article/test');
});

function show_progress(url, id, msg) {
    if (typeof(id) == 'undefined' || id == '' || id == null) {
        id = "t" + parseInt(Math.random(0, 1) * 100000);
    }
    var html = template('progressItem', {url: url, id: id, msg: msg});
    $(".progress").append(html);
}

function close_progress(id) {
    console.log(id);
    setTimeout(function () {
        $("#" + id).parent().remove();
    }, 3000);
}

var sss;