var comm = require('common');
var kob = require('kob');
require('jquery');
var ko = kob.ko;
//管理
var myViewModel = kob.createViewModel({
    rem: ko.observable(1),
    root_size: ko.observable('40'),
    root_size_rem: ko.observable(1),
    font_1: ko.observable('1'),
    line_1: ko.observable(1),
    font_2: ko.observable(1),
    line_2: ko.observable(1),
    step: ko.observable('0'),
    box: divItem(),
    box1: divItem(),
    box2: divItem(),
    banners: ko.observableArray([]),
    steps: ko.observableArray([]),
    jsonData: ko.observable({}),
    getnum: function (num, type) {
        return getnum(num, type);
    },
    getrem: function (num, type) {
        return  getrem(num, type);
    },
    getremhtml: function (num) {
        return getremhtml(num);
    },
    getfont: function (font, line) {
        var fonts = getnum(font);
        var lines = getnum(line) / fonts;
        return getremhtml('字体', fonts) + getremhtml('行高', lines * fonts)+ '<br/>' + getremhtml('行高', lines, true) + getremhtml('空隙', (lines - 1) * fonts);
    },
    getStep: function () {
        var step = getnum(myViewModel.step());
        return getremhtml('', step);
    },
    getEvalRem: function (value) {
        return getrem(eval(value.replace(/[^0-9+\-*\/\.]/g,''))+(value.indexOf('rem')>-1?'rem':'px'));
    },
    addItem: function (type) {
        switch (type){
            case 'step':
                myViewModel.steps.push(stepItem())
        }
    },
    getRems: function () {
        var str = myViewModel.getEvalRem(myViewModel.step());
        for(var i in myViewModel.steps()){
            str += '-' + myViewModel.getEvalRem(myViewModel.steps()[i].value());
        }
        return myViewModel.getEvalRem(str);
    }
});
myViewModel.steps.push(stepItem());
function stepItem() {
    var item = {
        value: ko.observable('')
    };
    return item;
}
function divItem() {
    var _item = {
        margin: 0,
        border:0,
        padding: 0,
        size: 16,
        line: 1,
    };
    for(var i in _item){
        _item[i] = ko.observable(_item.i);
    }
    _item.css = 'bbf-div-box-new';
    return _item;
}
function getrem(num, type) {
    return Math.round(getnum(num, type) * 1000) / 1000 + ' rem';
}
function getremhtml(title, num, type) {
    num = num ? num.toFixed(3) : 0;
    title = title ? title + ': ' : '';
    type = type ? '' : ' rem';
    return '<span class="code">' + title + num + type + '</span>';
}
function getnum(num, type) {
    num = num?num:0;
    var value = parseFloat(num);
    value = value || value==0?value:'';
    if (value == num && type) {
        return value;
    }
    if (typeof(num) == 'number') {
        return value / myViewModel.root_size();
    }else if (num.indexOf("rem") > -1) {
        return value;
    }else if (num.indexOf("px") > -1) {
        return value / myViewModel.root_size();
    }else{
        return value / myViewModel.root_size();
    }
}
ko.applyBindings(myViewModel);
