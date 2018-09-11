$(".win-min-btn").on('click', function(){
    $(this).toggleClass("icon-narrow");
    $(this).parents(".win-nav").next().toggle();
});
$(".win-close-btn").on('click', function(){
    $(this).parents(".win-box").hide();
});
function runscroll() {
    var obj = $(".run-scroll");
    var max = obj[0].scrollHeight/2, scrollto = obj.scrollTop();
    obj.scrollTop((scrollto >= max ? 0 : scrollto) + 2);
}
if($(".run-scroll").length>0){
	var str = '';
	for (var i = 1; i < 20; i++) {
	    str += '<li>好消息! 好消息' + i + '...</li>';
	}
	$(".run-scroll .auto").html(str+str);
	var scrolltimeout = setInterval(runscroll, 30);

}