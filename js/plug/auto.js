var startTime = Math.ceil(new Date().getTime() / 1000), //单位秒
    getDuration = function () {
        var time = '',
            hours = 0,
            minutes = 0,
            seconds = 0,
            endTime = Math.ceil(new Date().getTime() / 1000),
            duration = endTime - startTime;

        hours = Math.floor(duration / 3600); //停留小时数
        minutes = Math.floor(duration % 3600 / 60); //停留分钟数
        seconds = Math.floor(duration % 3600 % 60); //停留秒数

        time = (hours < 10 ? '0' + hours : hours) + ':' + (minutes < 10 ? '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds : seconds);

        return time;
    };
