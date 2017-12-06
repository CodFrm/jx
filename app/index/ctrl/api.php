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


use app\admin\auth;
use app\common\user;
use icf\lib\db;

class api extends auth {
    public function forSubSort_SQL($sid) {
        $sql = '';
        $tmpRec = db::table('sort')->where('sort_fid', $sid)->select();
        while ($row = $tmpRec->fetch()) {
            $sql .= " `sort_id`={$row['sort_id']} or ";
            $sql .= $this->forSubSort_SQL($row['sort_id']);
        }
        return $sql;
    }

    /**
     * 获取列表接口
     * @author Farmer
     */
    public function getSoftList($sid = 0, $keydown = '') {
        $retJson = ['code' => 0, 'msg' => 'success'];
        $page = (isset($_GET['page']) ? $_GET['page'] : 1);
        if ($sid > 0) {
            $db = db::table('soft_sort as a')
                ->join(':soft_list as b', 'a.soft_id=b.sid');
            $sql = $this->forSubSort_SQL($sid);
            $db->where("( `sort_id`=$sid or " . substr($sql, 0, strlen($sql) - 3) . ' )');
        } else {
            $db = db::table('soft_list');
        }
        $db->where('soft_type', 1)
            ->field(['sid', 'soft_name', 'soft_exp', 'soft_logo', 'soft_path', 'soft_time'])
            ->limit(($page - 1) * 10, 10)->order('sid');

        if (!empty($keydown)) {
            $db->where('soft_name', "%{$keydown}%", 'like')->_or();
            $db->where('soft_exp', "%{$keydown}%", 'like');
        }
        $count = $db->count();
        $rec = $db->select();
        while ($row = $rec->fetch()) {
            $row['soft_path'] = getFileName($row['soft_path']);
            $retJson['rows'][] = $row;
        }
        $retJson['total'] = ceil($count / 10);
        return $retJson;
    }

    /**
     * 文件下载
     * @author Farmer
     * @param int $sid
     */
    public function download($sid = 0) {
        if ($soft = db::table('soft_list')->where('sid', $sid)->find()) {
            $filename = __ROOT_ . '/static/res/' . $soft['soft_path'];
            if (!(@$hfile = fopen($filename, 'r'))) {
                return _404();
            }
            if (($msg = user::buy_soft($sid)) !== true) {
                auth::_error($msg, url('/'));
                return;
            }
            header('Content-Description: File Transfer');
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . getFileName($filename) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            //下载限速
            set_time_limit(0);
            while (!feof($hfile)) {
                print fread($hfile, 1024 * 1024 * 4);//4M/S
                sleep(1);
            }
            fclose($hfile);
            return;
        }
        return _404();
    }
}