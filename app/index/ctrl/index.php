<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7 19:22
 * blog:blog.icodef.com
 * function:首页
 *============================
 */

namespace app\index\ctrl;

use icf\lib\view;

class index {

    public function index() {
        $v = new view();
        $v->assign('user',getUser(_cookie('uid')));
        $v->display();
    }

    public function login() {
            $json = ['code' => -1, 'msg' => '系统错误'];
            $ret = isExist($_GET, [
                'user' => ['regex' => ['/^[\x{4e00}-\x{9fa5}\w\@\.]{2,}$/u', '用户名不符合规则'], 'msg' => '请输入用户名', 'sql' => 'user'],
                'pwd' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码', 'sql' => 'password'],
            ], $data);
            if ($ret === true) {
                if ($userMsg = getUser($_GET['user'])) {
                    if ($userMsg['pwd'] == encodePwd($userMsg['uid'], $_GET['pwd'])) {
                        setcookie('token', getToken($userMsg['uid']), time() + 86400, '/');
                        setcookie('uid', $userMsg['uid'], time() + 86400, '/');
                        $json['code'] = 0;
                        $json['msg'] = '登陆成功';
                    } else {
                        $json['code'] = -1;
                        $json['msg'] = '密码错误';
                    }
                } else {
                    $json['msg'] = '账号不存在';
                }
            } else {
                $json['msg'] = $ret;
            }
            return json($json);
    }

}