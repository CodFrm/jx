<?php
/**
 *============================
 * author:Farmer
 * time:2017/11/16
 * blog:blog.icodef.com
 * function:函数库
 *============================
 */

use icf\lib\db;

function get_nav() {
    $rec = db::table('nav')->where('nav_father', 0)->select();
    $rows = [];
    while ($row = $rec->fetch()) {
        $row['sub'] = getSubNav($row['nav_id']);
        $rows[] = $row;
    }
    return $rows;
}

function getSubNav($nav_id = 0) {
    $rec = db::table('nav')->where('nav_father', $nav_id)->select();
    $rows = [];
    while ($row = $rec->fetch()) {
        $row['sub'] = getSubNav($row['nav_id']);
        $rows[] = $row;
    }
    return $rows;
}

function outSubNavHtml($nav) {
    if ($nav == []) {
        return '';
    }
    $html = '<div class="nav-sub">';
    foreach ($nav as $key => $value) {
        $html .= '<div class="nav-sub-item"><a href="' . __HOME_ . '/' . $value['nav_url'] . '"' . ">{$value['nav_title']}</a>";
        $html .= outSubNavHtml($value['sub']);
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

function outBreadcrumbHtml($sid) {
    $html = '';
    while ($row = db::table('sort')->where(['sort_id' => $sid])->find()) {
        $html = '<a href="' . __HOME_ . '/sort/' . $row['sort_id'] . '">' . $row['sort_name'] . '</a>' . '/' . $html;
        $sid = $row['sort_fid'];
    }
    $html = substr($html, 0, strlen($html) - 1);
    return (empty($html) ? '<a href="' . __HOME_ . '">首页</a>' : $html);
}

