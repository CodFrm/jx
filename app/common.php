<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/21
 * blog:blog.icodef.com
 * function:公共函数库
 *============================
 */

use icf\lib\db;

/**
 * 判断变量是否设置
 * @author Farmer
 * @param $array
 * @param $mode
 * @return bool
 */
function isExist($array, $mode, &$data = '') {
    foreach ($mode as $key => $value) {
        if (is_string($value)) {
            if (empty($array[$key])) {
                return $value;
            }
        } else if (is_array($value)) {
            if (empty($array[$key])) {
                return $value['msg'];
            }
            if (!empty($value['regex'])) {//正则
                if (!preg_match($value['regex'][0], $array[$key])) {
                    return $value['regex'][1];
                }
            }
            if (!empty($value['func'])) {//对函数处理
                $tmpFunction = $value['func'];
                $funName = $value['func'][0];
                $parameter = array();
                unset($tmpFunction[0]);
                $parameter[] = $array[$key];
                foreach ($tmpFunction as $v) {
                    $parameter[] = $array[$v];
                }
                $tmpValue = call_user_func_array($funName, $parameter);
                if ($tmpValue !== true) {
                    return $tmpValue;
                }
            }
            if (!empty($value['enum'])) {//判断枚举类型
                if (!in_array($array[$key], $value['enum'][0])) {
                    return $value['enum'][1];
                }
            }
            if (!empty($value['sql'])) {//将其复制给sql插入数组
                $data[$value['sql']] = $array[$key];
            }
        }
    }
    return true;
}

/**
 * 取随机字符串
 * @author Farmer
 * @param $length
 * @param $type
 * @return string
 */
function getRandString($length, $type = 2) {
    $randString = '1234567890qwwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHHJKLZXCVBNM';
    $retStr = '';
    for ($n = 0; $n < $length; $n++) {
        $retStr .= substr($randString, rand(0, 9 + $type * 24), 1);
    }
    return $retStr;
}


function isLogin() {
    if ($uid = _cookie('uid') && $token = _cookie('token')) {
        return verifyToken(_cookie('uid'), _cookie('token'));
    }
    return false;
}

/**
 * 获取token
 * @author Farmer
 * @param $uid
 * @return string
 */
function getToken($uid) {
    $token = getRandString(8, 2) . time();
    db::table('token')->insert(['uid' => $uid, 'token' => $token, 'time' => time()]);
    db::table('token')->where('time<' . (time() - 604800))->delete();
    return $token;
}

/**
 * 验证token
 * @author Farmer
 * @param $uid
 * @param $token
 * @return bool
 */
function verifyToken($uid, $token) {
    $where = ['token' => $token, 'uid' => $uid];
    $tokenMsg = db::table('token')->where($where)->find();
    if (!$tokenMsg) {
        return false;
    } else if ($tokenMsg['time'] + 604800 < time()) {
        db::table('token')->where($where)->delete($where);
        return false;
    }
    db::table('token')->where($where)->update(['time' => time()]);
    return true;
}

/**
 * 判断文件是不是图片
 *
 * @author Farmer
 * @param string $fileName
 * @return bool
 *
 */
function isImg($fileName) {
    $file = fopen($fileName, "rb");
    $bin = fread($file, 2); // 只读2字节

    fclose($file);
    $strInfo = @unpack("C2chars", $bin);
    $typeCode = intval($strInfo ['chars1'] . $strInfo ['chars2']);
    $fileType = '';
    if ($typeCode == 255216 /*jpg*/ || $typeCode == 7173 /*gif*/ || $typeCode == 13780 /*png*/) {
        return $typeCode;
    } else {
        return false;
    }
}


/**
 * 分割文件
 * @author Farmer
 * @param $filename
 * @return array
 */
function filePart($filename) {
    $arr = explode('.', $filename, strrpos($filename, '.') - 1);
    if (sizeof($arr) < 2) {
        $arr[] = 'png';
    }
    return $arr;
}


/**
 * 通过路径获取文件名
 * @author Farmer
 * @param $path
 * @return bool|string
 */
function getFileName($path) {
    if (($pos = strrpos($path, '/')) !== false) {
        $pos++;
    }
    $s = substr($path, $pos);
    return $s;
}

/**
 * sid获取软件信息
 * @author Farmer
 * @param $sid
 * @return mixed
 */
function sidSoft($sid) {
    return db::table('soft_list')->where('sid', $sid)->find();
}


function sendEmail($to, $title, $content) {
    $smtpserver = "smtp.exmail.qq.com";//SMTP服务器
    $smtpserverport = 465;//SMTP服务器端口
    $smtpusermail = "love@icodef.com";//SMTP服务器的用户邮箱
    $smtpemailto = $to;//发送给谁
    $smtpuser = "love@icodef.com";//SMTP服务器的用户帐号(或填写new2008oh@126.com，这项有些邮箱需要完整的)
    $emailname = "计协下载站";
    $smtppass = "VAhBsdKFPUf53QZc";//SMTP服务器的用户密码
    $mailtitle = $title;//邮件主题
    $mailcontent = $content;//邮件内容
    $smtp = new icf\lib\smtp();
    $smtp->setName($emailname);
    $smtp->setServer($smtpserver, $smtpusermail, $smtppass, $smtpserverport, true); //设置smtp服务器，到服务器的SSL连接
    $smtp->setFrom($smtpuser); //设置发件人
    $smtp->setReceiver($smtpemailto); //设置收件人，多个收件人，调用多次
    $smtp->setMail($mailtitle, $mailcontent); //设置邮件主题、内容
    return $smtp->sendMail(); //发送
}


/**
 * 资源是否存在
 * @author Farmer
 * @param $path
 * @param int $type
 * @return bool
 */
function is_res($path, $type = 1) {
    if ($type == 1) {
        return is_file('static/res/images/' . $path);
    }
    return '文件不存在';
}