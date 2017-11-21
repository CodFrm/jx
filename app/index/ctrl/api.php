<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/21
 * blog:blog.icodef.com
 * function:接口
 *============================
 */

namespace app\index\ctrl;


use icf\lib\db;

class api {
    public function __construct() {
        if (!isLogin()) {
//            echo json(['code' => -1, 'msg' => '请先登录']);
//            exit();
        }
    }

    /**
     * 获取列表接口
     * @author Farmer
     */
    public function getSoftList() {
        $retJson = ['code' => 0, 'msg' => 'success'];
        $page = (isset($_GET['page']) ? $_GET['page'] : 1);
        $rec = db::table('soft_list')->limit(($page - 1) * 10, 10)->order('sid')->select();
        while ($row = $rec->fetch()) {
            $retJson['rows'][] = $row;
        }
        $count = db::table('soft_list')->count();
        $retJson['total'] = ceil($count / 10);
        return json_encode($retJson);
    }
}