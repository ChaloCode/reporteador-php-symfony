/*
Navicat MySQL Data Transfer

Source Server         : Local PC
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : colibri

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-10-06 15:59:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sys_conexion_bd
-- ----------------------------
DROP TABLE IF EXISTS `sys_conexion_bd`;
CREATE TABLE `sys_conexion_bd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Host` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Port` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Nombre_BD` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Usuario` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_Fos_user` int(11) NOT NULL,
  `Nombre_Conexion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_Tipo_Conexion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_627914B6A69333CE` (`id_Tipo_Conexion`),
  CONSTRAINT `FK_627914B6A69333CE` FOREIGN KEY (`id_Tipo_Conexion`) REFERENCES `sys_tipo_conexion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
