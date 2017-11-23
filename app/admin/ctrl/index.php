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
        $fileList=[];
        while ($row=readdir($hDir)){
            if ($row=='.' || $row=='..')continue;
            $tmp=getPathSoft('tmp/'.$row);
            if (isset($tmp['sid'])) {
                $tmp['soft_time'] = date('Y-m-d H:i:s', $tmp['soft_time']);
            }
            $tmp['name']=$row;
            $fileList[]=$tmp;
        }
        view()->assign('list',$fileList);
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
                    if (isImg($tmp_name)) {
                        $md5 = md5_file($tmp_name);
                        $destination = 'static/res/images/' . $md5;
                        if (!(file_exists($destination))) {
                            move_uploaded_file($tmp_name, $destination);
                        }
                        $json ['code'] = 0;
                        $json ['msg'] = "上传成功";
                        $json ['url'] = $md5;
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
}
