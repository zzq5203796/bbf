/**
 *
 *  attr ajax-data|data  ajax-post|method  tips|tips ajax-cb|cb
 *
 *
 */
$(document).on('click', '.ajax', function (e) {
    e.stopPropagation();
    var url = $(this).attr("url"),
        data = {A: 1},
        data_fun = $(this).attr("ajax-data"),
        method = $(this).hasClass("ajax-post") ? 'POST' : 'GET',
        tips = $(this).hasClass("tips"),
        cb = $(this).attr("ajax-cb");
    try {
        data = eval(data_fun);
    } catch (e) {
        data = data_fun;
        data = JSON.parse(data);
    }
    try {
        if (typeof (data) == "string") {
            data = JSON.parse(data);
        }
    } catch (e) {
        data = {};
    }
    url = url ? url : '/upload/index';

    var opt = {
        tips: tips, success: cb ? eval(cb) : function () {
        }
    };

    if(!$(this).hasClass('ajax-only')) opt._obj = $(this);
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
    that.data = {};
    that.request = function (url, method, data, option) {
        var opt = {
            success: function () {
            },
            error: error,
            msg: '',
            id: 'auto',
            show_msg: false,
            type: 'json',
            timeout: 30,
        };
        option = typeof (option) == 'function' ? {success: option} : option;
        option = Object.assign(opt, option);
        if (option._obj) {
            if(option._obj.hasClass('disable')){
                return;
            }
            option._obj.addClass("wait disable");
        }

        if (option.id !== 'auto' && that.data[option.id]) {
            that.data[option.id].abort();
        }
        req = $.ajax({
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
            timeout: option.timeout * 1000
        });
        if (option.id !== 'auto') {
            that.data[option.id] = req;
        }

        function autoEnd() {
            if (option._obj) {
                option._obj.removeClass("wait disable");
            }
        }
        function success(res) {
            option.show_log && log(res);
            autoEnd();
            sound("success");
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
            autoEnd();
            showMsg(msg);
            sound("eorror");
        }
    };


    return that;
})();
