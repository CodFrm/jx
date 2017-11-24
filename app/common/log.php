<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/24
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\common;


use icf\lib\db;

class log {

    public static function operChangePassword($uid) {
        db::table('log')->insert(['log_uid' => $_COOKIE['uid'], 'log_msg' => "修改密码,被修改者UID:$uid", 'log_time' => time(), 'log_type' => 1]);
        return db::table()->lastinsertid();
    }

    private static function log($msg, $type) {
        if (!isset($_COOKIE['uid'])) {
            $_COOKIE['uid'] = 0;
        }
        db::table('log')->insert(['log_uid' => $_COOKIE['uid'], 'log_msg' => $msg, 'log_time' => time(), 'log_type' => $type]);
        return db::table()->lastinsertid();
    }

    public static function operIntegral($uid, $number) {
        return self::log(("积分操作 UID:$uid 变动:$number"), 2);
    }
}