<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/16
 * blog:blog.icodef.com
 * function:函数库
 *============================
 */

use icf\lib\db;

function isLogin() {
    if ($uid = _cookie('uid') && $token = _cookie('token')) {
        return verifyToken(_cookie('uid'), _cookie('token'));
    }
    return false;
}


function getUser($user) {
    return db::table('user')->where('uid', $user)->_or()->where('user', $user)->_or()->where('email', $user)->find();
}

/**
 * 编码密码
 * @author Farmer
 * @param $uid
 * @param $pwd
 * @return string
 */
function encodePwd($uid, $pwd) {
    $str = md5($uid) . md5($pwd) . _config('pwd_deal');
    return md5($str);
}


/**
 * 获取token
 * @author Farmer
 * @param $uid
 * @return string
 */
function getToken($uid) {
    $token = getRandString(8, 2) . time();
    db::table('token')->insert(['uid' => $uid, 'token' => $token, 'time' => time()]);
    db::table('token')->where('time<' . (time() - 604800))->delete();
    return $token;
}

/**
 * 验证token
 * @author Farmer
 * @param $uid
 * @param $token
 * @return bool
 */
function verifyToken($uid, $token) {
    $where = ['token' => $token, 'uid' => $uid];
    $tokenMsg = db::table('token')->where($where)->find();
    if (!$tokenMsg) {
        return false;
    } else if ($tokenMsg['time'] + 604800 < time()) {
        db::table('token')->where($where)->delete($where);
        return false;
    }
    db::table('token')->where($where)->update(['time' => time()]);
    return true;
}

/**
 * 通过路径获取文件名
 * @author Farmer
 * @param $path
 * @return bool|string
 */
function getFileName($path) {
    if(($pos=strrpos($path, '/'))!==false){
        $pos++;
    }
    $s = substr($path, $pos);
    return $s;
}