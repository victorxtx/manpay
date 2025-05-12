-- MySQL dump 10.13  Distrib 8.0.40, for Linux (x86_64)
--
-- Host: localhost    Database: platform
-- ------------------------------------------------------
-- Server version	8.0.40-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `platform`
--

/*!40000 DROP DATABASE IF EXISTS `platform`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `platform` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `platform`;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `oid` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '平台视角顺序订单ID',
  `pid` int unsigned DEFAULT NULL,
  `trade_no` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '平台视角统一订单字符串',
  `out_trade_no` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '发起支付商户自定订单字符串',
  `qr_file` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '该订单发起后向客户展示的二维码文件名',
  `name` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `money` mediumint DEFAULT NULL COMMENT '订单提交金额（玩家将会收到的虚拟币值，单位：分）',
  `random_discount_rate` decimal(5,4) unsigned DEFAULT NULL COMMENT '订单提交时，触发的随机折扣率。x / 10000。取值范围在 config.php 中定义',
  `commission_fee_rate_actual` decimal(5,4) DEFAULT NULL COMMENT '订单提交时，平台的抽成费率',
  `actual_amount` mediumint DEFAULT NULL COMMENT '实付金额（单位：分）。',
  `sitename` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notify_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '向商户网站发起的异步成功支付通知（由商户程序员填写，商户管理员、平台管理员调用）',
  `return_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '玩家成功支付后被导向的商户网站页面（由商户程序员填写，商户管理员、平台管理员调用）',
  `order_place_time` datetime DEFAULT NULL,
  `pay_status` tinyint DEFAULT '0',
  `pay_time` datetime DEFAULT NULL,
  `payer` int unsigned DEFAULT NULL,
  `notify_status` tinyint DEFAULT '0',
  `notify_time` datetime DEFAULT NULL,
  `notifier` int unsigned DEFAULT NULL,
  `discarded` tinyint DEFAULT '0',
  PRIMARY KEY (`oid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settle`
--

DROP TABLE IF EXISTS `settle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settle` (
  `sid` bigint NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `before_balance` int DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `after_balance` int DEFAULT NULL,
  `method` char(16) DEFAULT NULL COMMENT '管理员向商户转账的方式',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `operator` int unsigned NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `pid` int unsigned NOT NULL AUTO_INCREMENT,
  `username` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nickname` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `passhash` char(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `side` tinyint unsigned NOT NULL COMMENT '0=管理;10=商户',
  `qq` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL,
  `reg_ip` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_time` datetime DEFAULT NULL,
  `last_ip` char(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stat` tinyint unsigned DEFAULT NULL COMMENT '0=正常;1=考察期; 2=封禁',
  `balance` bigint DEFAULT NULL COMMENT '分为单位余额',
  `commission_fee_rate` decimal(5,4) DEFAULT '0.0500' COMMENT '提现手续费 / 订单金额。取值 (0.0000, 1.0000)',
  `level` tinyint unsigned DEFAULT NULL COMMENT '0=普通商户1',
  `key` char(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `key_last` datetime DEFAULT NULL COMMENT '上次 key 写入时间，重置 key 的冷却时间',
  `notify_method` tinyint DEFAULT NULL COMMENT '0=GET, 1=POST',
  `search_filter` tinyint DEFAULT '2' COMMENT '0=仅未支付且未通知订单, 1=含已支付且未通知订单, 2=全部订单',
  `auth` char(32) COLLATE utf8mb4_general_ci DEFAULT '',
  PRIMARY KEY (`pid`) USING BTREE,
  UNIQUE KEY `username_unique` (`username`) USING BTREE,
  UNIQUE KEY `qq_unique` (`qq`) USING BTREE,
  UNIQUE KEY `key_unique` (`key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-12 18:14:06
