/*
Navicat MySQL Data Transfer

Source Server         : Local PC
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : colibri

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-10-06 15:58:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for sys_rol
-- ----------------------------
DROP TABLE IF EXISTS `sys_rol`;
CREATE TABLE `sys_rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Permiso` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sys_rol
-- ----------------------------
INSERT INTO `sys_rol` VALUES ('5', 'ROLE_CONFIG_NAME', 'Es el modulo de configuracion: administra conexiones y las consultas a BD externas.', 'config');
INSERT INTO `sys_rol` VALUES ('6', 'ROLE_REPORT_NAME', 'Es el modulo que generea reportes.', 'report');
INSERT INTO `sys_rol` VALUES ('7', 'ROLE_PREDICT_NAME', 'Es el modulo que realiza las predicciones.', 'predict');
INSERT INTO `sys_rol` VALUES ('8', 'ROLE_BACKEND_NAME', 'Es donde se gestiona los usuarios del sistema.', 'backend');
