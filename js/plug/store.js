function setStore(name, key, value) {
    return store.setKey(name, key, value);
}

function setStore1(name, obj, is_tran) {
    store.merge(name, obj, is_tran);
}

function getStore(name) {
    return store.get(name);
}

store = (function () {
    function setKey(name, key, value) {
        var store = getStore(name);
        if (typeof (key) == "Object") {
            store = Object.assign(store, key);
        } else {
            store[key] = value;
        }
        set(name, store);
    }

    function push(name, obj) {
        var store = getStore(name);
        store.push(obj);
        set(name, store);
    }

    function get(name) {
        var store = localStorage.getItem(name);
        store = store ? store : '{}';
        try {
            return JSON.parse(store);
        } catch (e) {
            showMsg(lang.get("not found STORE") + " [" + name + "]");
            return {};
        }
    }

    function set(name, store) {
        store = JSON.stringify(store); //转化为JSON字符串
        localStorage.setItem(name, store);
    }

    function merge(name, obj, is_tran) {
        var store = getStore(name);
        store = is_tran ? Object.assign(obj, store) : Object.assign(store, obj);

        set(name, store);
    }
    
    var that = {};
    that.set = set;
    that.setKey = setKey;
    that.push = push;
    that.merge = merge;
    that.get = get;
    return that;
})();