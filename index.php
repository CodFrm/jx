<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:入口文件
 *============================
 */

require_once 'icf/loader.php';

header('Content-type: text/html; charset=utf-8');
//进入框架的入口
define('__ROOT_',__DIR__);
define('__DEFAULT_MODEL_','index');

icf\index::run();