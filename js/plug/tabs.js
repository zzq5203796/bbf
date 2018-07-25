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