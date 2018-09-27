template_load("/template/tableView.html", "table", function () {
    for (var i in tableWait) {
        if (tableWait[i].cb != 0) {
            tableWait[i].cb();
            tableWait[i].cb = 0;
        }
    }
});

var tableWait = [];

function tableView(opts) {
    var that = {}, opt = {};

    opts = Object.assign({
        url: '',
        id: 'id',
        box: 'table.table-box',
        name: 'tablev',
        tmpl: 'tableViewItem',
        first: true,
        data: [
            {key: 'id', title: 'ID'},
            {key: 'title', title: '标题'},
            {key: 'id', title: 'ID'},
            {key: 'id', title: 'ID'},
        ],
        select: {
            show: true,
            max: 0
        },
        params: {},
        paged: 0,
        page_type: '1',

        total: 101,
        size: 10,
        total_page: 0,
        show_page: true,
    }, opts);
    opts.call = function (obj, cb) {
        return function () {
            cb(obj);
        }
    }

    if ($("#tableView").length == 0) {
        tableWait.push({cb: init, name: opts.box});
    } else {
        init();
    }
    opts.first && first();

    function init() {
        var html = template('tableView', {opts: opts, list: opts.data});
        if ($(opts.box).length == 0) {
            tableWait[opts.box] = html;
        }
        $(opts.box).append(html);
        event();
    }

    function event() {
        $(opts.box).on('click', '.tpage div', function (e) {
            var types = ['next', 'prev', 'first', 'last'];
            for (var i in types) {
                if ($(this).hasClass(types[i])) {
                    goPage(types[i]);
                }
            }
        });
        select.init();
    }

    var select = {
        mode: 0, // 0 all , other NUM is max num 
        bodybox: " tbody tr",
        allbox: " thead .bbf-checkbox",
        name: "checkIds",
        init: function () {
            $(opts.box).on('click', select.bodybox, function (e) {
                select.choose($(this));
            });
            $(opts.box).on('click', select.bodybox + " .stop", function (e) {
                e.stopPropagation();
            });
            $(opts.box).on('click', select.allbox, function (e) {
                select.chooseAll();
            });
        },
        choose: function (obj) {
            select.change(obj, !obj.hasClass("select"));
            select.change($(opts.box + select.allbox), select.is_all());
        },
        change: function (obj, value) {
            var checkbox = obj.find(".bbf-checkbox"),
                type = value ? "addClass" : "removeClass";
            if (obj.hasClass("bbf-checkbox-all"))
                checkbox = obj;
            obj[type]("select");
            checkbox[type]("checked").children().prop("checked", value);
        },
        is_all: function () {
            var obj = $(opts.box + select.bodybox),
                is_all = true;
            for (var i = 0; i < obj.length; i++) {
                if (!obj.eq(i).find(".bbf-checkbox").hasClass("checked")) {
                    is_all = false;
                    break;
                }
            }
            return is_all;
        },
        chooseAll: function () {
            var obj = $(opts.box + select.bodybox), is_all = select.is_all();
            select.change($(opts.box + select.allbox), !is_all)
            for (var i = 0; i < obj.length; i++) {
                select.change(obj.eq(i), !is_all);
            }
        },
        data: function () {
            var obj = $(opts.box + select.bodybox), data = [], item;
            for (var i = 0; i < obj.length; i++) {
                item = obj.eq(i);
                if (item.hasClass("select"))
                    data.push(item.find(select.name).val());
            }
            return data;
        }
    };

    function search() {
        goPage(0);
    }

    function first() {
        goPage('first');
    }

    function clear() {
        goPage(0);
    }

    function next() {
        goPage('next');
    }

    function prev() {
        goPage('prev');
    }

    function last() {
        goPage('last');
    }

    function goPage(page) {
        var params = opts.params;
        switch (page) {
            case 'first':
                page = 0;
                break;
            case 'last':
                page = opts.total_page - 1;
                break;
            case 'next':
                page = parseInt(opts.paged) + 1;
                break;
            case 'prev':
                page = opts.paged - 1;
                break;
            default:
                break;
        }
        page = page > 0 ? (page < opts.total_page ? page : opts.total_page - 1) : 0;
        params.page = page;
        _ajax.get(opts.url, params, function (data) {
            if (data.list) {
                opts.total = parseInt(data.total);
                opts.paged = parseInt(data.page);
                opts.size = data.size;
                data = data.list;
            } else {
                opts.paged = parseInt(page);
            }
            opts.total_page = Math.ceil(opts.total / opts.size);
            setHtml(data);
        });
    }

    function setHtml(data) {
        var box = $(opts.box + " tbody");
        if (box.length == 0) {
            tableWait.push({
                cb: function () {
                    setHtml(data);
                }, name: opts.box + " tbody"
            });
            return false;
        }
        var html = '';
        for (var i in data) {
            html += template(opts.tmpl, {opts: opts, i: i, item: data[i]});
        }

        if (opts.page_type == 1) {
            box.html(html);
        } else {
            box.append(html);
        }
        setPageHtml();
        select.change($(opts.box + select.allbox), select.is_all());
    }

    function setPageHtml() {
        if (opts.show_page) {
            var html = template("tableViewPage", {opts: opts, page: getPage()});
            $(opts.box + " .tpage").html(html);
        }
    }

    function getPage() {
        var total_page = opts.total_page;
        var show_page = 7,
            page = opts.paged + 1,
            show_page_temp = 3,
            start_page = page - show_page_temp,
            end_page = page + show_page_temp;

        start_page = start_page > 0 ? start_page : 1;
        end_page = opts.total_page < end_page ? opts.total_page : end_page;

        if (end_page - start_page < show_page - 1) {
            if (start_page == 1) {
                end_page = show_page;
            } else {
                start_page = end_page - show_page;
            }
        }

        start_page = start_page > 0 ? start_page : 0;
        end_page = opts.total_page < end_page ? opts.total_page : end_page;

        return {
            start: start_page,
            end: end_page,
            paged: page,
            total: total_page,
        };
    }

    that.goPage = goPage;
    that.push = setHtml;
    that.select = select;
    that.opts = opts;
    that.click = opts.click;
    return that;
}