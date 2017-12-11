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

function isAuth($group_id, &$auth = array()) {
    $rec = db::table('groupauth as a|auth as b')->where(['group_id' => $group_id, 'a.auth_id=b.auth_id'])->select();
    $model = input('model');
    $ctrl = input('ctrl');
    $action = input('action');
    while ($msg = $rec->fetch()) {
        $auth[$msg['auth_interface']]=1;
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
