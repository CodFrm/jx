<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/24
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;


use app\common\log;
use app\common\user;

class api {
    private $ip_list = ['127.0.0.1', '::', 'localhost','10.1.6.31'];

    public function __construct() {
        if (!in_array(getip(), $this->ip_list)) {
            _404();
            exit();
        }
        if (_get('key') != 'aDgea233Emm') {
            _404();
            exit();
        }
    }

    public function apply($qq) {
        $user['user'] = $qq;
        $user['pwd'] = getRandString(8, 1);
        $user['email'] = "$qq@qq.com";
        $path = time2path('static/res/images/', $user['avatar']) . $qq . '_' . time() . '.png';
        $user['avatar'] = $user['avatar'] . $qq . '_' . time() . '.png';
        getFile($path, 'http://q1.qlogo.cn/g?b=qq&nk=' . $qq . '&s=100');
        return user::applyUser($user);
    }

    public function exp($user, $number) {
        $number = ceil($number);
        if ($number <= 0) {
            return '充值数量错误';
        }
        if ($um = user::getUser($user)) {
            $lid = log::operIntegral($um['uid'], $number);
            $over = user::addIntegral($um['uid'], $number, $lid, 1);
            $home = url('/');
            sendEmail($um ['email'], '积分兑换成功 - 信院计算机协会下载站',
                "<h1>积分兑换成功</h1><p>您成功的兑换了:{$number}点积分,您的积分余额为{$over}</p>" .
                "<p>快去使用吧: <a href='$home'>$home</a></p>"
            );
            return '充值成功';
        } else {
            return '用户不存在';
        }
    }
}