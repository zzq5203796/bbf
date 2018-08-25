/*
 Navicat MySQL Data Transfer

 Source Server         : ubuntu
 Source Server Type    : MySQL
 Source Server Version : 50723
 Source Host           : 1.1.1.150:3306
 Source Schema         : test

 Target Server Type    : MySQL
 Target Server Version : 50723
 File Encoding         : 65001

 Date: 24/08/2018 16:59:47
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `first_link` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `preg_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `preg_content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `preg_next` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `header` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `preg` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of books
-- ----------------------------
INSERT INTO `books` VALUES (1, 'xx00', 'https://www.qu.la/book/34892/', 'https://www.qu.la/book/34892/2494615.html', '/<div class=\"bookname\">.*?<h1>(.*?)<\\/h1>.*?<\\/div>/is', '/<div id=\"content\">(.*?)<\\/div>/is', '/<a id=\"A3\" href=\"(.*?)\" target=\"_top\" class=\"next\">下一章<\\/a>/is', '[]', '[]');
INSERT INTO `books` VALUES (2, '淘宝大唐', 'https://www.qu.la/book/29223/', 'https://www.qu.la/book/29223/10586194.html', '/<div class=\"bookname\">.*?<h1>(.*?)<\\/h1>.*?<\\/div>/is', '/<div id=\"content\">(.*?)<\\/div>/is', '/<a id=\"pager_next\" href=\"(.*?)\" target=\"_top\" class=\"next\">下一章<\\/a>/is', '[]', '[]');
INSERT INTO `books` VALUES (3, '大海贼巴基', 'https://www.qu.la/book/18865/', 'https://www.qu.la/book/18865/7397273.html', '/<div class=\"bookname\">.*?<h1>(.*?)<\\/h1>.*?<\\/div>/is', '/<div id=\"content\">(.*?)<\\/div>/is', '/<a id=\"pager_next\" href=\"(.*?)\" target=\"_top\" class=\"next\">下一章<\\/a>/is', '[]', '[]');
INSERT INTO `books` VALUES (4, '都市全能巨星', 'https://www.qu.la/book/29543/', 'https://www.qu.la/book/29543/10626249.html', ' /<div class=\"bookname\">.*?<h1>(.*?)<\\/h1>.*?<\\/div>/is', ' /<div id=\"content\">(.*?)<\\/div>/is', ' /<a id=\"pager_next\" href=\"(.*?)\" target=\"_top\" class=\"next\">下一章<\\/a>/is', '[]', '[]');
INSERT INTO `books` VALUES (5, '超级图书馆', 'https://www.qu.la/book/63258/', 'https://www.qu.la/book/63258/3418952.html', ' /<div class=\"bookname\">.*?<h1>(.*?)<\\/h1>.*?<\\/div>/is', ' /<div id=\"content\">(.*?)<\\/div>/is', ' /<a id=\"pager_next\" href=\"(.*?)\" target=\"_top\" class=\"next\">下一章<\\/a>/is', '[]', '[]');

SET FOREIGN_KEY_CHECKS = 1;
