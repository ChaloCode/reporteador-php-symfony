/*
Navicat MySQL Data Transfer

Source Server         : Local PC
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : colibri

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-10-06 15:59:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sys_tipo_conexion
-- ----------------------------
DROP TABLE IF EXISTS `sys_tipo_conexion`;
CREATE TABLE `sys_tipo_conexion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Driver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C7A59BB93D3C9410` (`Nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sys_tipo_conexion
-- ----------------------------
INSERT INTO `sys_tipo_conexion` VALUES ('1', 'pdo_mysql', 'MySQL');
INSERT INTO `sys_tipo_conexion` VALUES ('2', 'pdo_sqlite', 'SQLite');
INSERT INTO `sys_tipo_conexion` VALUES ('3', 'pdo_pgsql', 'PostgreSQL');
INSERT INTO `sys_tipo_conexion` VALUES ('4', 'pdo_oci', 'Oracle');
INSERT INTO `sys_tipo_conexion` VALUES ('5', 'sqlsrv', 'SQL Server');
