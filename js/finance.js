$(document).on('change', '.finance input, .finance select', function () {
    var data = {
        use_money: 0,
        get_money: 0,
        get_money_tem: 0,
        loss_money: 0,
        use_days: 0,
        use_unit: 0,
    };
    for (var i in data) {
        data[i] = parseFloat($(".input-box [name='" + i + "']").val());
    }
    $(this).attr("name") == 'get_money_tem' && $(".input-box [name='get_money']").val(data.use_money + data.get_money_tem + data.loss_money);
    finance();
});

function finance() {
    var data = {
        use_money: 0,
        get_money: 0,
        loss_money: 0,
        use_days: 0,
        use_unit: 0,
    };
    for (var i in data) {
        data[i] = $(".input-box [name='" + i + "']").val();
    }
    var money = keepDecimalFull((data.get_money - data.use_money - data.loss_money), 2, 1),
        money_rate = money * 100,
        day_rate, mouth_rate, year_rate;
    $(".input-box [name='get_money_tem']").val(money);
    switch (data.use_unit) {
        case 'mouth':
            day_rate = 1 / 30;
            mouth_rate = 1;
            year_rate = 12;
            break
        case 'year':
            day_rate = 1 / 365;
            mouth_rate = 1 / 12;
            year_rate = 1;
            break;
        default:
            day_rate = 1;
            mouth_rate = 30;
            year_rate = 365;
            break;
    }
    var rate = money_rate / data.use_money,
        avg_rate = (rate / data.use_days),
        avg_money = money / data.use_days;
    var res = {
        '收益': money,
        '回报率': keepDecimalFull(rate + 100, 4) + "%",
        '日利率': keepDecimalFull(avg_rate * day_rate * 100, 2) + "‱",
        '日收益': keepDecimalFull(avg_money * day_rate, 2),
        '月利率': keepDecimalFull(avg_rate * mouth_rate, 4) + "%",
        '月收益': keepDecimalFull(avg_money * mouth_rate, 2),
        '年利率': keepDecimalFull(avg_rate * year_rate, 2) + "%",
        '年收益': keepDecimalFull(avg_money * year_rate, 2),
    };
    var str = JSON.stringify(res)
    $(".finance_res").html(str);
    console.log(res);
}

finance();

function keepDecimalFull(num, bit) {
    var result = parseFloat(num),
        nomust = arguments[2] ? arguments[2] : 0;
    if (isNaN(result)) {
        alert('传递参数错误，请检查！');
        return false;
    }
    var bit_num = Math.pow(10, bit);
    result = Math.round(num * bit_num) / bit_num;
    var s_x = result.toString();
    if (!nomust) {
        var pos_decimal = s_x.indexOf('.');
        if (pos_decimal < 0) {
            pos_decimal = s_x.length;
            s_x += '.';
        }
        while (s_x.length <= pos_decimal + 2) {
            s_x += '0';
        }
    }
    return s_x;
}