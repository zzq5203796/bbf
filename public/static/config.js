var jsRoot = "/"
System.config({
	baseURL: "/public",
	packages: {
		"jquery": {main: 'jquery-3.1.1.min.js', defaultExtension: 'js'},
		"common": {main: 'common.js', defaultExtension: 'js'},
		"ko": {main: 'knockout-3.4.2-min.js', defaultExtension: 'js'},
		"/": {defaultJSExtensions: true}
	},
	map: {
		"jquery": "static/jquery",
		"common": "js/ko",
		"ko": "static",
		"kob": "js/ko/ko-base",
	}
});
function importM(module, callback) {
	System.import(module).then(callback, function (err) {
		console.log("客户端import错误", err);
	});
}
