var book = {
    url: {
        book: "/article/index",
        check: "/article/book?is_check=1&book=",
        run: "/article/book?save=1&book=",
        read: "/article/book?book=",
        down: "/article/down?book=",
        searc: "/article/search",
    },
    obj:{

    },
    box: {
        body: ".book-box > tbody"
    },
    init: function(data) {
        var that = this;
        that.get();

    },

    get: function(){
        var that = this;
        _ajax.post(that.url.book, [], function(data){
            that.setHtml(data);
        });
    },
    clear: function(){
        var that = this;
        console.log(that);
        $(that.box.body).append('');
    },
    setHtml: function(data){
        var that = this;

        var html = template('bookItem', {obj: that, list: data});
        $(that.box.body).append(html);
    },
    search: function(){

    }
}
// book.init();
bookv = tableView({
    box: ".book-table",
    tmpl: "bookItem",
    data: [
        {key: 'id', title: 'ID'},
        {key: 'id', title: '标题'},
        {key: 'id', title: '本地'},
        {key: 'id', title: '来源'},
        {key: 'id', title: '其他'},
        {key: 'id', title: '<div class="btn" onclick="bookv.goPage(0);">刷新</div>'},
    ],
    url: "/article/index",
});