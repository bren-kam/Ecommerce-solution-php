-- MySQL dump 10.13  Distrib 5.6.19, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: test
-- ------------------------------------------------------
-- Server version	5.6.19-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `analytics_craigslist`
--

DROP TABLE IF EXISTS `analytics_craigslist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analytics_craigslist` (
  `website_id` int(11) NOT NULL,
  `craigslist_market_id` int(11) NOT NULL,
  `craigslist_tag_id` int(11) NOT NULL,
  `unique` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `posts` int(11) NOT NULL,
  `date` datetime NOT NULL,
  KEY `object_id` (`website_id`,`craigslist_market_id`,`craigslist_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analytics_emails`
--

DROP TABLE IF EXISTS `analytics_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analytics_emails` (
  `mc_campaign_id` varchar(50) NOT NULL,
  `ac_campaign_id` int(11) DEFAULT NULL,
  `syntax_errors` int(11) NOT NULL,
  `hard_bounces` int(11) NOT NULL,
  `soft_bounces` int(11) NOT NULL,
  `unsubscribes` int(11) NOT NULL,
  `abuse_reports` int(11) NOT NULL,
  `forwards` int(11) NOT NULL,
  `forwards_opens` int(11) NOT NULL,
  `opens` int(11) NOT NULL,
  `unique_opens` int(11) NOT NULL,
  `last_open` datetime NOT NULL,
  `clicks` int(11) NOT NULL,
  `unique_clicks` int(11) NOT NULL,
  `last_click` datetime NOT NULL,
  `users_who_clicked` int(11) NOT NULL,
  `emails_sent` int(11) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `mc_campaign_id` (`mc_campaign_id`),
  UNIQUE KEY `ac_campaign_id` (`ac_campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_ext_log`
--

DROP TABLE IF EXISTS `api_ext_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_ext_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `api` varchar(45) DEFAULT NULL,
  `method` varchar(100) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `request` text,
  `raw_request` text,
  `response` text,
  `raw_response` text,
  `date_created` datetime DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX` (`api`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_key_ashley_account`
--

DROP TABLE IF EXISTS `api_key_ashley_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_key_ashley_account` (
  `api_key_id` int(11) NOT NULL DEFAULT '0',
  `ashley_account` varchar(80) NOT NULL DEFAULT '',
  PRIMARY KEY (`api_key_id`,`ashley_account`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_key_brand`
--

DROP TABLE IF EXISTS `api_key_brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_key_brand` (
  `api_key_id` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`api_key_id`,`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_keys`
--

DROP TABLE IF EXISTS `api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_keys` (
  `api_key_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`api_key_id`),
  KEY `company_id` (`company_id`,`brand_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_log`
--

DROP TABLE IF EXISTS `api_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_log` (
  `api_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `method` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `success` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`api_log_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23433 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_settings`
--

DROP TABLE IF EXISTS `api_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_settings` (
  `api_key_id` int(11) NOT NULL,
  `key` varchar(200) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`api_key_id`,`key`),
  KEY `fk_as_idx` (`api_key_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attribute_item_relations`
--

DROP TABLE IF EXISTS `attribute_item_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_item_relations` (
  `attribute_item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`attribute_item_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attribute_items`
--

DROP TABLE IF EXISTS `attribute_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_items` (
  `attribute_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `attribute_item_name` text NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`attribute_item_id`),
  KEY `attribute_id` (`attribute_id`),
  FULLTEXT KEY `attribute_item_name` (`attribute_item_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2452 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attribute_relations`
--

DROP TABLE IF EXISTS `attribute_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_relations` (
  `attribute_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  KEY `category_id` (`category_id`),
  KEY `fk_ar_idx` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attributes` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_user_websites`
--

DROP TABLE IF EXISTS `auth_user_websites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_user_websites` (
  `auth_user_website_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `pages` tinyint(1) NOT NULL DEFAULT '0',
  `products` tinyint(1) NOT NULL DEFAULT '0',
  `analytics` tinyint(1) NOT NULL DEFAULT '0',
  `blog` tinyint(1) NOT NULL DEFAULT '0',
  `email_marketing` tinyint(1) NOT NULL DEFAULT '0',
  `shopping_cart` tinyint(1) NOT NULL DEFAULT '0',
  `geo_marketing` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`auth_user_website_id`),
  KEY `user_id` (`user_id`,`website_id`),
  KEY `fk_auw_idx` (`website_id`),
  KEY `fk_auw2_idx` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5018 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `link` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  PRIMARY KEY (`brand_id`),
  KEY `name_2` (`name`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1029 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `google_taxonomy` varchar(255) NOT NULL,
  `sequence` int(11) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checklist_items`
--

DROP TABLE IF EXISTS `checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist_items` (
  `checklist_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `checklist_section_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `assigned_to` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`checklist_item_id`),
  KEY `fk_ci_idx` (`checklist_section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checklist_sections`
--

DROP TABLE IF EXISTS `checklist_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist_sections` (
  `checklist_section_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`checklist_section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checklist_website_item_notes`
--

DROP TABLE IF EXISTS `checklist_website_item_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist_website_item_notes` (
  `checklist_website_item_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `checklist_website_item_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`checklist_website_item_note_id`),
  KEY `checklist_item_id` (`checklist_website_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checklist_website_items`
--

DROP TABLE IF EXISTS `checklist_website_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklist_website_items` (
  `checklist_website_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `checklist_id` int(11) NOT NULL,
  `checklist_item_id` int(11) NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `date_checked` datetime DEFAULT NULL,
  PRIMARY KEY (`checklist_website_item_id`),
  KEY `checklist_id` (`checklist_id`,`checklist_item_id`),
  KEY `fk_cwi_idx` (`checklist_id`),
  KEY `fk_cwi2_idx` (`checklist_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55289 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checklists`
--

DROP TABLE IF EXISTS `checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checklists` (
  `checklist_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_finished` datetime DEFAULT NULL,
  PRIMARY KEY (`checklist_id`),
  KEY `fk_c_idx` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1381 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `less` text,
  `css` text,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `company_packages`
--

DROP TABLE IF EXISTS `company_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_packages` (
  `company_package_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`company_package_id`),
  KEY `company_id` (`company_id`,`website_id`),
  KEY `fk_cp_idx` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_ad_headlines`
--

DROP TABLE IF EXISTS `craigslist_ad_headlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_ad_headlines` (
  `craigslist_ad_id` int(11) NOT NULL,
  `headline` varchar(250) NOT NULL,
  KEY `craigslist_ad_id` (`craigslist_ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_ad_markets`
--

DROP TABLE IF EXISTS `craigslist_ad_markets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_ad_markets` (
  `craigslist_ad_id` int(11) NOT NULL,
  `craigslist_market_id` int(11) NOT NULL,
  `primus_product_id` int(11) NOT NULL,
  PRIMARY KEY (`craigslist_ad_id`,`craigslist_market_id`,`primus_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_ads`
--

DROP TABLE IF EXISTS `craigslist_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_ads` (
  `craigslist_ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `price` float NOT NULL,
  `error` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `date_posted` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`craigslist_ad_id`),
  KEY `website_id` (`website_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Craigslist ads that account-side customers post';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_categories`
--

DROP TABLE IF EXISTS `craigslist_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_categories` (
  `craigslist_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `craigslist_category_code` varchar(3) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  PRIMARY KEY (`craigslist_category_id`),
  UNIQUE KEY `craigslist_category_code` (`craigslist_category_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_cities`
--

DROP TABLE IF EXISTS `craigslist_cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_cities` (
  `craigslist_city_id` int(11) NOT NULL AUTO_INCREMENT,
  `craigslist_city_code` varchar(3) NOT NULL,
  `city_name` varchar(20) NOT NULL,
  `state_name` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  PRIMARY KEY (`craigslist_city_id`),
  UNIQUE KEY `craigslist_city_code` (`craigslist_city_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_districts`
--

DROP TABLE IF EXISTS `craigslist_districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_districts` (
  `craigslist_district_id` int(11) NOT NULL AUTO_INCREMENT,
  `craigslist_district_code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `craigslist_city_id` int(11) NOT NULL,
  PRIMARY KEY (`craigslist_district_id`),
  KEY `craigslist_city_id` (`craigslist_city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_headlines`
--

DROP TABLE IF EXISTS `craigslist_headlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_headlines` (
  `craigslist_headline_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `headline` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`craigslist_headline_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_market_links`
--

DROP TABLE IF EXISTS `craigslist_market_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_market_links` (
  `website_id` int(11) NOT NULL,
  `craigslist_market_id` int(11) NOT NULL,
  `market_id` int(11) NOT NULL,
  `cl_category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`website_id`,`craigslist_market_id`,`market_id`,`cl_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_markets`
--

DROP TABLE IF EXISTS `craigslist_markets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_markets` (
  `craigslist_market_id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_market_id` int(11) NOT NULL,
  `parent_market_id` int(11) NOT NULL,
  `state` varchar(30) NOT NULL,
  `city` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `submarket` tinyint(1) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`craigslist_market_id`),
  UNIQUE KEY `state` (`state`,`city`,`area`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_tags`
--

DROP TABLE IF EXISTS `craigslist_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_tags` (
  `craigslist_tag_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `type` enum('category','product') NOT NULL,
  PRIMARY KEY (`craigslist_tag_id`),
  KEY `category_id` (`object_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `craigslist_templates`
--

DROP TABLE IF EXISTS `craigslist_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `craigslist_templates` (
  `craigslist_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `publish_visibility` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`craigslist_template_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_associations`
--

DROP TABLE IF EXISTS `email_associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_associations` (
  `email_id` int(11) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  PRIMARY KEY (`email_id`,`email_list_id`),
  KEY `fk_eas_idx` (`email_id`),
  KEY `fk_eas2_idx` (`email_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_autoresponders`
--

DROP TABLE IF EXISTS `email_autoresponders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_autoresponders` (
  `email_autoresponder_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `email_list_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `current_offer` tinyint(1) NOT NULL DEFAULT '0',
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email_autoresponder_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1129 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_import_emails`
--

DROP TABLE IF EXISTS `email_import_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_import_emails` (
  `website_id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_created` datetime NOT NULL,
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_lists`
--

DROP TABLE IF EXISTS `email_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_lists` (
  `email_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `ac_list_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email_list_id`),
  KEY `fk_el_idx` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5770 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_message_associations`
--

DROP TABLE IF EXISTS `email_message_associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_message_associations` (
  `email_message_id` int(11) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  KEY `email_message_id` (`email_message_id`,`email_list_id`),
  KEY `fk_ema_idx` (`email_message_id`),
  KEY `fk_ema2_idx` (`email_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_message_meta`
--

DROP TABLE IF EXISTS `email_message_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_message_meta` (
  `email_message_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `value` text NOT NULL,
  KEY `email_message_id` (`email_message_id`,`type`),
  KEY `fk_emm_idx` (`email_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_messages`
--

DROP TABLE IF EXISTS `email_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_messages` (
  `email_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `email_template_id` int(11) NOT NULL,
  `mc_campaign_id` varchar(50) NOT NULL,
  `ac_campaign_id` int(11) DEFAULT NULL,
  `ac_message_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `from` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `date_sent` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`email_message_id`),
  KEY `website_id` (`website_id`),
  KEY `ac_index` (`ac_message_id`,`ac_campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2460 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_template_associations`
--

DROP TABLE IF EXISTS `email_template_associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_template_associations` (
  `email_template_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  KEY `email_template_id` (`email_template_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_template_options`
--

DROP TABLE IF EXISTS `email_template_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_template_options` (
  `email_template_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  KEY `email_template_id` (`email_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_templates` (
  `email_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `template` text NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumbnail` varchar(150) NOT NULL,
  `type` varchar(30) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`email_template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3182 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emails` (
  `email_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_unsubscribed` datetime NOT NULL,
  `date_synced` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email_id`),
  KEY `email` (`email`),
  KEY `fk_e_idx` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `industries`
--

DROP TABLE IF EXISTS `industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `industries` (
  `industry_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`industry_id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb_article`
--

DROP TABLE IF EXISTS `kb_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kb_category_id` int(11) DEFAULT NULL,
  `kb_page_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `content` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime DEFAULT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`kb_page_id`,`kb_category_id`,`user_id`),
  KEY `fk_kba_idx` (`kb_category_id`),
  KEY `fk_kba2_idx` (`kb_page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb_article_rating`
--

DROP TABLE IF EXISTS `kb_article_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_article_rating` (
  `kb_article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `UNIQUE` (`kb_article_id`,`user_id`),
  KEY `kb_article_id` (`kb_article_id`,`timestamp`,`rating`,`user_id`),
  KEY `fk_kbar_idx` (`kb_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb_article_view`
--

DROP TABLE IF EXISTS `kb_article_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_article_view` (
  `kb_article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `kb_article_id` (`user_id`,`kb_article_id`,`timestamp`),
  KEY `fk_kbav_idx` (`kb_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb_category`
--

DROP TABLE IF EXISTS `kb_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `section` enum('account','admin') DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=317 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kb_page`
--

DROP TABLE IF EXISTS `kb_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kb_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kb_category_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kb_category_id` (`kb_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mobile_pages`
--

DROP TABLE IF EXISTS `mobile_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobile_pages` (
  `mobile_page_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `updated_user_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mobile_page_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `success` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item` varchar(200) NOT NULL,
  `quantity` smallint(6) NOT NULL,
  `amount` float NOT NULL,
  `monthly` float NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2546 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` float NOT NULL,
  `total_monthly` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1289 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_group_relations`
--

DROP TABLE IF EXISTS `product_group_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_group_relations` (
  `product_group_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `product_group_id` (`product_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_groups`
--

DROP TABLE IF EXISTS `product_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_groups` (
  `product_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`product_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `product_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `sequence` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`product_image_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4064176 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_option_list_items`
--

DROP TABLE IF EXISTS `product_option_list_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_option_list_items` (
  `product_option_list_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_option_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`product_option_list_item_id`),
  KEY `product_option_id` (`product_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_option_relations`
--

DROP TABLE IF EXISTS `product_option_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_option_relations` (
  `product_option_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  KEY `product_option_id` (`product_option_id`,`brand_id`),
  KEY `fk_por_idx` (`product_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_options`
--

DROP TABLE IF EXISTS `product_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_options` (
  `product_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_type` varchar(10) NOT NULL,
  `option_title` varchar(100) NOT NULL,
  `option_name` varchar(250) NOT NULL,
  PRIMARY KEY (`product_option_id`)
) ENGINE=InnoDB AUTO_INCREMENT=938 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_specification`
--

DROP TABLE IF EXISTS `product_specification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_specification` (
  `product_id` int(11) NOT NULL,
  `key` text,
  `value` text,
  `sequence` int(11) DEFAULT NULL,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `industry_id` int(11) NOT NULL DEFAULT '1',
  `website_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `price_min` float NOT NULL,
  `price_net` float NOT NULL,
  `price_freight` float NOT NULL,
  `price_discount` float NOT NULL,
  `weight` float NOT NULL,
  `volume` float NOT NULL,
  `product_specifications` text NOT NULL,
  `publish_visibility` varchar(20) NOT NULL,
  `publish_date` datetime NOT NULL,
  `user_id_created` int(11) NOT NULL,
  `user_id_modified` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `depth` float NOT NULL,
  `height` float NOT NULL,
  `length` float NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `slug` (`slug`),
  KEY `sku` (`sku`,`name`),
  KEY `publish_visibility` (`publish_visibility`),
  KEY `brand_id` (`brand_id`,`industry_id`,`website_id`,`category_id`),
  FULLTEXT KEY `name` (`name`,`description`,`sku`)
) ENGINE=MyISAM AUTO_INCREMENT=202248 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `rating` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`rating_id`),
  UNIQUE KEY `UNIQUE` (`product_id`,`ip_address`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `server`
--

DROP TABLE IF EXISTS `server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_about_us`
--

DROP TABLE IF EXISTS `sm_about_us`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_about_us` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `website_page_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smau_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_contact_us`
--

DROP TABLE IF EXISTS `sm_contact_us`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_contact_us` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `website_page_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smcu_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_current_ad`
--

DROP TABLE IF EXISTS `sm_current_ad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_current_ad` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `website_page_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smca_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_email_sign_up`
--

DROP TABLE IF EXISTS `sm_email_sign_up`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_email_sign_up` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` bigint(20) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `tab` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smesu_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_facebook_page`
--

DROP TABLE IF EXISTS `sm_facebook_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_facebook_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_facebook_site`
--

DROP TABLE IF EXISTS `sm_facebook_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_facebook_site` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smfs_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_fan_offer`
--

DROP TABLE IF EXISTS `sm_fan_offer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_fan_offer` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `before` text NOT NULL,
  `after` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `share_title` varchar(100) NOT NULL,
  `share_image_url` varchar(200) NOT NULL,
  `share_text` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smfo_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_posting`
--

DROP TABLE IF EXISTS `sm_posting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_posting` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_user_id` bigint(20) NOT NULL,
  `fb_page_id` bigint(20) NOT NULL,
  `key` varchar(32) NOT NULL,
  `access_token` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `key` (`fb_page_id`,`key`),
  KEY `fk_smpo_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_posting_posts`
--

DROP TABLE IF EXISTS `sm_posting_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_posting_posts` (
  `sm_posting_post_id` int(11) NOT NULL AUTO_INCREMENT,
  `sm_facebook_page_id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `post` text NOT NULL,
  `link` varchar(200) NOT NULL,
  `error` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_posted` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sm_posting_post_id`),
  KEY `date_posted` (`date_posted`),
  KEY `fk_smpp_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18229 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_products`
--

DROP TABLE IF EXISTS `sm_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_products` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smp_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_share_and_save`
--

DROP TABLE IF EXISTS `sm_share_and_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_share_and_save` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  `maximum_email_list_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `before` text NOT NULL,
  `after` text NOT NULL,
  `minimum` int(11) NOT NULL,
  `maximum` int(11) NOT NULL,
  `share_title` varchar(100) NOT NULL,
  `share_image_url` varchar(200) NOT NULL,
  `share_text` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smsas_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sm_sweepstakes`
--

DROP TABLE IF EXISTS `sm_sweepstakes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sm_sweepstakes` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `before` text NOT NULL,
  `after` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `contest_rules_url` varchar(200) NOT NULL,
  `share_title` varchar(100) NOT NULL,
  `share_image_url` varchar(200) NOT NULL,
  `share_text` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_sms_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `value` (`value`),
  KEY `object_id` (`object_id`),
  FULLTEXT KEY `value_2` (`value`)
) ENGINE=MyISAM AUTO_INCREMENT=13201 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_comments`
--

DROP TABLE IF EXISTS `ticket_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_comments` (
  `ticket_comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `to_address` varchar(255) null default null,
  `cc_address` varchar(255) null default null,
  `bcc_address` varchar(255) null default null,
  `comment` text NOT NULL,
  `private` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `jira_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`ticket_comment_id`),
  KEY `ticket_id` (`ticket_id`,`user_id`),
  KEY `fk_tc_idx` (`ticket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ticket_uploads`
--

DROP TABLE IF EXISTS `ticket_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_uploads` (
  `ticket_upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `ticket_comment_id` int(11) NOT NULL,
  `key` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`ticket_upload_id`),
  KEY `ticket_id` (`ticket_id`,`ticket_comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `assigned_to_user_id` int(11) NOT NULL,
  `user_id_created` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `summary` varchar(140) NOT NULL,
  `message` text NOT NULL,
  `priority` tinyint(1) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `browser_name` varchar(50) NOT NULL,
  `browser_version` varchar(20) NOT NULL,
  `browser_platform` varchar(50) NOT NULL,
  `browser_user_agent` varchar(200) NOT NULL,
  `jira_id` int(11) NULL DEFAULT NULL,
  `jira_key` VARCHAR(255) NULL DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ticket_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `token_type` varchar(30) NOT NULL,
  `date_valid` datetime NOT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3300 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `work_phone` varchar(20) NOT NULL,
  `cell_phone` varchar(20) NOT NULL,
  `photo` varchar(255) NULL DEFAULT NULL,
  `billing_first_name` varchar(50) NOT NULL,
  `billing_last_name` varchar(50) NOT NULL,
  `billing_address1` varchar(150) NOT NULL,
  `billing_city` varchar(150) NOT NULL,
  `billing_state` varchar(50) NOT NULL,
  `billing_zip` varchar(10) NOT NULL,
  `arb_subscription_id` varchar(13) NOT NULL,
  `role` tinyint(2) NOT NULL DEFAULT '5',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `email_signature` TEXT NULL DEFAULT NULL,
  `last_login` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `new_features_dismissed_at` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_u_idx` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_attachments`
--

DROP TABLE IF EXISTS `website_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_attachments` (
  `website_attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_page_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `extra` varchar(200) NOT NULL,
  `meta` varchar(200) NOT NULL,
  `sequence` int(2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`website_attachment_id`,`website_page_id`,`key`),
  KEY `fk_wa_idx` (`website_page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28919 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_auto_price`
--

DROP TABLE IF EXISTS `website_auto_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_auto_price` (
  `website_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` float DEFAULT NULL,
  `sale_price` float DEFAULT NULL,
  `alternate_price` float DEFAULT NULL,
  `ending` float DEFAULT NULL,
  `future` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`website_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_blocked_category`
--

DROP TABLE IF EXISTS `website_blocked_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_blocked_category` (
  `website_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`website_id`,`category_id`),
  KEY `fk_wbc_idx` (`website_id`),
  KEY `fk_wbc2_idx` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_brand_category`
--

DROP TABLE IF EXISTS `website_brand_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_brand_category` (
  `website_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`,`brand_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_brands`
--

DROP TABLE IF EXISTS `website_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_brands` (
  `website_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `top` tinyint(1) NOT NULL DEFAULT '1',
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`,`brand_id`),
  KEY `fk_wca_idx` (`website_id`),
  KEY `fk_wca2_idx` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_cart_item_options`
--

DROP TABLE IF EXISTS `website_cart_item_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_cart_item_options` (
  `website_cart_item_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_option_list_item_id` int(11) NOT NULL,
  KEY `website_cart_item_id` (`website_cart_item_id`,`product_option_id`),
  KEY `fk_wcio_idx` (`website_cart_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_cart_items`
--

DROP TABLE IF EXISTS `website_cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_cart_items` (
  `website_cart_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_cart_id` int(11) NOT NULL,
  `product_id` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `weight` float NOT NULL DEFAULT '0',
  `protection` tinyint(1) NOT NULL,
  `extra` text NOT NULL COMMENT 'Serialized!',
  `date_created` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_cart_item_id`),
  KEY `cart_id` (`website_cart_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_carts`
--

DROP TABLE IF EXISTS `website_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_carts` (
  `website_cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `website_shipping_method_id` int(11) NOT NULL,
  `website_ashley_express_shipping_method_id` int(11) DEFAULT NULL,
  `website_coupon_id` int(11) NOT NULL,
  `expires` datetime NOT NULL,
  `zip` varchar(10) NOT NULL,
  `shipping_price` float NOT NULL,
  `tax_price` float NOT NULL,
  `coupon_discount` float NOT NULL,
  `total_price` float NOT NULL,
  `date_created` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_cart_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_categories`
--

DROP TABLE IF EXISTS `website_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_categories` (
  `website_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `image_url` varchar(200) NOT NULL,
  `top` tinyint(1) NOT NULL DEFAULT '1',
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `header_script` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`website_id`,`category_id`),
  KEY `fk_wca_idx` (`website_id`),
  KEY `fk_wca2_idx` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_coupon_relations`
--

DROP TABLE IF EXISTS `website_coupon_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_coupon_relations` (
  `website_coupon_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  KEY `website_coupon_id` (`website_coupon_id`,`product_id`),
  KEY `fk_wcr_idx` (`website_coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_coupon_shipping_methods`
--

DROP TABLE IF EXISTS `website_coupon_shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_coupon_shipping_methods` (
  `website_coupon_id` int(11) NOT NULL,
  `website_shipping_method_id` int(11) NOT NULL,
  KEY `fk_wcsm_idx` (`website_coupon_id`),
  KEY `fk_wcsm2_idx` (`website_shipping_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_coupons`
--

DROP TABLE IF EXISTS `website_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_coupons` (
  `website_coupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `code` varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  `amount` float NOT NULL,
  `minimum_purchase_amount` float NOT NULL,
  `store_wide` tinyint(1) NOT NULL,
  `buy_one_get_one_free` tinyint(1) NOT NULL,
  `item_limit` int(11) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`website_coupon_id`),
  KEY `code` (`code`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_files`
--

DROP TABLE IF EXISTS `website_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_files` (
  `website_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `file_path` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`website_file_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_industries`
--

DROP TABLE IF EXISTS `website_industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_industries` (
  `website_id` int(11) NOT NULL,
  `industry_id` int(11) NOT NULL,
  PRIMARY KEY (`website_id`,`industry_id`),
  KEY `fk_wi_idx` (`website_id`),
  KEY `fk_wi2_idx` (`industry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_location`
--

DROP TABLE IF EXISTS `website_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `store_hours` text,
  `store_image` varchar(500) NOT NULL DEFAULT '',
  `lat` varchar(20) DEFAULT NULL,
  `lng` varchar(20) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_notes`
--

DROP TABLE IF EXISTS `website_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_notes` (
  `website_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text CHARACTER SET latin1 NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_note_id`),
  KEY `website_id` (`website_id`,`user_id`),
  KEY `fk_wn_idx` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10512 DEFAULT CHARSET=utf8 COMMENT='Website notes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_order_item_options`
--

DROP TABLE IF EXISTS `website_order_item_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_order_item_options` (
  `website_order_item_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_option_list_item_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `option_type` varchar(10) NOT NULL,
  `option_name` varchar(250) NOT NULL,
  `list_item_value` varchar(100) NOT NULL,
  KEY `website_order_item_id` (`website_order_item_id`,`product_option_id`),
  KEY `fk_woio_idx` (`website_order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_order_items`
--

DROP TABLE IF EXISTS `website_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_order_items` (
  `website_order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` float NOT NULL,
  `additional_shipping_price` float NOT NULL,
  `protection_price` float NOT NULL,
  `extra` text NOT NULL,
  `price_note` varchar(50) NOT NULL,
  `product_note` text NOT NULL,
  `ships_in` varchar(60) NOT NULL,
  `store_sku` varchar(30) NOT NULL,
  `warranty_length` varchar(60) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`website_order_item_id`),
  KEY `website_order_id` (`website_order_id`,`sku`),
  KEY `fk_woi_idx` (`website_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3919 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_orders`
--

DROP TABLE IF EXISTS `website_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_orders` (
  `website_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `website_user_id` int(11) NOT NULL,
  `website_cart_id` int(11) NOT NULL,
  `website_shipping_method_id` int(11) NOT NULL,
  `website_ashley_express_shipping_method_id` int(11) DEFAULT NULL,
  `authorize_only` INT(1) NOT NULL DEFAULT 0,
  `website_coupon_id` int(11) NOT NULL,
  `shipping_price` float NOT NULL,
  `tax_price` float NOT NULL,
  `coupon_discount` float NOT NULL,
  `total_cost` float NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `billing_name` varchar(100) DEFAULT NULL,
  `billing_first_name` varchar(50) NOT NULL,
  `billing_last_name` varchar(50) NOT NULL,
  `billing_address1` varchar(100) NOT NULL,
  `billing_address2` varchar(100) NOT NULL,
  `billing_city` varchar(100) NOT NULL,
  `billing_state` varchar(30) NOT NULL,
  `billing_zip` varchar(10) NOT NULL,
  `billing_phone` varchar(13) NOT NULL,
  `billing_alt_phone` varchar(13) NOT NULL,
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_first_name` varchar(50) NOT NULL,
  `shipping_last_name` varchar(50) NOT NULL,
  `shipping_address1` varchar(100) NOT NULL,
  `shipping_address2` varchar(100) NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_state` varchar(30) NOT NULL,
  `shipping_zip` varchar(10) NOT NULL,
  `shipping_track_number` text,
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`website_order_id`),
  KEY `website_user_id` (`website_user_id`),
  KEY `fk_wo_idx` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2652 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_page_product`
--

DROP TABLE IF EXISTS `website_page_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_page_product` (
  `website_page_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`website_page_id`,`product_id`),
  KEY `fk_wpp_idx` (`website_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_pagemeta`
--

DROP TABLE IF EXISTS `website_pagemeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_pagemeta` (
  `website_pagemeta_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_page_id` int(11) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`website_pagemeta_id`),
  UNIQUE KEY `website_page_id` (`website_page_id`,`key`),
  KEY `fk_pm_idx` (`website_page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_pages`
--

DROP TABLE IF EXISTS `website_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_pages` (
  `website_page_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `mobile` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `top` tinyint(1) NOT NULL DEFAULT '1',
  `updated_user_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `header_script` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`website_page_id`),
  UNIQUE KEY `website_id` (`website_id`,`slug`),
  KEY `fk_wp_idx` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_ashley_express`
--

DROP TABLE IF EXISTS `website_product_ashley_express`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_product_ashley_express` (
  `website_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`website_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_group_relations`
--

DROP TABLE IF EXISTS `website_product_group_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_product_group_relations` (
  `website_product_group_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`website_product_group_id`,`product_id`),
  KEY `fk_wpgr_idx` (`website_product_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_groups`
--

DROP TABLE IF EXISTS `website_product_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_product_groups` (
  `website_product_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`website_product_group_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1221385 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_option_list_items`
--

DROP TABLE IF EXISTS `website_product_option_list_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_product_option_list_items` (
  `website_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_option_list_item_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `alt_price` float DEFAULT NULL,
  `alt_price2` float DEFAULT NULL,
  KEY `website_id` (`website_id`,`product_id`,`product_option_id`,`product_option_list_item_id`),
  KEY `fk_website_product_option_list_items_idx` (`website_id`),
  KEY `fk_wpoli_idx` (`product_option_id`),
  KEY `fk_wpoli2_idx` (`product_option_list_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_options`
--

DROP TABLE IF EXISTS `website_product_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_product_options` (
  `website_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`website_id`,`product_id`,`product_option_id`),
  KEY `fk_website_product_options_idx` (`website_id`),
  KEY `fk_wpo_idx` (`product_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_view`
--

DROP TABLE IF EXISTS `website_product_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_product_view` (
  `website_product_view_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `ip` char(15) CHARACTER SET latin1 DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`website_product_view_id`),
  KEY `website_id` (`website_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_product_views`
--

DROP TABLE IF EXISTS `website_product_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_product_views` (
  `website_product_view_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `ip` char(15) CHARACTER SET latin1 DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`website_product_view_id`),
  KEY `website_id` (`website_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_products`
--

DROP TABLE IF EXISTS `website_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_products` (
  `website_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `alternate_price` float NOT NULL,
  `price` float NOT NULL,
  `sale_price` float NOT NULL,
  `wholesale_price` float NOT NULL,
  `inventory` int(11) NOT NULL,
  `additional_shipping_amount` float NOT NULL,
  `weight` float NOT NULL,
  `protection_amount` float NOT NULL,
  `additional_shipping_type` varchar(20) NOT NULL,
  `alternate_price_name` varchar(30) NOT NULL DEFAULT 'List Price',
  `meta_title` varchar(200) NOT NULL,
  `meta_description` varchar(250) NOT NULL,
  `meta_keywords` varchar(200) NOT NULL,
  `protection_type` varchar(20) NOT NULL,
  `price_note` varchar(100) NOT NULL,
  `product_note` text NOT NULL,
  `ships_in` varchar(60) NOT NULL,
  `store_sku` varchar(30) NOT NULL,
  `warranty_length` varchar(60) NOT NULL,
  `alternate_price_strikethrough` tinyint(1) NOT NULL,
  `display_inventory` tinyint(1) NOT NULL,
  `on_sale` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sequence` int(11) NOT NULL DEFAULT '100000',
  `blocked` int(1) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '1',
  `manual_price` int(1) NOT NULL DEFAULT '0',
  `setup_fee` float DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`,`product_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_reach_comments`
--

DROP TABLE IF EXISTS `website_reach_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_reach_comments` (
  `website_reach_comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_reach_id` int(11) NOT NULL,
  `website_user_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_reach_comment_id`),
  KEY `website_reach_id` (`website_reach_id`,`website_user_id`,`user_id`),
  KEY `fk_website_reach_comments_idx` (`website_reach_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_reach_meta`
--

DROP TABLE IF EXISTS `website_reach_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_reach_meta` (
  `website_reach_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  KEY `website_reach_id` (`website_reach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_reaches`
--

DROP TABLE IF EXISTS `website_reaches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_reaches` (
  `website_reach_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `website_user_id` int(11) NOT NULL,
  `assigned_to_user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `waiting` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `assigned_to_date` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`website_reach_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=110366 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_settings`
--

DROP TABLE IF EXISTS `website_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_settings` (
  `website_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`website_id`,`key`),
  KEY `fk_website_settings_idx` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_shipping_methods`
--

DROP TABLE IF EXISTS `website_shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_shipping_methods` (
  `website_shipping_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `method` varchar(20) NOT NULL,
  `amount` float NOT NULL,
  `zip_codes` text NOT NULL,
  `extra` text NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`website_shipping_method_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=963 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_tokens`
--

DROP TABLE IF EXISTS `website_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_tokens` (
  `website_token_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `match` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date_valid` datetime NOT NULL,
  PRIMARY KEY (`website_token_id`),
  KEY `key` (`key`,`match`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_top_brands`
--

DROP TABLE IF EXISTS `website_top_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_top_brands` (
  `website_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`website_id`,`brand_id`),
  KEY `fk_website_top_brands_idx` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_users`
--

DROP TABLE IF EXISTS `website_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_users` (
  `website_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `billing_name` varchar(100) DEFAULT NULL,
  `billing_first_name` varchar(50) DEFAULT NULL,
  `billing_last_name` varchar(50) DEFAULT NULL,
  `billing_address1` varchar(100) DEFAULT NULL,
  `billing_address2` varchar(100) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(30) DEFAULT NULL,
  `billing_zip` varchar(10) DEFAULT NULL,
  `billing_phone` varchar(13) NOT NULL,
  `billing_alt_phone` varchar(13) NOT NULL,
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_first_name` varchar(50) DEFAULT NULL,
  `shipping_last_name` varchar(50) DEFAULT NULL,
  `shipping_address1` varchar(100) DEFAULT NULL,
  `shipping_address2` varchar(100) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(30) DEFAULT NULL,
  `shipping_zip` varchar(10) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `date_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_user_id`),
  KEY `email` (`email`),
  KEY `fk_website_users_idx` (`website_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76329 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `website_wishlist`
--

DROP TABLE IF EXISTS `website_wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `website_wishlist` (
  `website_wishlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_options` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`website_wishlist_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `websites`
--

DROP TABLE IF EXISTS `websites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `websites` (
  `website_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_package_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `os_user_id` int(11) NOT NULL,
  `user_id_updated` int(11) DEFAULT NULL,
  `server_id` int(11) NOT NULL,
  `domain` varchar(150) NOT NULL,
  `subdomain` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT 'Website Title',
  `plan_name` varchar(200) NOT NULL,
  `plan_description` text NOT NULL,
  `theme` varchar(50) NOT NULL DEFAULT 'theme1',
  `logo` varchar(200) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `pages` tinyint(4) NOT NULL DEFAULT '1',
  `mobile_pages` tinyint(4) NOT NULL DEFAULT '0',
  `products` int(11) NOT NULL DEFAULT '200',
  `product_catalog` tinyint(1) NOT NULL DEFAULT '1',
  `link_brands` tinyint(1) NOT NULL DEFAULT '0',
  `blog` tinyint(1) NOT NULL,
  `email_marketing` tinyint(1) NOT NULL,
  `mobile_marketing` tinyint(1) NOT NULL,
  `shopping_cart` tinyint(1) NOT NULL,
  `geo_marketing` tinyint(1) NOT NULL DEFAULT '0',
  `seo` tinyint(4) NOT NULL,
  `room_planner` tinyint(1) NOT NULL,
  `craigslist` tinyint(1) NOT NULL,
  `social_media` tinyint(4) NOT NULL,
  `domain_registration` tinyint(1) NOT NULL,
  `additional_email_addresses` smallint(6) NOT NULL,
  `ftp_host` varchar(100) NOT NULL,
  `ftp_username` varchar(100) NOT NULL,
  `ftp_password` varchar(100) NOT NULL,
  `ga_profile_id` int(11) NOT NULL,
  `ga_tracking_key` varchar(20) NOT NULL,
  `wordpress_username` varchar(100) NOT NULL,
  `wordpress_password` varchar(100) NOT NULL,
  `mc_list_id` varchar(20) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT 'Furniture',
  `version` varchar(20) NOT NULL DEFAULT '0',
  `live` tinyint(1) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`),
  KEY `user_id` (`user_id`,`os_user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1677 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-11-06 15:25:32