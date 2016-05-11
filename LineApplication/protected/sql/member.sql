-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主機: localhost
-- 建立日期: Nov 23, 2013, 06:58 AM
-- 伺服器版本: 5.0.51
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 資料庫: `member`
-- 
CREATE DATABASE `member` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `member`;

-- --------------------------------------------------------

-- 
-- 資料表格式： `user`
-- 

CREATE TABLE `user` (
  `user_id` bigint(20) unsigned NOT NULL auto_increment COMMENT 'user id',
  `account` varchar(80) collate utf8_unicode_ci NOT NULL COMMENT 'user email 32(username)+@+63(網域名稱)+com+1個dot，這樣一共100個字元',
  `password` varchar(40) collate utf8_unicode_ci NOT NULL COMMENT 'user password',
  `level` varchar(10) collate utf8_unicode_ci NOT NULL,
  `private_key` varchar(10) collate utf8_unicode_ci NOT NULL,
  `last_login_time` datetime NOT NULL COMMENT 'last login time',
  `createtime` datetime NOT NULL COMMENT 'creat time',
  `updatetime` datetime NOT NULL COMMENT 'update time',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user  account data' AUTO_INCREMENT=3 ;

-- 
-- 列出以下資料庫的數據： `user`
-- 

INSERT INTO `user` VALUES (1, 'spread.moda@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', '', 'zxc', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `user` VALUES (2, 'larry@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', '', 'zxc', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- 資料表格式： `user_log`
-- 

CREATE TABLE `user_log` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(20) collate utf8_unicode_ci NOT NULL,
  `params` text collate utf8_unicode_ci NOT NULL,
  `createtime` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user action log' AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `user_log`
-- 

