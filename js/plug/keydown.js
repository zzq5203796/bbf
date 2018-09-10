function reloadDom(obj) {
    if (obj == 'f5') {
        try {
            document.myIframe.location.reload();
            showMsg(lang.get("iframe reload"));
        } catch (e) {
            showMsg(lang.get("disable F5"));
        }
        return false;
    }
    if ($(obj).attr("data-for")) {
    } else {
        window.location.reload(true);
        // setTimeout(function () {
        // }, 100);
    }
}

window.onbeforeunload = function (event) {
    var duration = getDuration();
    // showMsg(lang.get("wait:") + duration);
    showMsg('clear cache success.');
    $.ajax({
        url: '/zip/menu',
        dataType: "json",
        async: false,
        data: "",
        beforeSend: function () {
        },
        success: function (data) {
            // showMsg('clear cache success.');
        }
    });
    // return true;
};

$(document).keydown(function (e) {
    var code = e.keyCode;
    if (!group_key(e)) {
        return false;
    }
    if (code != 40 && code != 38) { //38-40 left up tight down
        return true;
    }
    var node = code == 40 ? 'next' : 'prev';
    var obj = $(".menu-text.active");
    if (obj.parent()[node]().length > 0) {
        var height = obj.height(),
            scroll = obj.parents('.scroll'),
            top = obj.parent()[node]().offset().top;
        if (top < 0) {
            scroll.scrollTop(scroll.scrollTop() + top);
        }
        var jump = top + height * 2.9 - scroll.height();
        if (jump > 0) {
            scroll.scrollTop(scroll.scrollTop() + jump);
        }
        obj.parent()[node]().children(".menu-text")[0].click();
        return false;
    }
});

function group_key(e) {
    var code = e.keyCode;
    this.ctrl = {
        shiftKey: 16,
        ctrlKey: 17,
        altKey: 18,
    };
    this.checkKey = function (data) {
        // console.log(code);
        for (var i in data) {
            if (e[i] && code == data[i]) {
                return true;
            }
        }
        return false;
    };
    this.checkKey(this.ctrl);
    if ((e.ctrlKey && code == 17) || (e.altKey && code == 18) || (e.shiftKey && code == 16)) {
        return true;
    }
    $(".keycontent").children().length > 35 && $(".keycontent").children().eq(1).remove();
    $(".keycontent").append("<p>CTRL: " + e.ctrlKey + "; &nbsp;&nbsp;&nbsp;&nbsp; ALT:  " + e.altKey + "; &nbsp;&nbsp;&nbsp;&nbsp;  Code: " + code + "</p>");
    if (code == 116) {
        reloadDom("f5");
        return false;
    }
    return true;
}