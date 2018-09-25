function sound(type) {
	_sound.play(type);
}

_sound = {
	data: {
		error: ["error", "fail-mali",
			"gitign/fail-aa",
			"gitign/fail-bi",
			"gitign/fail-game",
			"gitign/error",
		],
		success: [
			"gitign/victory-zhadan",
			"gitign/true",
			"gitign/true-answay",
		],
		warring: [
			"gitign/warring-dudu",
			"gitign/warring-jingbao",
		],
		auto: ['bi',"error", "fail-mali"],
	},
	path: "/public/sound/",
	palyData: [],
	init: function(){

	},
	getValue: function(type){
		if(1){
			return this.data.auto[0];
		}
		type = this.data[type]? this.data[type]: this.data.auto;
		type = type[randomNum(0, type.length-1)];
		return type;
	},
	play: function(type){
		type = this.getValue(type);
		try{
            audio=new Audio(_sound.path+type+".mp3");//路径
            audio.play();
            this.palyData.push(audio);
		}catch (e) {
			log(e,10);
        }
	}
};
function randomNum(minNum,maxNum){ 
    switch(arguments.length){ 
		case 1:
            return parseInt(Math.random()*minNum+1,10); 
        break;
        case 2:
            return parseInt(Math.random()*(maxNum-minNum+1)+minNum,10); 
        break;
            default:
                return 0;
            break;
    }
};