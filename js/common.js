_config = {
    debug: true,
    level: 0,
};

$(".sort").click(function () {
    var box = $(".menu-text.active").parent().parent(),
        sort_status = $(this).hasClass("desc") ? 1 : ($(this).hasClass("asc") ? 2 : 0);
    var menu = box.children();
    $(this).removeClass("desc").removeClass("asc");
    sort_status == 0 && $(this).addClass("desc");
    sort_status == 1 && $(this).addClass("asc");

    menu = menu.sort(
        function compareFunction(obj1, obj2) {
            var t1 = $(obj1).text(),
                t2 = $(obj2).text();
            if (sort_status == 0) {
                t1 = $(obj1).text();
                t2 = $(obj2).text();
            }
            if (sort_status == 1) {
                t1 = $(obj2).text();
                t2 = $(obj1).text();
            }
            if (sort_status == 2) {
                t1 = $(obj1).children(".menu-text").attr("title");
                t2 = $(obj2).children(".menu-text").attr("title");
            }
            return t1.localeCompare(t2, "zh");
        }
    );
    var str = '';
    for (var i = 0; i < menu.length; i++) {
        str += $(menu[i]).prop("outerHTML");
    }
    box.html(str);
});
$(".set-title").on('click', function (e) {
    if ($(this).attr("data-title") == undefined || $(this).attr("data-title") == '') {
        $(this).attr("data-title", $("title").html());
    }
    $("title").html($(".menu-text.active").text() + " | " + $(this).attr("data-title"));
});
$(".reload").on('click', function (e) {
    reloadDom(this);
});

$(".change-back").on('change', function (e) {
    $(this).toggleClass("on");
    var data = $(this).get(0).checked ? {toggle: 'show', fun: ballStart} : {toggle: 'hide', fun: ballStop};
    $("#con")[data.toggle]();
    data.fun();
});

function autorequires() {
    require('plug/*');
}
function template_load(url, box, cb){
    if($("#loadScript").length==0){
        $("body").append("<div id='loadScripts'></div>");
    }
    if($("#loadScript-"+box).length==0){
        $("#loadScripts").append("<div id='loadScript-" +box+"'></div>");
    }
    $("#loadScript-"+box).load(url, function(responseTxt,statusTxt,xhr){
        if(statusTxt=="success")
            cb();
        if(statusTxt=="error")
        alert("Error: "+xhr.status+": "+xhr.statusText);

    });
}