/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : jx

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-11-21 21:42:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jx_soft_list
-- ----------------------------
DROP TABLE IF EXISTS `jx_soft_list`;
CREATE TABLE `jx_soft_list` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `soft_name` varchar(255) NOT NULL COMMENT '软件名',
  `soft_exp` text NOT NULL COMMENT '软件说明',
  `soft_logo` varchar(255) NOT NULL,
  `soft_path` varchar(255) NOT NULL,
  `soft_uid` int(11) NOT NULL COMMENT '上传者的uid,用户ID',
  `soft_time` int(11) NOT NULL COMMENT '上传的时间',
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
INSERT INTO `jx_soft_list` VALUES ('13', '软件名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('14', '软件414名1423', '说明', '', '', '1', '101011');
INSERT INTO `jx_soft_list` VALUES ('15', 'putty3', '说明', 'putty.png', 'putty.exe', '1', '101011');

-- ----------------------------
-- Table structure for jx_token
-- ----------------------------
DROP TABLE IF EXISTS `jx_token`;
CREATE TABLE `jx_token` (
  `uid` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_token
-- ----------------------------
INSERT INTO `jx_token` VALUES ('1', 'cawIHl', '1511262853');
INSERT INTO `jx_token` VALUES ('1', 'IbbckGeu1511267247', '1511267247');
INSERT INTO `jx_token` VALUES ('1', 'vGlAWw0F1511267260', '1511269810');
INSERT INTO `jx_token` VALUES ('1', 'Uson32iG1511269828', '1511269828');
INSERT INTO `jx_token` VALUES ('1', 'x0wR0ceA1511269851', '1511270920');

-- ----------------------------
-- Table structure for jx_user
-- ----------------------------
DROP TABLE IF EXISTS `jx_user`;
CREATE TABLE `jx_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jx_user
-- ----------------------------
INSERT INTO `jx_user` VALUES ('1', 'farmer', '幻令', '4c19f05605df904c8765ff571449e355', '', 'avatar.png');
