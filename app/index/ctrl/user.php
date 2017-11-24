<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/24
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\index\ctrl;


use app\admin\auth;
use icf\lib\db;

class user extends auth {
    public function pwd() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ret = isExist($_POST, [
                'user' => ['func' => ['\app\common\user::isUser','user'], 'regex' => ['/^[\x{4e00}-\x{9fa5}\w\@\.]{2,}$/u', '用户名不符合规则'], 'msg' => '请输入用户名', 'sql' => 'user'],
                'pwd' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码'],
                'npwd' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入新密码', 'sql' => 'pwd'],
            ], $data);
            if ($ret === true) {
                if (\app\common\user::encodePwd($this->userMsg['uid'], $_POST['pwd']) != $this->userMsg['pwd']) {
                    return ['code' => -1, 'msg' => '旧密码不正确'];
                }
                $data['pwd'] = \app\common\user::encodePwd($this->userMsg['uid'], $data['pwd']);
                db::table('user')->where('uid', $this->userMsg['uid'])->update($data);
                db::table('token')->where('token',$_COOKIE['token'])->delete();
                return ['code' => 0, 'msg' => '修改成功,请重新登陆'];
            }
            return ['code' => -1, 'msg' => $ret];
        } else {
            view()->assign('user', $this->userMsg);
            view()->display();
        }
    }

}