/*
 Navicat Premium Data Transfer

 Source Server         : 本地数据库
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : www.thinkadmin.com

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 28/09/2020 15:55:54
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for market_brush_list
-- ----------------------------
DROP TABLE IF EXISTS `market_brush_list`;
CREATE TABLE `market_brush_list`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '刷手姓名',
  `user_phone` bigint(11) UNSIGNED NULL DEFAULT NULL COMMENT '刷手电话',
  `user_bkge` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '刷手佣金',
  `order_sn` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单编号',
  `order_price` decimal(10, 2) UNSIGNED NULL DEFAULT NULL COMMENT '订单金额',
  `express_price` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '快递费用',
  `pay_bkge` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '支付佣金',
  `pay_price` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '支付费用',
  `shop_id` bigint(10) UNSIGNED NOT NULL COMMENT '所属店铺',
  `create_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for market_brush_log
-- ----------------------------
DROP TABLE IF EXISTS `market_brush_log`;
CREATE TABLE `market_brush_log`  (
  `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` bigint(10) NULL DEFAULT NULL COMMENT '店铺ID',
  `account_log` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '资金日志',
  `account` decimal(20, 2) NULL DEFAULT 0.00 COMMENT '当前资金',
  `create_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  PRIMARY KEY (`log_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for market_brush_shops
-- ----------------------------
DROP TABLE IF EXISTS `market_brush_shops`;
CREATE TABLE `market_brush_shops`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '店铺名称',
  `shop_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '所属平台类型:1=拼多多，2=淘宝',
  `account` decimal(20, 2) NULL DEFAULT 0.00 COMMENT '余额',
  `bkge` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '默认佣金费用',
  `express_price` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '默认空包费用',
  `sort` bigint(20) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '店铺状态:0=禁用,1=启用',
  `create_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '创建时间',
  `update_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_system_shop_name`(`shop_name`) USING BTREE,
  INDEX `idx_system_shop_type`(`shop_type`) USING BTREE,
  INDEX `idx_system_shop_status`(`status`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
