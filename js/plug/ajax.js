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
    ajax.request(url, method, data, opt);
    return false;
});
ajax = (function () {
    var that = this;
    this.get = function (url, params, option) {
        request(url, 'get', params, option);
    };
    this.post = function (url, params, option) {
        request(url, 'post', params, option);
    };
    this.request = function (url, method, data, option) {
        var opt = {
            success: function () {
            },
            error: error,
            msg: '',
            show_msg: false,
            type: 'json',
            timeout: 30000
        };
        option = typeof (option) == 'function' ? {success: option} : option;
        option = Object.assign(opt, option);
        $.ajax({
            url: url,
            async: false,
            type: method,
            data: data,
            dataType: option.type,
            success: success,
            error: (function (option) {
                return function (xhr, status, error) {
                    log([xhr, status, error], 5);
                    option.error("require error.");
                }
            })(option),
            timeout: option.timeout
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
    return this;
})();
