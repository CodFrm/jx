<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/23
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin;

use icf\lib\db;
require_once "common.php";
class auth {
    protected $userMsg;

    public function __construct() {
        if (!isLogin()) {
            //没有登录给予游客权限
            $_COOKIE['uid'] = -1;
            $this->userMsg['group'] = [db::table('group')->where(['group_id' => 2])->find()];
        } else {
            $this->userMsg = uidUser($_COOKIE['uid']);
            $this->userMsg['group'] = getGroup($_COOKIE['uid']);
        }
        foreach ($this->userMsg['group'] as $item) {
            if (isAuth($item['group_id'])) {
                $auth = true;
                break;
            }
        }
        if ($auth !== true) {
            header('Location:' . url('index/index/error', 'error=你没有相应的权限&url=' . url('/')));
            exit();
        }
    }
}