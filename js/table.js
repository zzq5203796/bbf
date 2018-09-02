
template_load("/template/tableView.html", "table", function(){
    for(var i in tableWait){
        if(tableWait[i].cb != 0){
            tableWait[i].cb();
        }
    }
});

var tableWait = [];

function tableView(opts){
    var that = {}, opt = {};

    opts = Object.assign({
        url: '',
        box: 'table.table-box',
        tmpl: 'tableViewItem',
        data: [
            {key: 'id', title: 'ID'},
            {key: 'title', title: '标题'},
            {key: 'id', title: 'ID'},
            {key: 'id', title: 'ID'},
        ],
        params: {},
        paged: 0,
        page_type: '1',

        total: 0,
        size: 0,
        total_page: 0,
    }, opts);

   if($("#tableView").length==0){
        tableWait.push({cb:init, name: opts.box});
    }else{
        init();
    }
    first(); 

    function init(){
        var html = template('tableView', {obj: opts, list: opts.data});
        if($(opts.box).length==0){
            tableWait[opts.box] = html;
        }
        $(opts.box).append(html);

    }

    function search(){
        goPage(0);
    }

    function first(){ goPage('first');}
    function clear(){ goPage(0);}
    function next(){ goPage('next');}
    function prev(){ goPage('prev');}
    function last(){ goPage('last');}

    function goPage(page){
        var params = opts.params;
        switch(page){
            case 'first':
                page = 0; 
                break;
            case 'last':
                page = opts.total_page-1; 
                break;
            case 'next':
                page = parseInt(opts.paged)+1; 
                break;
            case 'prev':
                page = opts.paged-1; 
                break;
            default:
                break;
        }
        page = page < 0? 0: (page < opts.total_page? page: opts.total_page-1);
        params.page = page;
        _ajax.get(opts.url, params, function(data){
            setHtml(data);
            opt.paged = parseInt(page);
        });
    }

    function setPageHtml(){

    }

    function setHtml(data){
        var box = $(opts.box+" tbody");
        if(box.length==0){
            tableWait.push({cb: function(){setHtml(data);}, name: opts.box+" tbody"});
            return false;
        }
        var html = template(opts.tmpl, {obj: opts, list: data});

        if(opts.page_type == 1){
            box.html(html);
        }else{
            box.append(html);
        }
    }
    that.goPage = goPage;
    that.opts = opts;
    return that;
}