/**
 *  
 *  attr ajax-data|data  ajax-post|method  tips|tips ajax-cb|cb
 *  
 *  
 */
$(".ajax").on('click', function (e) {
    var url = $(this).attr("url"),
        data = {A: 1},
        data_fun = $(this).attr("ajax-data"),
        method = $(this).hasClass("ajax-post") ? 'POST' : 'GET',
        tips = $(this).hasClass("tips"),
        cb = $(this).attr("ajax-cb");
    if (data_fun) {
        data = eval(data_fun);
    }
    url = url ? url : '/upload/index';

    var opt = {tips: tips, success: cb ? eval(cb) : {}};
    _ajax.request(url, method, data, opt);
    return false;
});


_ajax = (function () {
    var that = {};
    that.get = function (url, params, option) {
        that.request(url, 'get', params, option);
    };
    that.post = function (url, params, option) {
       that.request(url, 'post', params, option);
    };
    that.request = function (url, method, data, option) {
        var opt = {
            success: function () {
            },
            error: error,
            msg: '',
            show_msg: false,
            type: 'json',
            timeout: 30
        };
        option = typeof (option) == 'function' ? {success: option} : option;
        option = Object.assign(opt, option);
        if(option._obj){
            option._obj.addClass("wait disable");
        }

        $.ajax({
            url: url,
            async: true, // default true
            type: method,
            data: data,
            dataType: option.type,
            success: success,
            error: (function (option) {
                return function (xhr, status, error) {
                    log([xhr, status, error], 10);
                    option.error("require error.");
                }
            })(option),
            timeout: option.timeout*1000
        });

        function success(res) {
            log(res);
            var code = res.status;
            if (code != 1) {
                option.error(res.msg, res);
            } else {
                showres(res.msg);
                option.success(res.data, res);
            }
        }

        function showres(msg) {
            option.show_msg && showMsg(res.msg);
        }

        function error(msg) {
            showMsg(msg);
        }
    };
    return that;
})();
