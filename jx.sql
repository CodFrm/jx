/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : jx

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-11-23 11:40:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jx_integral_change
-- ----------------------------
DROP TABLE IF EXISTS `jx_integral_change`;
CREATE TABLE `jx_integral_change` (
  `ic_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ic_oper_type` int(255) NOT NULL,
  `ic_detail` varchar(255) NOT NULL,
  `ic_number` int(11) NOT NULL,
  `ic_time` bigint(20) NOT NULL,
  `ic_type` int(11) NOT NULL,
  PRIMARY KEY (`ic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_integral_change
-- ----------------------------

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
  `soft_time` bigint(20) unsigned NOT NULL COMMENT '上传的时间',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_soft_list
-- ----------------------------
INSERT INTO `jx_soft_list` VALUES ('1', '软件名', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('2', '软件名1', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('3', '软件名12', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('4', '软件名123', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('5', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('6', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('7', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('8', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('9', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('10', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('11', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('12', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('13', 'tim', '2333', '', 'tim.exe', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('14', '电影', '看看电影', '', 'movie/jrkl.mp4', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('15', 'putty3', '说明', 'putty.png', 'putty.exe', '1', '101011');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_sort
-- ----------------------------

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
INSERT INTO `jx_token` VALUES ('1', 'cawIHl', '1511262853');
INSERT INTO `jx_token` VALUES ('1', 'IbbckGeu1511267247', '1511267247');
INSERT INTO `jx_token` VALUES ('1', 'vGlAWw0F1511267260', '1511269810');
INSERT INTO `jx_token` VALUES ('1', 'Uson32iG1511269828', '1511269828');
INSERT INTO `jx_token` VALUES ('1', 'x0wR0ceA1511269851', '1511338853');
INSERT INTO `jx_token` VALUES ('1', 'lorHdO0z1511338857', '1511339733');
INSERT INTO `jx_token` VALUES ('1', 'i6Okakpk1511339845', '1511339845');
INSERT INTO `jx_token` VALUES ('1', 'aGtGR9Ko1511339895', '1511408061');

-- ----------------------------
-- Table structure for jx_user
-- ----------------------------
DROP TABLE IF EXISTS `jx_user`;
CREATE TABLE `jx_user` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `integral` double unsigned NOT NULL COMMENT '用户积分',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_user
-- ----------------------------
INSERT INTO `jx_user` VALUES ('1', 'farmer', '幻令', '4c19f05605df904c8765ff571449e355', '', 'avatar.png', '10');
