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
    public static function forSubSort_SQL($sid) {
        $sql = '';
        $tmpRec = db::table('sort')->where('sort_fid', $sid)->select();
        while ($row = $tmpRec->fetch()) {
            $sql .= " `soft_sort_id`={$row['sort_id']} or ";
            $sql .= self::forSubSort_SQL($row['sort_id']);
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
            $db = db::table('soft_list');
            $sql = self::forSubSort_SQL($sid);
            if ($sql == '') {
                $db->where("`soft_sort_id`=$sid");
            } else {
                $db->where("( `soft_sort_id`=$sid or " . substr($sql, 0, strlen($sql) - 3) . ' )');
            }
        } else {
            $db = db::table('soft_list');
        }
        $db->where('soft_type', 1)
            ->field(['sid', 'soft_name', 'soft_exp', 'soft_logo', 'soft_filename', 'soft_time'])
            ->limit(($page - 1) * 10, 10)->order('sid');
        if (!empty($keydown)) {
            $db->where('soft_name', "%{$keydown}%", 'like')->_or();
            $db->where('soft_exp', "%{$keydown}%", 'like');
        }
        $count = $db->count();
        $rec = $db->select();
        while ($row = $rec->fetch()) {
            $row['soft_filename'] = getFileName($row['soft_filename']);
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
            if ($soft['soft_type'] == 1) {
                if (($msg = user::buy_soft($sid)) !== true) {
                    auth::_error($msg, url('/'));
                    return;
                }
            } else {
                if (!isset($this->userMsg['auth']['index->area'])) {
                    _404();
                    return '';
                }
            }
            header('Content-Description: File Transfer');
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $soft['soft_filename'] . '"');
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

    public static function applyUpload() {
        $retJson = ['code' => -1];
        if (db::table('soft_list')->where('soft_uid', $_COOKIE['uid'])->where('soft_type', 3)->find()) {
            $retJson['msg'] = '您有未上传完毕的任务(暂时未提供恢复,请直接找管理,下次更新╮(￣⊿￣)╭)';
            return $retJson;
        }
        if (!_post('name')) {
            $retJson['msg'] = '软件名不能为空';
            return $retJson;
        }
        if (!_post('filename')) {
            $retJson['msg'] = '文件名不能为空';
            return $retJson;
        }
        if (ceil(($price = _post('price'))) <= 0) {
            $price = 0;
        }
        if (!($sort_id = _post('sort_id'))) {
            $retJson['msg'] = '请选择分区';
            return $retJson;
        }
        if (!db::table('sort')->where('sort_id', $sort_id)->find()) {
            $retJson['msg'] = '分区不存在';
            return $retJson;
        }
        if (!is_file('static/res/images/' . _post('logo'))) {
            $retJson['msg'] = '图片不存在';
            return $retJson;
        }

        db::table('soft_list')->insert([
            'soft_name' => _post('name'),
            'soft_filename' => _post('filename'),
            'soft_exp' => _post('exp'),
            'soft_logo' => _post('logo'),
            'soft_path' => '',
            'soft_price' => $price,
            'soft_sort_id' => $sort_id,
            'soft_type' => 3,
            'soft_time' => time(),
            'soft_uid' => $_COOKIE['uid'],
        ]);
        $retJson = ['code' => 0, 'msg' => 'success', 'id' => db::table()->lastinsertid()];
        return $retJson;
    }

    public function upload_image() {
        $json ['code'] = -2;
        $json ['msg'] = "系统错误";
        if (isset ($_FILES ['upfile'] ['tmp_name'])) {
            if (is_uploaded_file($_FILES ['upfile'] ['tmp_name'])) {
                $upfile = $_FILES ["upfile"];
                $tmp_name = $upfile ["tmp_name"]; // 上传文件的临时存放路径
                $error = $upfile ['error']; // 上传后系统返回的值
                if ($error == 0) {
                    if ($format = isImg($tmp_name)) {
                        $tmpFile = filePart(getFileName($upfile['name']));
                        $filename = $tmpFile[0] . '_' . time() . '.' . $tmpFile[1];
                        $destination = time2path('static/res/images/', $timePath) . $filename;
                        $filename = $timePath . $filename;
                        if (!(file_exists($destination))) {
                            move_uploaded_file($tmp_name, $destination);
                        }
                        $json ['code'] = 0;
                        $json ['msg'] = "上传成功";
                        $json ['url'] = $filename;
                    } else {
                        $json ['code'] = -1;
                        $json ['msg'] = "上传失败";
                    }
                } else {
                    $json ['code'] = $error;
                    $json ['msg'] = '文件上传失败';
                }
            }
            return json($json);
        }
        return $json;
    }

    public function getSortList($sid = 0, $param = '') {
        if ($sid) {
            $result = db::table('sort')->field('sort_id,sort_name as name')->where('sort_fid', $sid)->select();
        } else {
            $result = db::table('sort')->field('sort_id,sort_name as name')->where('sort_fid', 0)->select();
        }
        $retRows = [];
        while ($row = $result->fetch()) {
            $tmp = ['sid' => $row['sort_id'], 'name' => $param . $row['name']];
            $retRows[] = $tmp;
            $retRows = array_merge($retRows, $this->getSortList($row['sort_id'], $param . $row['name'] . '=>'));
        }
        if (!$sid) {
            return ['code' => 0, 'msg' => 'success', 'rows' => $retRows];
        }
        return $retRows;
    }
}