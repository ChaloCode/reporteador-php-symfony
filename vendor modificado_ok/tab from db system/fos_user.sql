/*
Navicat MySQL Data Transfer

Source Server         : Local PC
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : colibri

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-10-06 15:58:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for fos_user
-- ----------------------------
DROP TABLE IF EXISTS `fos_user`;
CREATE TABLE `fos_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of fos_user
-- ----------------------------
INSERT INTO `fos_user` VALUES ('21', 'admin', 'admin', 'gjskateb@gmail.com', 'gjskateb@gmail.com', '1', '381n8ndyjvs48o848k4ogkoc0o8kcwc', '$2y$13$VHBD/DDnChGM/AwpgGfMXuR7DS3kaDEus1jOo9wgln0EbOWyhaW/S', '2016-10-06 22:04:32', '0', '0', null, null, null, 'a:1:{i:0;s:16:\"ROLE_SUPER_ADMIN\";}', '0', null);
INSERT INTO `fos_user` VALUES ('22', 'testuser', 'testuser', 'test@example.com', 'test@example.com', '1', 'anfzzpceyo008c00o80ks04o0wssw8g', '$2y$13$VHBD/DDnChGM/AwpgGfMXuR7DS3kaDEus1jOo9wgln0EbOWyhaW/S', '2016-10-06 22:35:29', '0', '0', null, null, null, 'a:0:{}', '0', null);
INSERT INTO `fos_user` VALUES ('23', 'adminuser', 'adminuser', 'prueba@email.co', 'prueba@email.co', '1', 'rpv8qcnwgu8wkw0ogok0cskko88w808', '$2y$13$VHBD/DDnChGM/AwpgGfMXuR7DS3kaDEus1jOo9wgln0EbOWyhaW/S', '2016-09-26 23:11:09', '0', '0', null, null, null, 'a:0:{}', '0', null);
