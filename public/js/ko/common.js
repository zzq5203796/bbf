function json_data(data) {
    if (typeof (data) != "object") {
        try {
            data = JSON.parse(data);
        }catch (e) {
            data = [];
            console.log(data);
            console.log(e);
        }
    }
    var data_ = [];
    for (var i in data) {
        var item = {
            name: i,
            value: data[i],
            text: data[i],
            type: typeof (data[i]),
            has_child: false,
            child: []
        }
        if (item.type == 'object' || item.type == 'array') {
            // console.log(item.value instanceof Array, item.value)
            item.type = item.value instanceof Array ? 'array' :item.type;
            item.text = item.type;
            item.has_child = true;
            item.child = json_data(data[i]);
        }
        data_.push(item);
    }
    return data_;
}


module.exports = {
    json_data
};