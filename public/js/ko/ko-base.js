var ko = require('ko');
var comm = require('common');
var baseViewModel = {
	baseUrl: baseUrl,
};

window.myApp = baseViewModel;

function createViewModel(model) {
	var result = Object.assign({
		app: baseViewModel
	}, model);
	return result;
}


module.exports = {
	ko,
	createViewModel
};
