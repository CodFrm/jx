<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/16
 * blog:blog.icodef.com
 * function:函数库
 *============================
 */

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
