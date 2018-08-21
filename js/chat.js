$(function () {
    $(".chat-img-box").on("click", ".close-img", function () {
        $(this).parent().remove();
        $(".chat-img-box .add-img").show();
    });

    var uploader_avatar = WebUploader.create({
        // 选完文件后，是否自动上传。
        auto: true,
        duplicate: false, // 同一文件是否可以重复上传
        // swf文件路径
        swf: '__STATIC__/libs/webuploader/Uploader.swf',
        // 文件接收服务端。
        server: "/shop/shop/upload_pic.html",
        //验证文件总数量, 超出则不允许加入队列
        fileNumLimit: 5,
        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
        resize: false,
        // 验证单个文件大小是否超出限制, 超出则不允许加入队列
        fileSingleSizeLimit: 2 * 1024 * 1024,
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        //选择文件的按钮
        pick: '#upload_images',
        // 只允许选择图片文件
        accept: {title: 'Images', extensions: 'gif,jpg,jpeg,bmp,png', mimeTypes: 'image/*'}
    });
    uploader_avatar.on('fileQueued', function (file) {
        uploader_avatar.upload();
    });
    /*上传成功**/
    uploader_avatar.on('uploadSuccess', function (file, result) {
        var img = $(".chat-img-box > div");
        if (img.length > uploader_avatar.options.fileNumLimit) {
            show_msg('数量不超过' + uploader_avatar.options.fileNumLimit + '张');
            return false;
        }
        if (result.code) {
            var data = result.data;
            $(".chat-img-box").append('<div class="chat-img">' +
                '<img src="' + data.url + '" />' +
                '<input name="images[]" value="' + data.url + '" />' +
                '<span class="close-img"></span></div>');
            if ($(".chat-img-box > div").length > uploader_avatar.options.fileNumLimit) {
                $(".chat-img-box .add-img").hide();
            }
            uploader_avatar.reset();
        } else {
            updateAlert(result.msg);
            setTimeout(function () {
                $(this).removeClass('disabled').prop('disabled', false);
            }, 1500);
        }
    });
    chat.init();
});
if (!detail) {
    var detail = {
        order_no: "000",
        goods_id: 0,
        buyer_id: 0,
        seller_id: -1,
        uid: 0,
    }
}
var chat = {
    init: function () {
        var that = this;
        that.obj.send.click(function () {
            that.send();
        });
        that.polling();
        that.getList();
    },
    link: {
        send: "/user/Leavemessage/addChat",
        list: "/user/leavemessage/list_chat.html",
        newest: "",
    },
    btn: {
        send: ".send-msg-btn",
        removeImg: ".close-img",
    },
    box: {chat: ".my-consult-chat-box-wapper"},
    obj: {
        send: $(".send-msg-btn"),
        chat: $(".my-consult-chat-box-wapper ul"),
        content: $("#msg-content")
    },
    data: function () {
        var that = this,
            data = {
                seller_id: detail.seller_id,
                order_no: detail.order_no,
                msg: trim(that.obj.content.val()),
                images: []
            },
            img = $("[name='images[]']");
        for (var i = 0; i < img.length; i++) {
            data.images[i] = img.eq(i).val();
        }
        if (data.msg.length < 5) {
            show_msg("字数太少.");
            return false;
        }
        return data;
    },
    reset: function () {
        var that = this;
        that.obj.content.val("");
        $(that.btn.removeImg).parent().remove();
    },
    send: function () {
        var data = this.data(), that = this;
        if (!data) {
            return;
        }
        data._obj = this.obj.send;
        that.stopPolling();
        setTimeout(function () {
            that.polling();
        }, 10000);
        ajax_post_success("/user/Leavemessage/addChat", data, function (data) {
            that.pushMsg(data);
            that.reset();
            that.waitCount = 0;
            that.polling();
        });
    },
    getList: function () {
        var that = this;
        if (that.waitCount > that.pollMax) {
            that.pushMsg({
                nickname: "系统消息",
                username: "系统消息",
                avatar: "/themes/default/public/img/common/defaultHead.png",
                msg: "长时间未操作，已下线"
            });
            that.stopPolling();
            return;
        }
        ajax_get_success(this.link.list, {order_no: detail.order_no, goods_id: detail.goods_id}, function (data) {
            that.obj.chat.html("");
            if (data.length == 0) {
                that.stopPolling();
            }
            for (var i in data) {
                that.pushMsg(data[i]);
            }
        });
    },
    pushMsg: function (data) {
        var imgs = "",
            is_me = data.uid == detail.uid;
        for (var i in data.images) {
            imgs += '<img class="chat-img" src="' + data.images[i] + '">';
        }
        var name = typeof (data.nickname) == "undefined" && data.nickname != "" ? data.username : data.nickname;
        var time = '<li><div class="rtime">2018-08-21 09:43</div></li>',
            title = is_me ?
                '<div style="text-align: right">' +
                '<span>' + name + '</span>' +
                '<img class="avatar" src="' + data.avatar + '">' +
                '</div>' :
                '<div>' +
                '<img class="avatar" src="' + data.avatar + '">' +
                '<span>' + name + '</span>' +
                '</div>';
        var str = time + '<li><div class="my-consult-chat-' + (is_me ? 'right' : 'lef') + '-layout"><div>' +
            title +
            '<div class="my-consult-chat-say">' +
            data.msg +
            '<div class="my-consult-chat-image">' + imgs + '</div>'
        '</div>' +
        '</div></div></li>';
        this.obj.chat.append(str);

        //todo
        this.obj.chat.parent().scrollTop(this.obj.chat.parent()[0].scrollHeight);
    },
    pollTimeout: '',
    pollMax: 20,
    waitCount: 0,
    stopPolling: function () {
        clearTimeout(this.pollTimeout);
    },
    polling: function () {
        var that = this,
            t = 10000, num;
        num = that.waitCount > 11 ? (that.waitCount * 3 + 20) : (that.waitCount > 5 ? (that.waitCount * 2 + 10) : 0);
        t += 1000 * num;
        that.stopPolling();
        this.pollTimeout = setTimeout(function () {
            that.waitCount++;
            that.getList();
            that.polling();
        }, t);
    }
}

function trim(str) { //删除左右两端的空格
    str = str ? str : '';
    return str.replace(/(^\s*)|(\s*$)/g, "");
}