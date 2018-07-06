var comm = require('common');
var kob = require('kob');
require('jquery');
var ko = kob.ko;
//管理
var myViewModel = kob.createViewModel({
    content: ko.observable(''),
    banners: ko.observableArray([]),
    jsonData: ko.observable({}),
});
// myViewModel.json(songResJson);
myViewModel.jsonData(comm.json_data(songResJson));
ko.applyBindings(myViewModel);
module.exports = {
    comm,
    myViewModel
};