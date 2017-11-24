<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/23
 * blog:blog.icodef.com
 * function:
 *============================
 */

use icf\lib\db;

/**
 * 通过uid获取用户信息
 * @author Farmer
 * @param $uid
 * @return mixed
 */
function uidUser($uid) {
    return db::table('user')->where(['uid' => $uid])->find();
}

/**
 * 获取用户组信息
 * @author Farmer
 * @param $uid
 * @return array
 */
function getGroup($uid) {
    if ($rec = db::table('usergroup as a|group as b')->order('group_auth')->where(['uid' => $uid, 'a.group_id=b.group_id'])->select()) {
        return $rec->fetchAll();
    }
    return [];
}

function isAuth($group_id) {
    $rec = db::table('groupauth as a|auth as b')->where(['group_id' => $group_id, 'a.auth_id=b.auth_id'])->select();
    $model = input('model');
    $ctrl = input('ctrl');
    $action = input('action');
    while ($msg = $rec->fetch()) {
        if ($count = substr_count($msg['auth_interface'], '->')) {
            if ($count == 1) {
                if (($model . '->' . $ctrl) == $msg['auth_interface']) {
                    return true;
                }
            } else {
                if (($model . '->' . $ctrl . '->' . $action) == $msg['auth_interface']) {
                    return true;
                }
            }

        } else {
            if ($msg['auth_interface'] == $model) {
                return true;
            }
        }
    }
    return false;
}

/**
 * 通过路径搜索文件
 * @author Farmer
 * @param $path
 * @return mixed
 */
function getPathSoft($path) {
    return db::table('soft_list as a')->where('soft_type', 0)->join(['user' => 'b'], 'a.soft_uid=b.uid')->where('soft_path', $path)->find();
}

/**
 * 时间转路径并创建目录
 * @author Farmer
 * @param $path
 * @return bool|string
 */
function time2path($path = '/', &$timePath = '') {
    $year = date('Y');
    $month = date('m');
    if (!file_exists($path . $year)) {
        if (!mkdir($path . $year, 0777, true)) return false;
    }
    if (!file_exists($path . $year . '/' . $month)) {
        if (!mkdir($path . $year . '/' . $month, 0777, true)) return false;
    }
    $timePath = $year . '/' . $month . '/';
    return $path . $year . '/' . $month . '/';
}


function getFile($path, $url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $content = curl_exec($ch);
    curl_close($ch);
    $fp2 = @fopen($path, 'a');
    fwrite($fp2, $content);
    fclose($fp2);
}