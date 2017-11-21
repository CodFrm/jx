<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/21
 * blog:blog.icodef.com
 * function:公共函数库
 *============================
 */


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
