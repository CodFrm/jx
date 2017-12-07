<?php
/**
 *============================
 * author:Farmer
 * time:2017/12/7
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\index\ctrl;


use app\admin\auth;
use app\admin\ctrl\oper;
use icf\lib\db;
use icf\lib\view;

class area extends auth {
    private $title = '分区管理';

    private function createAreaSql($array) {
        $sql = '(';
        foreach ($array as $item) {
            $sql .= "`sort_id`={$item['sort_id']} or ";
        }
        $sql = substr($sql, 0, strlen($sql) - 3);
        $sql .= ')';
        return $sql;
    }

    public function index() {
        $v = new view();
        $area = $this->getManageArea($_COOKIE['uid']);
        $sql = self::createAreaSql($area);
        $db = db::table('soft_sort as a')->field('count(distinct sid)')
            ->join(':soft_list as b', 'a.soft_id=b.sid');
        $db->where($sql);
        $total =$db->find()['count(distinct sid)'];
        $v->assign('total', $total);
        $v->assign('area_manage', $area);
        $v->assign('breadcrumb', '<a href="' . __HOME_ . '">首页</a>/<a href="' . __HOME_ . '/index/area/index">' . $this->title . '</a>');
        $v->assign('user', $this->userMsg);
        $v->display();
    }

    private function getManageArea($uid) {
        $rec = db::table('area_manager as a')->join(':sort as b', 'a.sort_id=b.sort_id')
            ->field(['sort_name', 'a.sort_id'])->where('uid', $uid)->select();
        $retSort = [];
        while ($row = $rec->fetch()) {
            $tmp['sort_name'] = $row['sort_name'];
            $tmp['sort_id'] = $row['sort_id'];
            $retSort[$row['sort_id']] = $tmp;
            $retSort = array_merge($retSort, $this->forManageArea($row['sort_id']));
        }
        return $retSort;
    }

    private function forManageArea($sid) {
        $retSort = [];
        $rec = db::table('sort')
            ->field(['sort_name', 'sort_id'])->where('sort_fid', $sid)->select();
        while ($row = $rec->fetch()) {
            $tmp['sort_name'] = $row['sort_name'];
            $tmp['sort_id'] = $row['sort_id'];
            $retSort[$row['sort_id']] = $tmp;
            $retSort = array_merge($retSort, $this->forManageArea($row['sort_id']));
        }
        return $retSort;
    }

    public function area($at = 'get') {
        if ($at == 'get') {
            $area = $this->getManageArea($_COOKIE['uid']);
            foreach ($area as $item) {
                $sql = api::forSubSort_SQL($item['sort_id']);
                $db = db::table('soft_sort as a')
                    ->join(':soft_list as b', 'a.soft_id=b.sid');
                if ($sql != '') {
                    $sql = "( `sort_id`={$item['sort_id']} or " . substr($sql, 0, strlen($sql) - 3) . ' )';
                    $db->where($sql);
                } else {
                    $db->where("`sort_id`={$item['sort_id']}");
                }
                $db->count();
            }
            oper::getOper($db);
        }
    }

}
