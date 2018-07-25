var jump = {}
var left = 0; //用left变量存储赋给obj.style.left的值，以防每次系统都省略小数，所导致最后结果的细微差异
var iSpeed = 0;

function startMove(obj, iTarget) {
    clearInterval(obj.timer);
    obj.timer = setInterval(function () {
        iSpeed += (iTarget - obj.offsetLeft) / 5; //速度
        iSpeed *= 0.7; //考虑阻力
        left += iSpeed;
        if (Math.abs(iSpeed) < 1 && Math.abs(iTarget - obj.offsetLeft) < 1) //停止条件 速度和距离绝对值小于1
        {
            clearInterval(obj.timer);
            obj.style.left = iTarget + "px"; //清楚后，顺便把目标值赋给obj.style.left
        }
        else {
            obj.style.left = left + "px";
        }
    }, 30);
}

jump = (function () {
    var that = this,
        data = {
            box: ".jump",
            speed: {},
            left: 0,
            top: 0,
            left_rate: 1,
            top_rate: 1,
            timer: null,
        },
        jumpList = [];
    this.init = function (obj) {
        obj = Object.assign(data, obj);
        if ($(obj.box).length == 0) {
            $("body").append('<div style="width: 50px; height: 50px;border-radius: 50px; background: #999; display: inline-block; position: fixed; z-index: +99999;" class="jump"></div>');
        }
        startMove(obj)
    }

    function startMove(obj) {
        clearInterval(obj.timer);
        var chose = $(obj.box);
        // Math.ceil(Math.random() * 6
        obj.timer = setInterval(function () {
            obj.left += obj.speed * obj.left_rate;
            obj.top += obj.speed / 2 * obj.top_rate;
            if (obj.left + 50 >= window.innerWidth) {
                obj.left = window.innerWidth - 50;
                obj.left_rate = -1;
            }
            if (obj.top + 50 >= window.innerHeight) {
                obj.top = window.innerHeight - 50;
                obj.top_rate = -1;
            }
            if (obj.left <= 0) {
                obj.left = 0;
                obj.left_rate = 1;
            }
            if (obj.top <= 0) {
                obj.top = 0;
                obj.top_rate = 1;
            }
            chose.css({left: obj.left, top: obj.top});
            $(obj.box)
        }, 30);
    }

    return this;
})();

// jump.init({});


function RandomNum(num1, num2) {
    return Math.floor(Math.random() * (num2 - num1 + 1) + num1);

}

var con  =document.getElementById("con");
if(!con){
    $('body').append('<div id="con" class="back-body"></div>');
    con  =document.getElementById("con");
}

//构造小球函数
function Ball() {
    this.ball = document.createElement("div");
    var randomNum = RandomNum(20, 50);
    this.width = randomNum
    this.height = randomNum
    //如果元素有id名，我们可以直接使用，不用document.get....获取
    this.left = RandomNum(0, con.offsetWidth - randomNum);
    this.top = RandomNum(0, con.offsetHeight - randomNum);
    this.backgroundColor = "rgba(" + RandomNum(0, 255) + "," + RandomNum(0, 255) + "," + RandomNum(0, 255) + "," + Math.random() + ")";//随机颜色
    this.tempX = this.left;
    this.tempY = this.top;
    this.speedx = RandomNum(10, 20) - 15.5;//后面的小数是保证随机产生的方向有正有负
    this.speedy = RandomNum(10, 20) - 15.5;
}

//画小球的方法
Ball.prototype.draw = function () {
    this.ball.style.width = this.width + "px";
    this.ball.style.height = this.height + "px";
    this.ball.className = "ball";
    this.ball.style.left = this.tempX + "px";
    this.ball.style.top = this.tempY + "px";
    this.ball.style.backgroundColor = this.backgroundColor;
    con.appendChild(this.ball);
}


//运动函数
Ball.prototype.move = function () {

    this.tempX = this.tempX + this.speedx;
    this.tempY = this.tempY + this.speedy;
    // 判断临界值
    if (this.tempX + this.width >= document.body.clientWidth || this.tempX <= 0) {
        this.speedx = -this.speedx;//改变方向
    }
    if (this.tempY + this.height >= document.body.clientHeight || this.tempY <= 0) {
        this.speedy = -this.speedy;
    }
    this.draw();
}
//产生小球
var balls = [];
for (var i = 0; i < 40; i++) {
    var ball = new Ball();
    balls.push(ball);//生成的小球对象放进数组

}

function start() {
    for (var i = 0; i < balls.length; i++) {
        balls[i].move();
    }
}

var ballTime;

function ballStart() {
    ballTime = setInterval(start, 30);//设置定时器
}

function ballStop() {
    clearTimeout(ballTime);
}