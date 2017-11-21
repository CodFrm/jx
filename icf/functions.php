<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:框架的内置函数
 *============================
 */

//全局函数
global $g;

/**
 * 全局数据
 * @param $key
 * @param $value
 * @return bool
 */
function _global($key, $value = null) {
    global $g;
    if (isset($g[$key])) {
        if ($value === null) {
            return $g[$key];
        } else {
            $g[$key] = $value;
            return $value;
        }
    } else {
        $g[$key] = $value;
    }
    return true;
}

/**
 * 读取配置
 * @param $key
 * @return mixed
 */
function _config($key) {
    return _global('config')[$key];
}

/**
 * 读取get
 * @author Farmer
 * @param $key
 * @param null $default
 * @return null
 */
function _get($key, $default = false) {
    return _readValue($_GET, $key, $default);
}

/**
 * 读取POST
 * @author Farmer
 * @param $key
 * @param bool $default
 * @return bool
 */
function _post($key, $default = false) {
    return _readValue($_POST, $key, $default);
}

/**
 * 读取COOKIE
 * @author Farmer
 * @param $key
 * @param bool $default
 * @return bool
 */
function _cookie($key, $default = false) {
    return _readValue($_COOKIE, $key, $default);
}

function _readValue($data, $key, $default = false) {
    if (isset($data[$key])) {
        return $data[$key];
    }
    return $default;
}

/**
 * Json 编码 对于中文处理 仅支持php5.4以后的版本
 *
 * @author Farmer
 * @param string $str
 * @return string
 */
function json($str) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($str, JSON_UNESCAPED_UNICODE);
}


function _404() {
    echo '404';
}

/**
 * 获取变量
 *
 * @author Farmer
 * @param string $var
 * @return mixed
 */
function input($var, $val = null) {
    $arrVar = explode('.', $var);
    if (sizeof($arrVar) <= 1) {
        $ret = _global($var, $val);
    } else {
        $ret = _global($arrVar [0], $val);
        unset ($arrVar [0]);
        foreach ($arrVar as $value) {
            if (!isset ($ret [$value])) {
                return false;
            }
            $ret = $ret [$value];
        }
    }
    return $ret;
}

function view() {
    return new \icf\lib\view();
}

/**
 * 获取客户ip
 * @author Farmer
 * @return string
 */
function getip() {
    $arr_ip_header = array(
        'HTTP_CDN_SRC_IP',
        'HTTP_PROXY_CLIENT_IP',
        'HTTP_WL_PROXY_CLIENT_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
    );
    $client_ip = 'unknown';
    foreach ($arr_ip_header as $key) {
        if (!empty($_SERVER[$key]) && strtolower($_SERVER[$key]) != 'unknown') {
            $client_ip = $_SERVER[$key];
            break;
        }
    }
    return $client_ip;
}

/**
 * 获取请求地址
 * @author Farmer
 * @return string
 */
function getReqUrl(){
    return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
}

/**
 * 生成访问URL
 * @author Farmer
 * @param string $action
 * @param string $param
 * @return string
 */
function url($action='',$param='') {
    preg_match_all( '/([\w]+)/', $action, $arrMatch);
    $url='';
    foreach ($arrMatch[0] as $value){
        $url.=('/'.$value);
    }
    return __HOME_.$url.($param?('?'.$param):'');
}