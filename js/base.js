mytext = "<meta name=\"renderer\" content=\"webkit\">" +
  "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">" +
  "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">" +
  "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">" +
  "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">" +
  "<meta name=\"format-detection\" content=\"telephone=no\">" +
  "<link rel=\"stylesheet\" href=\"/public/static/layui/css/layui.css\"  media=\"all\">" +
  "<link rel=\"stylesheet\" href=\"/css/bbf.css\"  media=\"all\">" +
  "<script type=\"text/javascript\" src=\"/public/static/layui/layui.all.js\"></script>" +
  "<script type=\"text/javascript\" src=\"/public/static/systemjs/system.min.js\"></script>" +
  "<script type=\"text/javascript\" src=\"/public/static/config.js\"></script>";
document.write(mytext);
var baseUrl = window.location.host;
setTimeout(function () {
    if(window.entryJS){
        importM(window.entryJS, function (module) {
            window.myEntry = module;
            console.log("加载完成", module);
            if (window.onEntryLoad) {
                window.onEntryLoad(module);
            }
        });
    }
},200);