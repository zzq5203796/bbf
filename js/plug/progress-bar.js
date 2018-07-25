var progressLoadingTimeOut;

function progressLoading(num) {
    clearTimeout(progressLoadingTimeOut);
    progressLoadingTimeOut = setTimeout(function () {
        num = num ? num : 0;
        num = num > 100 ? 100 : num;
        progressBar(num);
        if (num < 100) {
            // num += 1;
            num += Math.ceil(Math.random() * 5) + 1;
            progressLoading(num);
        }
    }, 9);
}

function progressBar(num) {
    $(".progress-bar .bar").width(num + "%").attr('data-afterContent', num + "%");
}