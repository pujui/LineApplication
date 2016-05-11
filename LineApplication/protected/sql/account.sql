-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主機: localhost
-- 建立日期: Nov 23, 2013, 06:59 AM
-- 伺服器版本: 5.0.51
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 資料庫: `account`
-- 
CREATE DATABASE `account` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `account`;

-- --------------------------------------------------------

-- 
-- 資料表格式： `detail`
-- 

CREATE TABLE `detail` (
  `detail_id` bigint(20) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL COMMENT '帳務群組',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '建立者',
  `detail_title` varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '項目名稱',
  `is_credit` tinyint(1) unsigned NOT NULL COMMENT '1 credit, 0 debit',
  `credit` int(10) unsigned NOT NULL COMMENT '入帳',
  `debit` int(10) unsigned NOT NULL COMMENT '出帳',
  `receipt` varchar(20) collate utf8_unicode_ci NOT NULL COMMENT '發票號碼',
  `receipt_date` date NOT NULL COMMENT '發票日期',
  `receipt_memo` text collate utf8_unicode_ci NOT NULL COMMENT 'memo',
  `owner` bigint(20) unsigned NOT NULL COMMENT '擁有者',
  `is_del` tinyint(1) unsigned NOT NULL COMMENT '1: 刪除',
  `createtime` datetime NOT NULL COMMENT '建立時間',
  `updatetime` datetime NOT NULL COMMENT '更新時間',
  PRIMARY KEY  (`detail_id`),
  KEY `user_id` (`user_id`),
  KEY `receipt` (`receipt`),
  KEY `receipt_date` (`receipt_date`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='帳務明細' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 資料表格式： `detail_group`
-- 

CREATE TABLE `detail_group` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `group_title` varchar(40) collate utf8_unicode_ci NOT NULL COMMENT '群組名稱',
  `is_del` tinyint(3) unsigned NOT NULL COMMENT '1: 刪除',
  `createtime` datetime NOT NULL,
  `updatetime` datetime NOT NULL,
  PRIMARY KEY  (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='帳務群組' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- 資料表格式： `group_member`
-- 

CREATE TABLE `group_member` (
  `group_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `createtime` datetime NOT NULL,
  PRIMARY KEY  (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='群組名單';
