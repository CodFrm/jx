<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/7
 * blog:blog.icodef.com
 * function:配置文件
 *============================
 */

return [
    'debug' => true,
    'db' => [
        'type' => 'mysql',
        'server' => 'localhost',
        'port' => 3306,
        'db' => 'jx',
        'user' => 'root',
        'pwd' => '',
        'prefix' => 'jx_'
    ],
    //模块,控制器,操作 默认关键字
    'model_key' => 'm',
    'ctrl_key' => 'c',
    'action_key' => 'a',
    'route' => [
        '*' => [
            'debug/{test}' => 'index->debug'
        ],
        'get' => [
            'd/{sid}' => 'index->api->download',
            'admin'=>'admin->index->index',
            'sort/{sort_id}'=>'index->index->index'
        ]
    ],
    'tpl_suffix' => 'html',
    'public' => 'public',
    'log' => true,
    'pwd_deal' => 'df@#5!51Dw'
];