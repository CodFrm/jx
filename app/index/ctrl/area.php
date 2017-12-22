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
            $sql .= "`soft_sort_id`={$item['sort_id']} or ";
        }
        $sql = substr($sql, 0, strlen($sql) - 3);
        $sql .= ')';
        return $sql;
    }

    public function index() {
        $v = new view();
        $area = $this->getManageArea($_COOKIE['uid']);
        $sql = self::createAreaSql($area);
        $db = db::table('soft_list');
        $db->where($sql);
        $total = $db->count();
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

    public function oper($at = 'get', $page = 1, $keyword = '') {
        if ($at == 'get') {
            $area = $this->getManageArea($_COOKIE['uid']);
            $sql = self::createAreaSql($area);
            $db = db::table('soft_list as a')
                ->join(':user as b', 'a.soft_uid=b.uid');
            $db->where($sql)->where('soft_type', 0);
            $page =
            $json = oper::getOper($db,
                'uid,sid,user,soft_filename as filename,soft_name as name,soft_path as file,soft_logo as image,soft_price as price,soft_name',
                $page, $keyword);
            foreach ($json['rows'] as $key => $item) {
                $json['rows'][$key]['file'] = getFileName($json['rows'][$key]['file']);
            }
            return $json;
        } else if ($at == 'pass') {
            $soft = db::table('soft_list')->where('sid', _get('sid'))->find();
            $retJson = ['code' => -1];
            $filePath = 'static/res/' . $soft['soft_path'];
            if (!is_file($filePath)) {
                $retJson['msg'] = '文件不存在';
                return $retJson;
            }
            $tmpFile = md5($filePath) . '_' . $soft['soft_filename'];
            $newFile = time2path('static/res/soft/', $timePath) . $tmpFile;
            @rename($filePath, $newFile);
            db::table('soft_list')->where('sid', _get('sid'))->update(['soft_type' => 1, 'soft_path' => 'soft/' . $timePath . $tmpFile]);
            return ['code' => 0, 'msg' => '成功'];
        } else if ($at == 'refuse') {
            db::table('soft_list')->where('sid', _get('sid'))
                ->update(['soft_reason' => htmlspecialchars(_get('reason')), 'soft_type' => 2]);
            return ['code' => 0, 'msg' => '成功'];
        }
    }

}
