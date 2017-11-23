<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/23
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace app\admin\ctrl;

use app\admin\auth;
use icf\lib\db;

class index extends auth {
    public function index() {

    }

    public function add() {

    }

    /**
     * 审核文件
     * @author Farmer
     */
    public function audit() {
        $hDir = opendir(__ROOT_ . '/static/res/tmp');
        $fileList = [];
        while ($row = readdir($hDir)) {
            if ($row == '.' || $row == '..') continue;
            $tmp = getPathSoft('tmp/' . $row);
            if (isset($tmp['sid'])) {
                $tmp['soft_time'] = date('Y-m-d H:i:s', $tmp['soft_time']);
            }
            $tmp['name'] = $row;
            $fileList[] = $tmp;
        }
        view()->assign('list', $fileList);
        view()->display();
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

    public function operAudit() {
        $retJson = ['code' => -1, 'msg' => '系统错误'];
        if (_get('action') == 'accept') {
            if (!($soft_name = _get('soft_name'))) {
                $retJson['msg'] = '软件名不能为空';
                return $retJson;
            }
            if (!is_file('static/res/images/' . _get('soft_logo'))) {
                $retJson['msg'] = '图片不存在';
                return $retJson;
            }
            $filePath = 'static/res/' . _get('soft_path');
            if (!is_file($filePath)) {
                $retJson['msg'] = '文件不存在';
                return $retJson;
            }
            set_time_limit(0);
            $tmpFile = getFileName($filePath);
            $newFile = time2path('static/res/soft/', $timePath) . $tmpFile;
            @copy($filePath, $newFile);
            @unlink($filePath);
            $newFile = 'soft/' . $timePath . $tmpFile;
            if (_get('sid')) {
                db::table('soft_list')->where('sid', _get('sid'))
                    ->update([
                        'soft_name' => _get('soft_name'),
                        'soft_exp' => _get('soft_exp'),
                        'soft_logo' => _get('soft_logo'),
                        'soft_path' => $newFile,
                        'soft_type' => 1,
                        'soft_time' => time()
                    ]);
            } else {
                db::table('soft_list')
                    ->insert([
                        'soft_name' => _get('soft_name'),
                        'soft_exp' => _get('soft_exp'),
                        'soft_logo' => _get('soft_logo'),
                        'soft_path' => $newFile,
                        'soft_type' => 1,
                        'soft_time' => time(),
                        'soft_uid'=>0,
                    ]);
            }
            $retJson['code'] = 0;
            $retJson['msg'] = '成功';
            return $retJson;
        } else if (_get('action') == 'unaccept') {

        }
        return $retJson;
    }
}
