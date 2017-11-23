<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/23
 * blog:blog.icodef.com
 * function:用户操作
 *============================
 */

namespace app\common;


use icf\lib\db;

class user {
    public static function buy_soft($sid) {
        if ($soft = sidSoft($sid)) {
            if (db::table('integral_change')->where(['ic_param_id' => $sid, 'ic_uid' => _cookie('uid')])->find()) {
                return true;
            }
            $user = uidUser(_cookie('uid'));
            $size = filesize($filename = __ROOT_ . '/static/res/' . $soft['soft_path']);
            $spend = ceil($size / 1024 / 1024 / 500);
            if ($user['integral'] < $spend) {
                return '积分不足';
            }
            db::table('integral_change')->insert(['ic_oper_type' => 1,
                'ic_detail' => '购买' . $soft['soft_name'] . '花费' . $spend . '积分', 'ic_uid' => $user['uid'],
                'ic_number' => -$spend,
                'ic_time' => time(), 'ic_param_id' => $sid]);
            db::table('user')->where('uid', $user['uid'])->update('`integral`=`integral`-' . $spend);
            return true;
        }
        return '软件不存在';
    }
}