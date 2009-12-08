/*
SQLyog Enterprise - MySQL GUI v8.12 
MySQL - 5.1.36-community-log : Database - tog_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`tog_db` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `tog_db`;

/*Table structure for table `tbl_brand` */

DROP TABLE IF EXISTS `tbl_brand`;

CREATE TABLE `tbl_brand` (
  `brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_brand` */

insert  into `tbl_brand`(`brand_id`,`brand_name`) values (0,'');

/*Table structure for table `tbl_cart_temp` */

DROP TABLE IF EXISTS `tbl_cart_temp`;

CREATE TABLE `tbl_cart_temp` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `billing_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shipping_id` int(10) unsigned NOT NULL DEFAULT '0',
  `subtotal` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `shipping_method_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shipping_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `coupon_code` varchar(50) NOT NULL DEFAULT '',
  `coupon_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `tax_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `FK_tbl_cart_temp_user_id` (`user_id`),
  KEY `FK_tbl_cart_temp_billing_id` (`billing_id`),
  KEY `FK_tbl_cart_temp_shipping_id` (`shipping_id`),
  KEY `FK_tbl_cart_temp_shipping_method_id` (`shipping_method_id`),
  KEY `FK_tbl_cart_temp_coupon_code` (`coupon_code`),
  CONSTRAINT `FK_tbl_cart_temp_billing_id` FOREIGN KEY (`billing_id`) REFERENCES `tbl_user_address` (`address_id`),
  CONSTRAINT `FK_tbl_cart_temp_coupon_code` FOREIGN KEY (`coupon_code`) REFERENCES `tbl_coupon` (`coupon_code`),
  CONSTRAINT `FK_tbl_cart_temp_shipping_id` FOREIGN KEY (`shipping_id`) REFERENCES `tbl_user_address` (`address_id`),
  CONSTRAINT `FK_tbl_cart_temp_shipping_method_id` FOREIGN KEY (`shipping_method_id`) REFERENCES `tbl_shipping_method` (`method_id`),
  CONSTRAINT `FK_tbl_cart_temp_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_cart_temp` */

/*Table structure for table `tbl_cart_temp_detail` */

DROP TABLE IF EXISTS `tbl_cart_temp_detail`;

CREATE TABLE `tbl_cart_temp_detail` (
  `session_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `unit_price` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `subtotal` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `FK_tbl_cart_temp_detail_product_id` (`product_id`),
  KEY `FK_tbl_cart_temp_detail_user_id` (`user_id`),
  CONSTRAINT `FK_tbl_cart_temp_detail_product_id` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`product_id`),
  CONSTRAINT `FK_tbl_cart_temp_detail_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_cart_temp_detail` */

/*Table structure for table `tbl_category` */

DROP TABLE IF EXISTS `tbl_category`;

CREATE TABLE `tbl_category` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_name` varchar(255) NOT NULL DEFAULT '',
  `cat_alias` varchar(255) NOT NULL DEFAULT '',
  `cat_desc` mediumtext,
  `cat_image` varchar(255) NOT NULL DEFAULT '',
  `cat_thumb` varchar(255) NOT NULL DEFAULT '',
  `cat_publish` tinyint(1) NOT NULL DEFAULT '1',
  `cat_order` int(11) NOT NULL DEFAULT '0',
  `cat_frontpage` tinyint(1) NOT NULL DEFAULT '0',
  `c_date` int(11) NOT NULL DEFAULT '0',
  `m_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`),
  KEY `FK_tbl_category` (`parent_id`),
  CONSTRAINT `FK_tbl_category` FOREIGN KEY (`parent_id`) REFERENCES `tbl_category` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_category` */

insert  into `tbl_category`(`cat_id`,`parent_id`,`cat_name`,`cat_alias`,`cat_desc`,`cat_image`,`cat_thumb`,`cat_publish`,`cat_order`,`cat_frontpage`,`c_date`,`m_date`) values (0,0,'','',NULL,'','',1,0,0,0,0);

/*Table structure for table `tbl_country` */

DROP TABLE IF EXISTS `tbl_country`;

CREATE TABLE `tbl_country` (
  `country_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_code` char(5) NOT NULL DEFAULT '',
  `country_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `IDX_tbl_country_country_code` (`country_code`)
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_country` */

insert  into `tbl_country`(`country_id`,`country_code`,`country_name`) values (0,'',''),(1,'AF','Afghanistan'),(2,'AL','Albania'),(3,'DZ','Algeria'),(4,'AS','American Samoa'),(5,'AD','Andorra'),(6,'AO','Angola'),(7,'AI','Anguilla'),(8,'AQ','Antarctica'),(9,'AG','Antigua and Barbuda'),(10,'AR','Argentina'),(11,'AM','Armenia'),(12,'AW','Aruba'),(13,'AU','Australia'),(14,'AT','Austria'),(15,'AZ','Azerbaijan'),(16,'BS','Bahamas'),(17,'BH','Bahrain'),(18,'BD','Bangladesh'),(19,'BB','Barbados'),(20,'BY','Belarus'),(21,'BE','Belgium'),(22,'BZ','Belize'),(23,'BJ','Benin'),(24,'BM','Bermuda'),(25,'BT','Bhutan'),(26,'BO','Bolivia'),(27,'BA','Bosnia and Herzegowina'),(28,'BW','Botswana'),(29,'BV','Bouvet Island'),(30,'BR','Brazil'),(31,'IO','British Indian Ocean Territory'),(32,'BN','Brunei Darussalam'),(33,'BG','Bulgaria'),(34,'BF','Burkina Faso'),(35,'BI','Burundi'),(36,'KH','Cambodia'),(37,'CM','Cameroon'),(38,'CA','Canada'),(39,'CV','Cape Verde'),(40,'KY','Cayman Islands'),(41,'CF','Central African Republic'),(42,'TD','Chad'),(43,'CL','Chile'),(44,'CN','China'),(45,'CX','Christmas Island'),(46,'CC','Cocos (Keeling) Islands'),(47,'CO','Colombia'),(48,'KM','Comoros'),(49,'CG','Congo'),(50,'CK','Cook Islands'),(51,'CR','Costa Rica'),(52,'CI','Cote D\'Ivoire'),(53,'HR','Croatia'),(54,'CU','Cuba'),(55,'CY','Cyprus'),(56,'CZ','Czech Republic'),(57,'DK','Denmark'),(58,'DJ','Djibouti'),(59,'DM','Dominica'),(60,'DO','Dominican Republic'),(61,'TP','East Timor'),(62,'EC','Ecuador'),(63,'EG','Egypt'),(64,'SV','El Salvador'),(65,'GQ','Equatorial Guinea'),(66,'ER','Eritrea'),(67,'EE','Estonia'),(68,'ET','Ethiopia'),(69,'FK','Falkland Islands (Malvinas)'),(70,'FO','Faroe Islands'),(71,'FJ','Fiji'),(72,'FI','Finland'),(73,'FR','France'),(74,'FX','France, Metropolitan'),(75,'GF','French Guiana'),(76,'PF','French Polynesia'),(77,'TF','French Southern Territories'),(78,'GA','Gabon'),(79,'GM','Gambia'),(80,'GE','Georgia'),(81,'DE','Germany'),(82,'GH','Ghana'),(83,'GI','Gibraltar'),(84,'GR','Greece'),(85,'GL','Greenland'),(86,'GD','Grenada'),(87,'GP','Guadeloupe'),(88,'GU','Guam'),(89,'GT','Guatemala'),(90,'GN','Guinea'),(91,'GW','Guinea-bissau'),(92,'GY','Guyana'),(93,'HT','Haiti'),(94,'HM','Heard and Mc Donald Islands'),(95,'HN','Honduras'),(96,'HK','Hong Kong'),(97,'HU','Hungary'),(98,'IS','Iceland'),(99,'IN','India'),(100,'ID','Indonesia'),(101,'IR','Iran (Islamic Republic of)'),(102,'IQ','Iraq'),(103,'IE','Ireland'),(104,'IL','Israel'),(105,'IT','Italy'),(106,'JM','Jamaica'),(107,'JP','Japan'),(108,'JO','Jordan'),(109,'KZ','Kazakhstan'),(110,'KE','Kenya'),(111,'KI','Kiribati'),(112,'KP','Korea, Democratic People\'s Republic of'),(113,'KR','Korea, Republic of'),(114,'KW','Kuwait'),(115,'KG','Kyrgyzstan'),(116,'LA','Lao People\'s Democratic Republic'),(117,'LV','Latvia'),(118,'LB','Lebanon'),(119,'LS','Lesotho'),(120,'LR','Liberia'),(121,'LY','Libyan Arab Jamahiriya'),(122,'LI','Liechtenstein'),(123,'LT','Lithuania'),(124,'LU','Luxembourg'),(125,'MO','Macau'),(126,'MK','Macedonia, The Former Yugoslav Republic of'),(127,'MG','Madagascar'),(128,'MW','Malawi'),(129,'MY','Malaysia'),(130,'MV','Maldives'),(131,'ML','Mali'),(132,'MT','Malta'),(133,'MH','Marshall Islands'),(134,'MQ','Martinique'),(135,'MR','Mauritania'),(136,'MU','Mauritius'),(137,'YT','Mayotte'),(138,'MX','Mexico'),(139,'FM','Micronesia, Federated States of'),(140,'MD','Moldova, Republic of'),(141,'MC','Monaco'),(142,'MN','Mongolia'),(143,'MS','Montserrat'),(144,'MA','Morocco'),(145,'MZ','Mozambique'),(146,'MM','Myanmar'),(147,'NA','Namibia'),(148,'NR','Nauru'),(149,'NP','Nepal'),(150,'NL','Netherlands'),(151,'AN','Netherlands Antilles'),(152,'NC','New Caledonia'),(153,'NZ','New Zealand'),(154,'NI','Nicaragua'),(155,'NE','Niger'),(156,'NG','Nigeria'),(157,'NU','Niue'),(158,'NF','Norfolk Island'),(159,'MP','Northern Mariana Islands'),(160,'NO','Norway'),(161,'OM','Oman'),(162,'PK','Pakistan'),(163,'PW','Palau'),(164,'PA','Panama'),(165,'PG','Papua New Guinea'),(166,'PY','Paraguay'),(167,'PE','Peru'),(168,'PH','Philippines'),(169,'PN','Pitcairn'),(170,'PL','Poland'),(171,'PT','Portugal'),(172,'PR','Puerto Rico'),(173,'QA','Qatar'),(174,'RE','Reunion'),(175,'RO','Romania'),(176,'RU','Russian Federation'),(177,'RW','Rwanda'),(178,'KN','Saint Kitts and Nevis'),(179,'LC','Saint Lucia'),(180,'VC','Saint Vincent and the Grenadines'),(181,'WS','Samoa'),(182,'SM','San Marino'),(183,'ST','Sao Tome and Principe'),(184,'SA','Saudi Arabia'),(185,'SN','Senegal'),(186,'SC','Seychelles'),(187,'SL','Sierra Leone'),(188,'SG','Singapore'),(189,'SK','Slovakia (Slovak Republic)'),(190,'SI','Slovenia'),(191,'SB','Solomon Islands'),(192,'SO','Somalia'),(193,'ZA','South Africa'),(194,'GS','South Georgia and the South Sandwich Islands'),(195,'ES','Spain'),(196,'LK','Sri Lanka'),(197,'SH','St. Helena'),(198,'PM','St. Pierre and Miquelon'),(199,'SD','Sudan'),(200,'SR','Suriname'),(201,'SJ','Svalbard and Jan Mayen Islands'),(202,'SZ','Swaziland'),(203,'SE','Sweden'),(204,'CH','Switzerland'),(205,'SY','Syrian Arab Republic'),(206,'TW','Taiwan'),(207,'TJ','Tajikistan'),(208,'TZ','Tanzania, United Republic of'),(209,'TH','Thailand'),(210,'TG','Togo'),(211,'TK','Tokelau'),(212,'TO','Tonga'),(213,'TT','Trinidad and Tobago'),(214,'TN','Tunisia'),(215,'TR','Turkey'),(216,'TM','Turkmenistan'),(217,'TC','Turks and Caicos Islands'),(218,'TV','Tuvalu'),(219,'UG','Uganda'),(220,'UA','Ukraine'),(221,'AE','United Arab Emirates'),(222,'GB','United Kingdom'),(223,'US','United States'),(224,'UM','United States Minor Outlying Islands'),(225,'UY','Uruguay'),(226,'UZ','Uzbekistan'),(227,'VU','Vanuatu'),(228,'VA','Vatican City State (Holy See)'),(229,'VE','Venezuela'),(230,'VN','Viet Nam'),(231,'VG','Virgin Islands (British)'),(232,'VI','Virgin Islands (U.S.)'),(233,'WF','Wallis and Futuna Islands'),(234,'EH','Western Sahara'),(235,'YE','Yemen'),(236,'YU','Yugoslavia'),(237,'ZR','Zaire'),(238,'ZM','Zambia'),(239,'ZW','Zimbabwe'),(240,'XE','East Timor'),(241,'XJ','Jersey'),(242,'XB','St. Barthelemy'),(243,'XU','St. Eustatius'),(244,'XC','Canary Islands');

/*Table structure for table `tbl_coupon` */

DROP TABLE IF EXISTS `tbl_coupon`;

CREATE TABLE `tbl_coupon` (
  `coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_code` varchar(50) NOT NULL DEFAULT '',
  `coupon_type` int(10) unsigned NOT NULL DEFAULT '0',
  `coupon_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`coupon_id`),
  UNIQUE KEY `IDX_tbl_coupon_coupon_code` (`coupon_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_coupon` */

insert  into `tbl_coupon`(`coupon_id`,`coupon_code`,`coupon_type`,`coupon_amount`) values (0,'0',0,'0.00000');

/*Table structure for table `tbl_mailing_list` */

DROP TABLE IF EXISTS `tbl_mailing_list`;

CREATE TABLE `tbl_mailing_list` (
  `mailing_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mailing_address` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mailing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_mailing_list` */

/*Table structure for table `tbl_manufacturer` */

DROP TABLE IF EXISTS `tbl_manufacturer`;

CREATE TABLE `tbl_manufacturer` (
  `mf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mf_name` varchar(255) NOT NULL DEFAULT '',
  `mf_desc` mediumtext,
  `mf_email` varchar(255) NOT NULL DEFAULT '',
  `mf_url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`mf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_manufacturer` */

insert  into `tbl_manufacturer`(`mf_id`,`mf_name`,`mf_desc`,`mf_email`,`mf_url`) values (0,'',NULL,'','');

/*Table structure for table `tbl_order` */

DROP TABLE IF EXISTS `tbl_order`;

CREATE TABLE `tbl_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_num` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `b_first_name` varchar(50) NOT NULL DEFAULT '',
  `b_last_name` varchar(50) NOT NULL DEFAULT '',
  `b_address_1` varchar(255) NOT NULL DEFAULT '',
  `b_address_2` varchar(255) NOT NULL DEFAULT '',
  `b_city` varchar(255) NOT NULL DEFAULT '',
  `b_state` varchar(255) NOT NULL DEFAULT '',
  `b_country_code` char(5) NOT NULL DEFAULT '',
  `b_zip_code` char(10) NOT NULL DEFAULT '',
  `b_phone_1` varchar(20) NOT NULL DEFAULT '',
  `b_phone_2` varchar(20) NOT NULL DEFAULT '',
  `b_fax` varchar(20) NOT NULL DEFAULT '',
  `s_first_name` varchar(50) NOT NULL DEFAULT '',
  `s_last_name` varchar(50) NOT NULL DEFAULT '',
  `s_address_1` varchar(255) NOT NULL DEFAULT '',
  `s_address_2` varchar(255) NOT NULL DEFAULT '',
  `s_city` varchar(255) NOT NULL DEFAULT '',
  `s_state` varchar(255) NOT NULL DEFAULT '',
  `s_country_code` char(5) NOT NULL DEFAULT '',
  `s_zip_code` char(7) NOT NULL DEFAULT '',
  `s_phone_1` varchar(20) NOT NULL DEFAULT '',
  `s_phone_2` varchar(20) NOT NULL DEFAULT '',
  `s_fax` varchar(20) NOT NULL DEFAULT '',
  `subtotal` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `tax_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `shipping_method_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shipping_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `coupon_code` varchar(50) NOT NULL DEFAULT '0',
  `coupon_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `total` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `currency` char(5) NOT NULL DEFAULT '''USD''',
  `ip_address` varchar(20) NOT NULL DEFAULT '',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  `m_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `FK_tbl_order_user_id` (`user_id`),
  KEY `FK_tbl_order_shipping_method_id` (`shipping_method_id`),
  KEY `FK_tbl_order_coupon_code` (`coupon_code`),
  CONSTRAINT `FK_tbl_order_coupon_code` FOREIGN KEY (`coupon_code`) REFERENCES `tbl_coupon` (`coupon_code`),
  CONSTRAINT `FK_tbl_order_shipping_method_id` FOREIGN KEY (`shipping_method_id`) REFERENCES `tbl_shipping_method` (`method_id`),
  CONSTRAINT `FK_tbl_order_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_order` */

insert  into `tbl_order`(`order_id`,`order_num`,`user_id`,`b_first_name`,`b_last_name`,`b_address_1`,`b_address_2`,`b_city`,`b_state`,`b_country_code`,`b_zip_code`,`b_phone_1`,`b_phone_2`,`b_fax`,`s_first_name`,`s_last_name`,`s_address_1`,`s_address_2`,`s_city`,`s_state`,`s_country_code`,`s_zip_code`,`s_phone_1`,`s_phone_2`,`s_fax`,`subtotal`,`tax_amount`,`shipping_method_id`,`shipping_amount`,`coupon_code`,`coupon_amount`,`total`,`currency`,`ip_address`,`c_date`,`m_date`) values (0,'',0,'','','','','','','','','','','','','','','','','','','','','','','0.00000','0.00000',0,'0.00000','0','0.00000','0.00000','\'USD\'','',0,0);

/*Table structure for table `tbl_order_history` */

DROP TABLE IF EXISTS `tbl_order_history`;

CREATE TABLE `tbl_order_history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_status_code` char(2) NOT NULL DEFAULT '',
  `comments` mediumtext,
  `c_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`history_id`),
  KEY `FK_tbl_order_history_order_id` (`order_id`),
  KEY `FK_tbl_order_history` (`order_status_code`),
  CONSTRAINT `FK_tbl_order_history` FOREIGN KEY (`order_status_code`) REFERENCES `tbl_order_status` (`status_code`),
  CONSTRAINT `FK_tbl_order_history_order_id` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_order_history` */

/*Table structure for table `tbl_order_item` */

DROP TABLE IF EXISTS `tbl_order_item`;

CREATE TABLE `tbl_order_item` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0',
  `unit_price` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `subtotal` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  `m_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `FK_tbl_order_item_order_id` (`order_id`),
  KEY `FK_tbl_order_item_product_id` (`product_id`),
  CONSTRAINT `FK_tbl_order_item_order_id` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_tbl_order_item_product_id` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_order_item` */

/*Table structure for table `tbl_order_status` */

DROP TABLE IF EXISTS `tbl_order_status`;

CREATE TABLE `tbl_order_status` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status_code` char(2) NOT NULL DEFAULT '',
  `status_name` varchar(255) NOT NULL DEFAULT '',
  `status_desc` text,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `IDX_tbl_order_status_status_code` (`status_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_order_status` */

insert  into `tbl_order_status`(`status_id`,`status_code`,`status_name`,`status_desc`) values (1,'P','Processing',NULL),(2,'C','Confirmed',NULL),(3,'X','Cancelled',NULL),(4,'S','Shipped',NULL),(5,'R','Refunded',NULL),(6,'D','Delivered',NULL);

/*Table structure for table `tbl_payment` */

DROP TABLE IF EXISTS `tbl_payment`;

CREATE TABLE `tbl_payment` (
  `payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_method_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `payment_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `payment_status` int(10) unsigned NOT NULL DEFAULT '0',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  `m_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`payment_id`),
  KEY `FK_tbl_payment_order_id` (`order_id`),
  KEY `FK_tbl_payment_payment_method_id` (`payment_method_id`),
  CONSTRAINT `FK_tbl_payment_order_id` FOREIGN KEY (`order_id`) REFERENCES `tbl_order` (`order_id`),
  CONSTRAINT `FK_tbl_payment_payment_method_id` FOREIGN KEY (`payment_method_id`) REFERENCES `tbl_payment_method` (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_payment` */

insert  into `tbl_payment`(`payment_id`,`payment_method_id`,`order_id`,`payment_amount`,`payment_status`,`c_date`,`m_date`) values (0,0,0,'0.00000',0,0,0);

/*Table structure for table `tbl_payment_method` */

DROP TABLE IF EXISTS `tbl_payment_method`;

CREATE TABLE `tbl_payment_method` (
  `method_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `method_name` varchar(255) NOT NULL DEFAULT '',
  `method_publish` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_payment_method` */

insert  into `tbl_payment_method`(`method_id`,`method_name`,`method_publish`) values (0,'',0);

/*Table structure for table `tbl_product` */

DROP TABLE IF EXISTS `tbl_product`;

CREATE TABLE `tbl_product` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mf_id` int(10) unsigned NOT NULL DEFAULT '0',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0',
  `discount_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_sku` varchar(50) NOT NULL DEFAULT '',
  `product_name` varchar(255) NOT NULL DEFAULT '',
  `product_brief` text,
  `product_desc` mediumtext,
  `product_image` varchar(255) NOT NULL DEFAULT '',
  `product_thumb` varchar(255) NOT NULL DEFAULT '',
  `product_price` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `product_reward_points` int(10) unsigned NOT NULL DEFAULT '0',
  `product_best_seller` tinyint(1) NOT NULL DEFAULT '0',
  `product_hot_deal` tinyint(1) NOT NULL DEFAULT '0',
  `product_new_arrival` tinyint(1) NOT NULL DEFAULT '1',
  `product_publish` tinyint(1) NOT NULL DEFAULT '1',
  `product_order` int(10) unsigned NOT NULL DEFAULT '0',
  `product_in_stock` int(10) unsigned NOT NULL DEFAULT '99',
  `product_uom` varchar(20) NOT NULL DEFAULT '',
  `product_available_date` int(10) unsigned NOT NULL DEFAULT '0',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  `m_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`),
  KEY `FK_tbl_product_brand_id` (`brand_id`),
  KEY `FK_tbl_product_discount_id` (`discount_id`),
  KEY `FK_tbl_product_mf_id` (`mf_id`),
  CONSTRAINT `FK_tbl_product_mf_id` FOREIGN KEY (`mf_id`) REFERENCES `tbl_manufacturer` (`mf_id`),
  CONSTRAINT `FK_tbl_product_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `tbl_brand` (`brand_id`),
  CONSTRAINT `FK_tbl_product_discount_id` FOREIGN KEY (`discount_id`) REFERENCES `tbl_product_discount` (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_product` */

insert  into `tbl_product`(`product_id`,`mf_id`,`brand_id`,`discount_id`,`product_sku`,`product_name`,`product_brief`,`product_desc`,`product_image`,`product_thumb`,`product_price`,`product_reward_points`,`product_best_seller`,`product_hot_deal`,`product_new_arrival`,`product_publish`,`product_order`,`product_in_stock`,`product_uom`,`product_available_date`,`c_date`,`m_date`) values (0,0,0,0,'','',NULL,NULL,'','','0.00000',0,0,0,1,1,0,99,'',0,0,0);

/*Table structure for table `tbl_product_cat_xref` */

DROP TABLE IF EXISTS `tbl_product_cat_xref`;

CREATE TABLE `tbl_product_cat_xref` (
  `product_id` int(11) unsigned NOT NULL DEFAULT '0',
  `cat_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_id`,`cat_id`),
  KEY `FK_tbl_product_cat_xref_cat_id` (`cat_id`),
  CONSTRAINT `FK_tbl_product_cat_xref_cat_id` FOREIGN KEY (`cat_id`) REFERENCES `tbl_category` (`cat_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_tbl_product_cat_xref_product_id` FOREIGN KEY (`product_id`) REFERENCES `tbl_product` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_product_cat_xref` */

/*Table structure for table `tbl_product_discount` */

DROP TABLE IF EXISTS `tbl_product_discount`;

CREATE TABLE `tbl_product_discount` (
  `discount_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discount_name` varchar(255) NOT NULL DEFAULT '',
  `discount_amount` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `is_percent` tinyint(1) NOT NULL DEFAULT '0',
  `s_date` int(10) unsigned NOT NULL DEFAULT '0',
  `e_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_product_discount` */

insert  into `tbl_product_discount`(`discount_id`,`discount_name`,`discount_amount`,`is_percent`,`s_date`,`e_date`) values (0,'','0.00000',0,0,0);

/*Table structure for table `tbl_shipping_method` */

DROP TABLE IF EXISTS `tbl_shipping_method`;

CREATE TABLE `tbl_shipping_method` (
  `method_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `method_name` varchar(255) NOT NULL DEFAULT '',
  `discount_id` int(10) unsigned NOT NULL DEFAULT '0',
  `method_price` decimal(12,5) unsigned NOT NULL DEFAULT '0.00000',
  `method_publish` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_shipping_method` */

insert  into `tbl_shipping_method`(`method_id`,`method_name`,`discount_id`,`method_price`,`method_publish`) values (0,'',0,'0.00000',0);

/*Table structure for table `tbl_user` */

DROP TABLE IF EXISTS `tbl_user`;

CREATE TABLE `tbl_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL DEFAULT '',
  `user_pwd` varchar(32) NOT NULL DEFAULT '',
  `user_email` varchar(255) NOT NULL DEFAULT '',
  `user_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_credits` int(10) unsigned NOT NULL DEFAULT '0',
  `user_status` int(10) unsigned NOT NULL DEFAULT '0',
  `user_hash` varchar(32) NOT NULL DEFAULT '',
  `ip_address` varchar(20) NOT NULL DEFAULT '',
  `last_visit` int(10) unsigned NOT NULL DEFAULT '0',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  `m_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `FK_tbl_user_user_type_id` (`user_type_id`),
  CONSTRAINT `FK_tbl_user_user_type_id` FOREIGN KEY (`user_type_id`) REFERENCES `tbl_user_type` (`user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_user` */

insert  into `tbl_user`(`user_id`,`user_name`,`user_pwd`,`user_email`,`user_type_id`,`user_credits`,`user_status`,`user_hash`,`ip_address`,`last_visit`,`c_date`,`m_date`) values (0,'','','',0,0,0,'','',0,0,0);

/*Table structure for table `tbl_user_address` */

DROP TABLE IF EXISTS `tbl_user_address`;

CREATE TABLE `tbl_user_address` (
  `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `address_type` enum('B','S') NOT NULL DEFAULT 'S',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `company_name` varchar(255) NOT NULL DEFAULT '',
  `title` char(10) NOT NULL DEFAULT '',
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `address_1` varchar(255) NOT NULL DEFAULT '',
  `address_2` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) NOT NULL DEFAULT '',
  `country_code` char(5) NOT NULL DEFAULT '',
  `zip_code` char(7) NOT NULL DEFAULT '',
  `phone_1` varchar(20) NOT NULL DEFAULT '',
  `phone_2` varchar(20) NOT NULL DEFAULT '',
  `fax` varchar(20) NOT NULL DEFAULT '',
  `c_date` int(10) unsigned NOT NULL DEFAULT '0',
  `m_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`address_id`),
  KEY `FK_tbl_address_user_id` (`user_id`),
  KEY `FK_tbl_user_address` (`country_code`),
  CONSTRAINT `FK_tbl_address_user_id` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_tbl_user_address` FOREIGN KEY (`country_code`) REFERENCES `tbl_country` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_user_address` */

/*Table structure for table `tbl_user_type` */

DROP TABLE IF EXISTS `tbl_user_type`;

CREATE TABLE `tbl_user_type` (
  `user_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_type_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tbl_user_type` */

insert  into `tbl_user_type`(`user_type_id`,`user_type_name`) values (0,'Unused'),(1,'Guest'),(2,'Banned'),(3,'Registered'),(4,'Manager'),(5,'Administrator');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
