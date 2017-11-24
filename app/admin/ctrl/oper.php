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
use icf\lib\db;

class oper extends auth {
    private function getOper(db\query $table, $field = '*', $page = 1, $keyword = []) {
        if ($keyword) {
            foreach ($keyword as $key => $value) {
                $table->where($key, "%$value%", 'like');
            }
        }
        $table->limit(($page - 1) * 20, 20);
        $table->field($field);
        return ['rows'=>$table->select()->fetchAll(),'code'=>0,'msg'=>'success'];
    }

    public function operUser($at = 'get', $page = 1, $keyword = '') {
        $ret = [];
        if ($at == 'get') {
            return $this->getOper(db::table('user'), 'uid,user,email,avatar,integral', $page, ['user' => $keyword]);
            $where = ['log_type' => 12];
            $oper = new listOper('log as a', 'log_id', $where);
            if ($keyword) {
                $where['b.order_number'] = ["%$keyword%", 'like', 'b_start'];
                $where['c.user'] = ["%$keyword%", 'like', 'or', 'b_end'];
            }
            $ret = $oper->get($page, $where, 'join :order as b on a.log_param=b.order_id join :user as c on c.uid=a.log_uid',
                'log_id,log,user,log_time,log_ip,order_number');
            $ret['code'] = 0;
        } else {
            if ($at == 'add') {
                $data['order_time'] = time();
                if (DB('order')->insert($data) > 0) {
                    $lid = DB()->lastinsertid();
                    if ($userMsg = uidUser($_POST['remarks'])) {
                        money_change($userMsg['uid'], (double)$_POST['money'], $lid, '充值金额' . (double)$_POST['money'] . '元');
                        $userMsg = uidUser($_GET['remarks']);
                        DB('order')->update(['order_balance' => $userMsg['money']], ['order_id' => $lid]);
                    }
                    $ret = ['code' => 0, 'msg' => '加入成功'];
                } else {
                    $ret = ['code' => -1, 'msg' => '订单编号重复'];
                }
            } else if ($at == 'edit') {

            }
        }
        return $ret;
    }
}