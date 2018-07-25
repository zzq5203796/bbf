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