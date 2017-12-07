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


use app\admin\auth;
use app\common\log;
use app\common\user;
use icf\lib\db;

class oper extends auth {
    public static function getOper(db\query $table, $field = '*', $page = 1, $keyword = []) {
        if ($keyword) {
            foreach ($keyword as $key => $value) {
                $table->where($key, "%$value%", 'like');
            }
        }
        $total = $table->count();
        $table->limit(($page - 1) * 20, 20);
        $table->field($field);
        return ['rows' => $table->select()->fetchAll(), 'total' => $total, 'page' => ceil($total / 20), 'code' => 0, 'msg' => 'success'];
    }

    public function operUser($at = 'get', $page = 1, $keyword = '') {
        $ret = [];
        if ($at == 'get') {
            return self::getOper(db::table('user'), 'uid,user,email,avatar,integral', $page, ['user' => $keyword]);
        } else if ($at == 'add') {
            $_POST['avatar']=$_POST['image'];
            $ret = user::applyUser($_POST);
            if (strpos($ret, '成功') !== false) {
                return ['code' => 0, 'msg' => $ret];
            }
            return ['code' => -1, 'msg' => $ret];
        } else if ($at == 'cp') {
            $ret = isExist($_GET, [
                'npwd' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码', 'sql' => 'pwd'],
            ], $data);
            if ($ret === true) {
                db::table('user')->where('uid', _get('uid'))->update(['pwd' => user::encodePwd(_get('uid'), $data['pwd'])]);
                log::operChangePassword(_get('uid'));
                return ['code' => 0, 'msg' => '修改成功'];
            }
            return ['code' => 0, 'msg' => $ret];
        } else if ($at == 'adi') {
            $n = ceil(_get('n'));
            $log = log::operIntegral(_get('uid'), $n);
            user::addIntegral(_get('uid'), $n, $log);
            return ['code' => 0, 'msg' => '操作成功'];
        }

        return $ret;
    }
}