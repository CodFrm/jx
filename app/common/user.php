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

    /**
     * 添加积分
     * @author Farmer
     * @param $uid
     * @param $number
     * @param int $type
     */
    public static function addIntegral($uid, $number, $param = 0, $type = 0) {
        $detail = '';
        if ($type == 0) {
            $detail = '管理员后台积分操作';
        } else if ($type == 1) {
            $detail = '机器人积分兑换';
        }
        db::table('user')->where('uid', $uid)->update('`integral`=`integral`+' . $number);
        $um = uidUser($uid);
        db::table('integral_change')->insert(['ic_uid' => $uid, 'ic_oper_type' => 100 + $type, 'ic_detail' => $detail,
            'ic_number' => $number, 'ic_time' => time(), 'ic_param_id' => $param, 'ic_over_integral' => $um['integral']]);
        return $um['integral'];
    }

    public static function buy_soft($sid) {
        if ($soft = sidSoft($sid)) {
            if (db::table('integral_change')->where(['ic_param_id' => $sid, 'ic_uid' => _cookie('uid')])->find()) {
                return true;
            }
            $user = uidUser(_cookie('uid'));
            $spend = $soft['soft_price'] <= 0 ? 1 : $soft['soft_price'] + 1;
            if ($user['integral'] < $spend) {
                return '积分不足';
            }
            db::table('user')->where('uid', $user['uid'])->update('`integral`=`integral`-' . $spend);
            $um = uidUser($user['uid']);
            db::table('integral_change')->insert(['ic_oper_type' => 1,
                'ic_detail' => '购买' . $soft['soft_name'] . '花费' . $spend . '积分', 'ic_uid' => $user['uid'],
                'ic_number' => -$spend,
                'ic_time' => time(), 'ic_param_id' => $sid, 'ic_over_integral' => $um['integral']]);
            return true;
        }
        return '软件不存在';
    }

    /**
     * 验证用户名
     * @author Farmer
     * @param $user
     * @return bool|string
     */
    public static function isUser($user, $my = null) {
        if ($um = self::getUser($user)) {
            if (is_null($my)) {
                return '用户名已经被注册';
            }
            return ($um['user'] == $my ?: '用户名已经被注册');
        } else {
            return true;
        }
    }

    public static function getUser($user) {
        return db::table('user')->where('uid', $user)->_or()->where('user', $user)->_or()->where('email', $user)->find();
    }

    public static function applyUser($user) {
        $ret = isExist($user, [
            'user' => ['func' => ['\app\common\user::isUser'], 'regex' => ['/^[\x{4e00}-\x{9fa5}\w\@\.]{2,}$/u', '用户名不符合规则'], 'msg' => '请输入用户名', 'sql' => 'user'],
            'pwd' => ['regex' => ['/^[\\~!@#$%^&*()-_=+|{}\[\], .?\/:;\'\"\d\w]{6,16}$/', '密码不符合规范'], 'msg' => '请输入密码', 'sql' => 'pwd'],
            'email' => ['func' => ['\app\common\user::isEmail'], 'regex' => ['/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', '邮箱不符合规则', 'msg' => '请输入邮箱'], 'sql' => 'email'],
            'avatar' => ['func' => ['is_res'], 'msg' => '头像不存在', 'sql' => 'avatar']
        ], $data);
        if ($ret === true) {
            $pwdUrl = url('index/user/pwd');
            //添加用户
            $data['integral'] = 10;
            db::table('user')->insert($data);
            $uid = db::table()->lastinsertid();
            set_time_limit(0);
            sendEmail($user['email'], '欢迎加入 - 信院计算机协会下载站',
                "<h1>欢迎加入</h1><p>您的登陆账号:{$data['user']} (邮箱,用户id都可以用于登陆 邮箱:{$data['email']}用户id为:$uid)</p>" .
                "<p>密码:{$data['pwd']}</p><p>密码修改可以访问 <a href='$pwdUrl'>$pwdUrl</a>进行修改</p>"
            );
            db::table('user')->where('uid', $uid)->update(['pwd' => user::encodePwd($uid, $data['pwd'])]);
            //添加权限
            db::table('usergroup')->insert(['uid' => $uid, 'group_id' => 2]);
            db::table('usergroup')->insert(['uid' => $uid, 'group_id' => 3]);
            return '申请成功,请注意邮箱邮件';
        }
        return $ret;
    }

    /**
     * 验证邮箱
     * @author Farmer
     * @param $user
     * @return bool|string
     */
    public static function isEmail($email) {
        if (self::getUser($email)) {
            return '邮箱已经被注册';
        } else {
            return true;
        }
    }

    /**
     * 编码密码
     * @author Farmer
     * @param $uid
     * @param $pwd
     * @return string
     */
    public static function encodePwd($uid, $pwd) {
        $str = md5($uid) . md5($pwd) . _config('pwd_deal');
        return md5($str);
    }

}