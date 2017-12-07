/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : jx

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-12-07 17:14:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jx_area_manager
-- ----------------------------
DROP TABLE IF EXISTS `jx_area_manager`;
CREATE TABLE `jx_area_manager` (
  `sort_id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_area_manager
-- ----------------------------
INSERT INTO `jx_area_manager` VALUES ('1', '1');
INSERT INTO `jx_area_manager` VALUES ('4', '1');

-- ----------------------------
-- Table structure for jx_auth
-- ----------------------------
DROP TABLE IF EXISTS `jx_auth`;
CREATE TABLE `jx_auth` (
  `auth_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `auth_interface` varchar(255) NOT NULL COMMENT '权限接口',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_auth
-- ----------------------------
INSERT INTO `jx_auth` VALUES ('1', 'index->index', '首页浏览');
INSERT INTO `jx_auth` VALUES ('2', 'index->api->getSoftList', '列表获取');
INSERT INTO `jx_auth` VALUES ('3', 'index->api->download', '下载权限');
INSERT INTO `jx_auth` VALUES ('4', 'admin', '管理员权限');
INSERT INTO `jx_auth` VALUES ('5', 'index->user', '用户页面');
INSERT INTO `jx_auth` VALUES ('6', 'index->area', '版主权限');
INSERT INTO `jx_auth` VALUES ('7', '???', '还没想好,emm');

-- ----------------------------
-- Table structure for jx_group
-- ----------------------------
DROP TABLE IF EXISTS `jx_group`;
CREATE TABLE `jx_group` (
  `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '群组id',
  `group_name` varchar(255) NOT NULL,
  `group_auth` int(11) NOT NULL COMMENT '权限级别',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_group
-- ----------------------------
INSERT INTO `jx_group` VALUES ('1', '管理员', '10');
INSERT INTO `jx_group` VALUES ('2', '游客', '1');
INSERT INTO `jx_group` VALUES ('3', '会员', '2');
INSERT INTO `jx_group` VALUES ('4', '分区管理Max', '7');
INSERT INTO `jx_group` VALUES ('5', '分区管理', '6');

-- ----------------------------
-- Table structure for jx_groupauth
-- ----------------------------
DROP TABLE IF EXISTS `jx_groupauth`;
CREATE TABLE `jx_groupauth` (
  `group_id` int(11) NOT NULL,
  `auth_id` int(11) NOT NULL,
  KEY `auth_id` (`auth_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_groupauth
-- ----------------------------
INSERT INTO `jx_groupauth` VALUES ('2', '1');
INSERT INTO `jx_groupauth` VALUES ('2', '2');
INSERT INTO `jx_groupauth` VALUES ('3', '3');
INSERT INTO `jx_groupauth` VALUES ('1', '4');
INSERT INTO `jx_groupauth` VALUES ('3', '5');
INSERT INTO `jx_groupauth` VALUES ('7', '6');
INSERT INTO `jx_groupauth` VALUES ('4', '6');

-- ----------------------------
-- Table structure for jx_integral_change
-- ----------------------------
DROP TABLE IF EXISTS `jx_integral_change`;
CREATE TABLE `jx_integral_change` (
  `ic_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ic_uid` bigint(20) unsigned NOT NULL,
  `ic_oper_type` int(255) NOT NULL,
  `ic_detail` varchar(255) NOT NULL,
  `ic_number` double(10,4) NOT NULL,
  `ic_time` bigint(20) NOT NULL,
  `ic_param_id` bigint(11) NOT NULL,
  `ic_over_integral` bigint(20) NOT NULL,
  PRIMARY KEY (`ic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_integral_change
-- ----------------------------
INSERT INTO `jx_integral_change` VALUES ('2', '1', '1', '购买putty3花费1积分', '-1.0000', '1512615682', '15', '6');

-- ----------------------------
-- Table structure for jx_log
-- ----------------------------
DROP TABLE IF EXISTS `jx_log`;
CREATE TABLE `jx_log` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_uid` bigint(20) unsigned NOT NULL,
  `log_msg` text NOT NULL,
  `log_time` bigint(20) unsigned NOT NULL,
  `log_type` int(11) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_log
-- ----------------------------

-- ----------------------------
-- Table structure for jx_nav
-- ----------------------------
DROP TABLE IF EXISTS `jx_nav`;
CREATE TABLE `jx_nav` (
  `nav_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nav_title` varchar(255) NOT NULL,
  `nav_url` varchar(255) NOT NULL,
  `nav_father` int(10) unsigned NOT NULL,
  PRIMARY KEY (`nav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_nav
-- ----------------------------
INSERT INTO `jx_nav` VALUES ('1', '首页', '.', '0');
INSERT INTO `jx_nav` VALUES ('2', '软件', 'sort/1', '0');
INSERT INTO `jx_nav` VALUES ('3', '设计软件', 'sort/10', '2');
INSERT INTO `jx_nav` VALUES ('4', '编程软件', 'sort/3', '2');

-- ----------------------------
-- Table structure for jx_soft_list
-- ----------------------------
DROP TABLE IF EXISTS `jx_soft_list`;
CREATE TABLE `jx_soft_list` (
  `sid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `soft_name` varchar(255) NOT NULL COMMENT '软件名',
  `soft_exp` text NOT NULL COMMENT '软件说明',
  `soft_logo` varchar(255) NOT NULL,
  `soft_path` varchar(255) NOT NULL,
  `soft_uid` bigint(20) unsigned NOT NULL COMMENT '上传者的uid,用户ID',
  `soft_price` double(10,4) NOT NULL DEFAULT '1.0000',
  `soft_time` bigint(20) unsigned NOT NULL COMMENT '上传的时间',
  `soft_type` int(11) unsigned NOT NULL COMMENT '软件状态',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_soft_list
-- ----------------------------
INSERT INTO `jx_soft_list` VALUES ('1', '软件名', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('2', '软件名1', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('3', '软件名12', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('4', '软件名123', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('5', '软件名1423', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('6', '软件名1423', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('7', '软件名1423', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('8', '软件名1423', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('9', '软件名1423', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('10', '软件名1423', '说明', '', '', '1', '0.0000', '101011', '0');
INSERT INTO `jx_soft_list` VALUES ('11', '1232131', '说明', '2017/11/TIM截图20171123210523_1511447100.png', 'soft/2017/11/PuTTY_0.67.0.0.exe', '1', '0.0000', '1511447102', '1');
INSERT INTO `jx_soft_list` VALUES ('12', '软件名1423', '说明', '2017/11/TIM截图20171123203233_1511445287.png', 'soft/2017/11/PuTTY_0.67.0.0.exe', '1', '0.0000', '1511445535', '1');
INSERT INTO `jx_soft_list` VALUES ('13', 'tim', '2333', '32', 'tim.exe', '1', '0.0000', '101011', '1');
INSERT INTO `jx_soft_list` VALUES ('14', '电影', '看看电影', '123', 'movie/jrkl.mp4', '1', '0.0000', '101011', '1');
INSERT INTO `jx_soft_list` VALUES ('15', 'putty3', '说明', 'putty.png', 'putty.exe', '1', '0.0000', '101011', '1');
INSERT INTO `jx_soft_list` VALUES ('16', '校园网', '2333333', '2017/11/TIM截图20171123210523_1511445825.png', 'soft/2017/11/openvpn-stushare.exe', '1', '0.0000', '1511445829', '1');

-- ----------------------------
-- Table structure for jx_soft_sort
-- ----------------------------
DROP TABLE IF EXISTS `jx_soft_sort`;
CREATE TABLE `jx_soft_sort` (
  `soft_id` bigint(20) unsigned NOT NULL,
  `sort_id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_soft_sort
-- ----------------------------
INSERT INTO `jx_soft_sort` VALUES ('15', '10');
INSERT INTO `jx_soft_sort` VALUES ('13', '2');
INSERT INTO `jx_soft_sort` VALUES ('15', '5');

-- ----------------------------
-- Table structure for jx_sort
-- ----------------------------
DROP TABLE IF EXISTS `jx_sort`;
CREATE TABLE `jx_sort` (
  `sort_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '目录id',
  `sort_name` varchar(255) NOT NULL COMMENT '目录名',
  `sort_exp` varchar(255) NOT NULL COMMENT '目录说明',
  `sort_fid` bigint(20) unsigned NOT NULL COMMENT '父目录id',
  PRIMARY KEY (`sort_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_sort
-- ----------------------------
INSERT INTO `jx_sort` VALUES ('1', '软件', '软件区', '0');
INSERT INTO `jx_sort` VALUES ('2', '电脑软件', '', '1');
INSERT INTO `jx_sort` VALUES ('3', '编程软件', '', '1');
INSERT INTO `jx_sort` VALUES ('4', '游戏区', '游戏区', '0');
INSERT INTO `jx_sort` VALUES ('5', '单机游戏', '', '4');
INSERT INTO `jx_sort` VALUES ('6', '网游', '', '4');
INSERT INTO `jx_sort` VALUES ('7', '电影区', '电影', '0');
INSERT INTO `jx_sort` VALUES ('8', '喜剧', '', '7');
INSERT INTO `jx_sort` VALUES ('9', '恐怖电影', '', '7');
INSERT INTO `jx_sort` VALUES ('10', '设计软件', '', '1');

-- ----------------------------
-- Table structure for jx_token
-- ----------------------------
DROP TABLE IF EXISTS `jx_token`;
CREATE TABLE `jx_token` (
  `uid` bigint(20) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `time` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_token
-- ----------------------------
INSERT INTO `jx_token` VALUES ('1', 'vIb8Hswo1512528696', '1512545511');
INSERT INTO `jx_token` VALUES ('1', 'Stz0WWI61512614578', '1512638005');

-- ----------------------------
-- Table structure for jx_user
-- ----------------------------
DROP TABLE IF EXISTS `jx_user`;
CREATE TABLE `jx_user` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `integral` double(10,4) unsigned NOT NULL COMMENT '用户积分',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_user
-- ----------------------------
INSERT INTO `jx_user` VALUES ('1', 'farmer', '5ed87229c3bf49789b879507976bbd38', 'code.farmer@qq.com', '2017/11/TIM截图20171123210523_1511447100.png', '6.0000');

-- ----------------------------
-- Table structure for jx_usergroup
-- ----------------------------
DROP TABLE IF EXISTS `jx_usergroup`;
CREATE TABLE `jx_usergroup` (
  `uid` bigint(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `expire_time` int(11) NOT NULL DEFAULT '-1' COMMENT '用户组到期时间 永久为-1',
  KEY `uid` (`uid`),
  KEY `user_group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_usergroup
-- ----------------------------
INSERT INTO `jx_usergroup` VALUES ('1', '2', '-1');
INSERT INTO `jx_usergroup` VALUES ('1', '3', '-1');
INSERT INTO `jx_usergroup` VALUES ('1', '1', '-1');
INSERT INTO `jx_usergroup` VALUES ('1', '4', '-1');
