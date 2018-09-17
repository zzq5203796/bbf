lang = {
    data: {
        "can't found full window.": '找不到那瘪犊子.',
        "not found STORE": '小三有不见了',
        "clear cache success.": '正在清理智障',
        "wait:": '老子腿都站麻了  ',
        "iframe reload": '隔壁老王前来报到',
        "disable F5": '哈哈，没反应，您奈我何.',
        "require error.": '又骗老子去搬砖.',
        "success": '成功人士路过',
        " ": '',
    },
    get: function (key) {
        var keyStr = lang.data[key] ? lang.data[key] : key;
        return keyStr;
    }
};
function stripscript(str)
{
    return str.replace(/(^\s*)|(\s*$)/g, "")
        .replace("\\", "\\\\").replace("*", "\\*")
        .replace("+", "\\+").replace("|", "\\|")
        .replace("{", "\\{").replace("}", "\\}")
        .replace("(", "\\(").replace(")", "\\)")
        .replace("^", "\\^").replace("$", "\\$")
        .replace("[", "\\[").replace("]", "\\]")
        .replace("?", "\\?").replace(",", "\\,")
        .replace(".", "\\.").replace("&", "\\&");
}
_Trans = {
    match: function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "")
            .replace("\\", "\\\\").replace("*", "\\*")
            .replace("+", "\\+").replace("|", "\\|")
            .replace("{", "\\{").replace("}", "\\}")
            .replace("(", "\\(").replace(")", "\\)")
            .replace("^", "\\^").replace("$", "\\$")
            .replace("[", "\\[").replace("]", "\\]")
            .replace("?", "\\?").replace(",", "\\,")
            .replace(".", "\\.").replace("&", "\\&");
    },
    trim: function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    },
    html: function (str) {
        return str;
    }
    
}