/*
Navicat MySQL Data Transfer

Source Server         : Local PC
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : colibri

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-10-06 15:59:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sys_consulta_sql
-- ----------------------------
DROP TABLE IF EXISTS `sys_consulta_sql`;
CREATE TABLE `sys_consulta_sql` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `string_query` longtext COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_conexion` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
