<?php
function ajax_success($msg = 'success', $data = []) {
    ajax_return(1, $msg, $data);
}

function ajax_error($msg = 'error', $data = []) {
    ajax_return(0, $msg, $data);
}

function ajax_reload($msg = 'jump to', $data = []) {
    ajax_return(300, $msg, $data);
}

function ajax_not_login($msg = 'not login', $data = []) {
    ajax_return(401, $msg, $data);
}

function ajax_not_auth($msg = 'not auth', $data = []) {
    ajax_return(403, $msg, $data);
}

function ajax_return($code, $msg = 'tips', $data = []) {
    echo json_encode(['status' => $code, 'msg' => $msg, 'data' => $data]);
    die();
}

function ajax_return_res($res, $msg = 'tips', $data = []) {
    if ($res) {
        ajax_success("操作成功", $data);
    } else {
        ajax_error("操作失败", $data);
    }
    die();
}

function display($data) {
    if (IS_AJAX) {
        ajax_return('', $data);
    } else {
        echo $data;
    }
}