
function getmessage() {
    
}
if(_config.debug){
	log(version, 4);
}

function showMsg(msg) {
    layer.msg(lang.get(msg));
}

/**
	level: 0 msg, 4 debug,  6 warring, 10 error,

*/
function log(data, level) {
    level = level ? level : 0;
    if(_config.level<=level){
    	console.log(data, level);
    }
}