
var _sound = {
	path: "/public/sound/"
};
function sound(type) {
	var data = {
		error: ["error", "fail-mali"]
	};
	audio=new Audio(_sound.path+"fail-mali.mp3");//路径
	audio.play();
}