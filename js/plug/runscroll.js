$(".win-min-btn").on('click', function(){
    $(this).parent().next().toggle();
});
function runscroll() {
    var obj = $(".run-scroll");
    var max = obj[0].scrollHeight/2, scrollto = obj.scrollTop();
    obj.scrollTop((scrollto >= max ? 0 : scrollto) + 2);
}
var str = '';
for (var i = 1; i < 20; i++) {
    str += '<li>好消息! 好消息' + i + '...</li>';
}
$(".run-scroll .auto").html(str+str);
var scrolltimeout = setInterval(runscroll, 30);