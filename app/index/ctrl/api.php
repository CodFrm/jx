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
use icf\lib\db;

class api extends auth {
    /**
     * 获取列表接口
     * @author Farmer
     */
    public function getSoftList() {
        $retJson = ['code' => 0, 'msg' => 'success'];
        $page = (isset($_GET['page']) ? $_GET['page'] : 1);
        $rec = db::table('soft_list')
            ->where('soft_type',1)
            ->field(['sid', 'soft_name', 'soft_exp', 'soft_logo', 'soft_path', 'soft_time'])
            ->limit(($page - 1) * 10, 10)->order('sid')->select();
        while ($row = $rec->fetch()) {
            $row['soft_path'] = getFileName($row['soft_path']);
            $retJson['rows'][] = $row;
        }
        $count = db::table('soft_list')->where('soft_type',1)->count();
        $retJson['total'] = ceil($count / 10);
        return json_encode($retJson);
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