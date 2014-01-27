-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 22, 2013 at 06:47 AM
-- Server version: 5.6.5
-- PHP Version: 5.3.2-1ubuntu4.19

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `imaginer_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics_craigslist`
--

CREATE TABLE IF NOT EXISTS `analytics_craigslist` (
  `website_id` int(11) NOT NULL,
  `craigslist_market_id` int(11) NOT NULL,
  `craigslist_tag_id` int(11) NOT NULL,
  `unique` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `posts` int(11) NOT NULL,
  `date` datetime NOT NULL,
  KEY `object_id` (`website_id`,`craigslist_market_id`,`craigslist_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_emails`
--

CREATE TABLE IF NOT EXISTS `analytics_emails` (
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

-- --------------------------------------------------------

--
-- Table structure for table `api_ext_log`
--

CREATE TABLE IF NOT EXISTS `api_ext_log` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4753 ;

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE IF NOT EXISTS `api_keys` (
  `api_key_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`api_key_id`),
  KEY `company_id` (`company_id`,`brand_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `api_log`
--

CREATE TABLE IF NOT EXISTS `api_log` (
  `api_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `method` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `success` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`api_log_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23423 ;

-- --------------------------------------------------------

--
-- Table structure for table `api_settings`
--

CREATE TABLE IF NOT EXISTS `api_settings` (
  `api_key_id` int(11) NOT NULL,
  `key` varchar(200) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`api_key_id`,`key`),
  KEY `fk_as_idx` (`api_key_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=534 ;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_items`
--

CREATE TABLE IF NOT EXISTS `attribute_items` (
  `attribute_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `attribute_item_name` text NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`attribute_item_id`),
  KEY `attribute_id` (`attribute_id`),
  FULLTEXT KEY `attribute_item_name` (`attribute_item_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2350 ;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_item_relations`
--

CREATE TABLE IF NOT EXISTS `attribute_item_relations` (
  `attribute_item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`attribute_item_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_relations`
--

CREATE TABLE IF NOT EXISTS `attribute_relations` (
  `attribute_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  KEY `category_id` (`category_id`),
  KEY `fk_ar_idx` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `auth_user_websites`
--

CREATE TABLE IF NOT EXISTS `auth_user_websites` (
  `auth_user_website_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `pages` tinyint(1) NOT NULL DEFAULT '0',
  `products` tinyint(1) NOT NULL DEFAULT '0',
  `analytics` tinyint(1) NOT NULL DEFAULT '0',
  `blog` tinyint(1) NOT NULL DEFAULT '0',
  `email_marketing` tinyint(1) NOT NULL DEFAULT '0',
  `shopping_cart` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`auth_user_website_id`),
  KEY `user_id` (`user_id`,`website_id`),
  KEY `fk_auw_idx` (`website_id`),
  KEY `fk_auw2_idx` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4914 ;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE IF NOT EXISTS `brands` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `link` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  PRIMARY KEY (`brand_id`),
  KEY `name_2` (`name`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=781 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `google_taxonomy` varchar(255) NOT NULL,
  `sequence` int(11) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1162 ;

-- --------------------------------------------------------

--
-- Table structure for table `checklists`
--

CREATE TABLE IF NOT EXISTS `checklists` (
  `checklist_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_finished` datetime DEFAULT NULL,
  PRIMARY KEY (`checklist_id`),
  KEY `fk_c_idx` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1323 ;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_items`
--

CREATE TABLE IF NOT EXISTS `checklist_items` (
  `checklist_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `checklist_section_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `assigned_to` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`checklist_item_id`),
  KEY `fk_ci_idx` (`checklist_section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_sections`
--

CREATE TABLE IF NOT EXISTS `checklist_sections` (
  `checklist_section_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`checklist_section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_website_items`
--

CREATE TABLE IF NOT EXISTS `checklist_website_items` (
  `checklist_website_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `checklist_id` int(11) NOT NULL,
  `checklist_item_id` int(11) NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `date_checked` datetime DEFAULT NULL,
  PRIMARY KEY (`checklist_website_item_id`),
  KEY `checklist_id` (`checklist_id`,`checklist_item_id`),
  KEY `fk_cwi_idx` (`checklist_id`),
  KEY `fk_cwi2_idx` (`checklist_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=55231 ;

-- --------------------------------------------------------

--
-- Table structure for table `checklist_website_item_notes`
--

CREATE TABLE IF NOT EXISTS `checklist_website_item_notes` (
  `checklist_website_item_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `checklist_website_item_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`checklist_website_item_note_id`),
  KEY `checklist_item_id` (`checklist_website_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7135 ;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `company_packages`
--

CREATE TABLE IF NOT EXISTS `company_packages` (
  `company_package_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`company_package_id`),
  KEY `company_id` (`company_id`,`website_id`),
  KEY `fk_cp_idx` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_ads`
--

CREATE TABLE IF NOT EXISTS `craigslist_ads` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Craigslist ads that account-side customers post' AUTO_INCREMENT=2659 ;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_ad_headlines`
--

CREATE TABLE IF NOT EXISTS `craigslist_ad_headlines` (
  `craigslist_ad_id` int(11) NOT NULL,
  `headline` varchar(250) NOT NULL,
  KEY `craigslist_ad_id` (`craigslist_ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_ad_markets`
--

CREATE TABLE IF NOT EXISTS `craigslist_ad_markets` (
  `craigslist_ad_id` int(11) NOT NULL,
  `craigslist_market_id` int(11) NOT NULL,
  `primus_product_id` int(11) NOT NULL,
  PRIMARY KEY (`craigslist_ad_id`,`craigslist_market_id`,`primus_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_categories`
--

CREATE TABLE IF NOT EXISTS `craigslist_categories` (
  `craigslist_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `craigslist_category_code` varchar(3) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  PRIMARY KEY (`craigslist_category_id`),
  UNIQUE KEY `craigslist_category_code` (`craigslist_category_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_cities`
--

CREATE TABLE IF NOT EXISTS `craigslist_cities` (
  `craigslist_city_id` int(11) NOT NULL AUTO_INCREMENT,
  `craigslist_city_code` varchar(3) NOT NULL,
  `city_name` varchar(20) NOT NULL,
  `state_name` varchar(20) NOT NULL,
  `country` varchar(20) NOT NULL,
  PRIMARY KEY (`craigslist_city_id`),
  UNIQUE KEY `craigslist_city_code` (`craigslist_city_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=708 ;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_districts`
--

CREATE TABLE IF NOT EXISTS `craigslist_districts` (
  `craigslist_district_id` int(11) NOT NULL AUTO_INCREMENT,
  `craigslist_district_code` varchar(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `craigslist_city_id` int(11) NOT NULL,
  PRIMARY KEY (`craigslist_district_id`),
  KEY `craigslist_city_id` (`craigslist_city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_headlines`
--

CREATE TABLE IF NOT EXISTS `craigslist_headlines` (
  `craigslist_headline_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `headline` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`craigslist_headline_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_markets`
--

CREATE TABLE IF NOT EXISTS `craigslist_markets` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=575 ;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_market_links`
--

CREATE TABLE IF NOT EXISTS `craigslist_market_links` (
  `website_id` int(11) NOT NULL,
  `craigslist_market_id` int(11) NOT NULL,
  `market_id` int(11) NOT NULL,
  `cl_category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`website_id`,`craigslist_market_id`,`market_id`,`cl_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_tags`
--

CREATE TABLE IF NOT EXISTS `craigslist_tags` (
  `craigslist_tag_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `type` enum('category','product') NOT NULL,
  PRIMARY KEY (`craigslist_tag_id`),
  KEY `category_id` (`object_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `craigslist_templates`
--

CREATE TABLE IF NOT EXISTS `craigslist_templates` (
  `craigslist_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `publish_visibility` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`craigslist_template_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=173 ;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=267248 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_associations`
--

CREATE TABLE IF NOT EXISTS `email_associations` (
  `email_id` int(11) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  PRIMARY KEY (`email_id`,`email_list_id`),
  KEY `fk_eas_idx` (`email_id`),
  KEY `fk_eas2_idx` (`email_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_autoresponders`
--

CREATE TABLE IF NOT EXISTS `email_autoresponders` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1054 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_import_emails`
--

CREATE TABLE IF NOT EXISTS `email_import_emails` (
  `website_id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_created` datetime NOT NULL,
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_lists`
--

CREATE TABLE IF NOT EXISTS `email_lists` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5637 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_messages`
--

CREATE TABLE IF NOT EXISTS `email_messages` (
  `email_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `email_template_id` int(11) NOT NULL,
  `mc_campaign_id` varchar(50) NOT NULL,
  `ac_campaign_id` int(11) DEFAULT NULL,
  `ac_message_id` int(11) DEFAULT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `date_sent` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`email_message_id`),
  KEY `website_id` (`website_id`),
  KEY `ac_index` (`ac_message_id`,`ac_campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2366 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_message_associations`
--

CREATE TABLE IF NOT EXISTS `email_message_associations` (
  `email_message_id` int(11) NOT NULL,
  `email_list_id` int(11) NOT NULL,
  KEY `email_message_id` (`email_message_id`,`email_list_id`),
  KEY `fk_ema_idx` (`email_message_id`),
  KEY `fk_ema2_idx` (`email_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_message_meta`
--

CREATE TABLE IF NOT EXISTS `email_message_meta` (
  `email_message_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `value` text NOT NULL,
  KEY `email_message_id` (`email_message_id`,`type`),
  KEY `fk_emm_idx` (`email_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE IF NOT EXISTS `email_templates` (
  `email_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `template` text NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumbnail` varchar(150) NOT NULL,
  `type` varchar(30) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`email_template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3111 ;

-- --------------------------------------------------------

--
-- Table structure for table `email_template_associations`
--

CREATE TABLE IF NOT EXISTS `email_template_associations` (
  `email_template_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  KEY `email_template_id` (`email_template_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_template_options`
--

CREATE TABLE IF NOT EXISTS `email_template_options` (
  `email_template_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  KEY `email_template_id` (`email_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `industries`
--

CREATE TABLE IF NOT EXISTS `industries` (
  `industry_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`industry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `kb_article`
--

CREATE TABLE IF NOT EXISTS `kb_article` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=196 ;

-- --------------------------------------------------------

--
-- Table structure for table `kb_article_rating`
--

CREATE TABLE IF NOT EXISTS `kb_article_rating` (
  `kb_article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `UNIQUE` (`kb_article_id`,`user_id`),
  KEY `kb_article_id` (`kb_article_id`,`timestamp`,`rating`,`user_id`),
  KEY `fk_kbar_idx` (`kb_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `kb_article_view`
--

CREATE TABLE IF NOT EXISTS `kb_article_view` (
  `kb_article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `kb_article_id` (`user_id`,`kb_article_id`,`timestamp`),
  KEY `fk_kbav_idx` (`kb_article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `kb_category`
--

CREATE TABLE IF NOT EXISTS `kb_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `section` enum('account','admin') DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=131 ;

-- --------------------------------------------------------

--
-- Table structure for table `kb_page`
--

CREATE TABLE IF NOT EXISTS `kb_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kb_category_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kb_category_id` (`kb_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_associations`
--

CREATE TABLE IF NOT EXISTS `mobile_associations` (
  `mobile_subscriber_id` int(11) NOT NULL,
  `mobile_list_id` int(11) NOT NULL,
  `trumpia_contact_id` int(11) NOT NULL,
  PRIMARY KEY (`mobile_subscriber_id`,`mobile_list_id`,`trumpia_contact_id`),
  KEY `fk_ma_idx` (`mobile_subscriber_id`),
  KEY `fk_ma2_idx` (`mobile_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_keywords`
--

CREATE TABLE IF NOT EXISTS `mobile_keywords` (
  `mobile_keyword_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `keyword` varchar(50) NOT NULL,
  `response` varchar(140) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mobile_keyword_id`),
  KEY `am_keyword_campaign_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_keyword_lists`
--

CREATE TABLE IF NOT EXISTS `mobile_keyword_lists` (
  `mobile_keyword_id` int(11) NOT NULL,
  `mobile_list_id` int(11) NOT NULL,
  PRIMARY KEY (`mobile_keyword_id`,`mobile_list_id`),
  KEY `fk_mkl_idx` (`mobile_keyword_id`),
  KEY `fk_mkl2_idx` (`mobile_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_lists`
--

CREATE TABLE IF NOT EXISTS `mobile_lists` (
  `mobile_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `frequency` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mobile_list_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_messages`
--

CREATE TABLE IF NOT EXISTS `mobile_messages` (
  `mobile_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` varchar(160) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_sent` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mobile_message_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_message_associations`
--

CREATE TABLE IF NOT EXISTS `mobile_message_associations` (
  `mobile_message_id` int(11) NOT NULL,
  `mobile_list_id` int(11) NOT NULL,
  PRIMARY KEY (`mobile_message_id`,`mobile_list_id`),
  KEY `fk_mma_idx` (`mobile_message_id`),
  KEY `fk_mma2_idx` (`mobile_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_pages`
--

CREATE TABLE IF NOT EXISTS `mobile_pages` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_plans`
--

CREATE TABLE IF NOT EXISTS `mobile_plans` (
  `mobile_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `trumpia_plan_id` int(11) NOT NULL,
  `name` varchar(20) CHARACTER SET latin1 NOT NULL,
  `credits` int(11) NOT NULL,
  `keywords` int(11) NOT NULL,
  PRIMARY KEY (`mobile_plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `mobile_subscribers`
--

CREATE TABLE IF NOT EXISTS `mobile_subscribers` (
  `mobile_subscriber_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_unsubscribed` datetime NOT NULL,
  `date_synced` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mobile_subscriber_id`),
  UNIQUE KEY `website_id` (`website_id`,`phone`),
  KEY `fk_ms_idx` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=587 ;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `success` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36535 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` float NOT NULL,
  `total_monthly` float NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1279 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item` varchar(200) NOT NULL,
  `quantity` smallint(6) NOT NULL,
  `amount` float NOT NULL,
  `monthly` float NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2536 ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
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
  `price` float NOT NULL,
  `price_min` float NOT NULL,
  `weight` float NOT NULL,
  `volume` float NOT NULL,
  `product_specifications` text NOT NULL,
  `publish_visibility` varchar(20) NOT NULL,
  `publish_date` datetime NOT NULL,
  `user_id_created` int(11) NOT NULL,
  `user_id_modified` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `slug` (`slug`),
  KEY `sku` (`sku`,`name`),
  KEY `publish_visibility` (`publish_visibility`),
  KEY `brand_id` (`brand_id`,`industry_id`,`website_id`,`category_id`),
  FULLTEXT KEY `name` (`name`,`description`,`sku`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=201235 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_groups`
--

CREATE TABLE IF NOT EXISTS `product_groups` (
  `product_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`product_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=160 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_group_relations`
--

CREATE TABLE IF NOT EXISTS `product_group_relations` (
  `product_group_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  KEY `product_id` (`product_id`),
  KEY `product_group_id` (`product_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE IF NOT EXISTS `product_images` (
  `product_image_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`product_image_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4064020 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_options`
--

CREATE TABLE IF NOT EXISTS `product_options` (
  `product_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `option_type` varchar(10) NOT NULL,
  `option_title` varchar(100) NOT NULL,
  `option_name` varchar(250) NOT NULL,
  PRIMARY KEY (`product_option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=787 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_option_list_items`
--

CREATE TABLE IF NOT EXISTS `product_option_list_items` (
  `product_option_list_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_option_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`product_option_list_item_id`),
  KEY `product_option_id` (`product_option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=728 ;

-- --------------------------------------------------------

--
-- Table structure for table `product_option_relations`
--

CREATE TABLE IF NOT EXISTS `product_option_relations` (
  `product_option_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  KEY `product_option_id` (`product_option_id`,`brand_id`),
  KEY `fk_por_idx` (`product_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--  --
-- Table structure for table `product_specification`
--

CREATE TABLE `product_specification` (
  `product_id` INT NOT NULL ,
  `key` TEXT NULL ,
  `value` TEXT NULL ,
  `sequence` INT NULL ,
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `rating` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`rating_id`),
  UNIQUE KEY `UNIQUE` (`product_id`,`ip_address`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47882 ;

-- --------------------------------------------------------

--
-- Table structure for table `sm_about_us`
--

CREATE TABLE IF NOT EXISTS `sm_about_us` (
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

-- --------------------------------------------------------

--
-- Table structure for table `sm_contact_us`
--

CREATE TABLE IF NOT EXISTS `sm_contact_us` (
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

-- --------------------------------------------------------

--
-- Table structure for table `sm_current_ad`
--

CREATE TABLE IF NOT EXISTS `sm_current_ad` (
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

-- --------------------------------------------------------

--
-- Table structure for table `sm_email_sign_up`
--

CREATE TABLE IF NOT EXISTS `sm_email_sign_up` (
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

-- --------------------------------------------------------

--
-- Table structure for table `sm_facebook_page`
--

CREATE TABLE IF NOT EXISTS `sm_facebook_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=405 ;

-- --------------------------------------------------------

--
-- Table structure for table `sm_facebook_site`
--

CREATE TABLE IF NOT EXISTS `sm_facebook_site` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smfs_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sm_fan_offer`
--

CREATE TABLE IF NOT EXISTS `sm_fan_offer` (
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

-- --------------------------------------------------------

--
-- Table structure for table `sm_posting`
--

CREATE TABLE IF NOT EXISTS `sm_posting` (
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

-- --------------------------------------------------------

--
-- Table structure for table `sm_posting_posts`
--

CREATE TABLE IF NOT EXISTS `sm_posting_posts` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18135 ;

-- --------------------------------------------------------

--
-- Table structure for table `sm_products`
--

CREATE TABLE IF NOT EXISTS `sm_products` (
  `sm_facebook_page_id` int(11) NOT NULL,
  `fb_page_id` varchar(50) NOT NULL,
  `key` varchar(32) NOT NULL,
  `content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `fb_page_id` (`fb_page_id`),
  KEY `fk_smp_idx` (`sm_facebook_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sm_share_and_save`
--

CREATE TABLE IF NOT EXISTS `sm_share_and_save` (
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

-- --------------------------------------------------------

--
-- Table structure for table `sm_sweepstakes`
--

CREATE TABLE IF NOT EXISTS `sm_sweepstakes` (
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

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `object_id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `value` (`value`),
  KEY `object_id` (`object_id`),
  FULLTEXT KEY `value_2` (`value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13167 ;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `assigned_to_user_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `summary` varchar(140) NOT NULL,
  `message` text NOT NULL,
  `priority` tinyint(1) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `browser_name` varchar(50) NOT NULL,
  `browser_version` varchar(20) NOT NULL,
  `browser_platform` varchar(50) NOT NULL,
  `browser_user_agent` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ticket_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27373 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE IF NOT EXISTS `ticket_comments` (
  `ticket_comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `private` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ticket_comment_id`),
  KEY `ticket_id` (`ticket_id`,`user_id`),
  KEY `fk_tc_idx` (`ticket_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39004 ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_uploads`
--

CREATE TABLE IF NOT EXISTS `ticket_uploads` (
  `ticket_upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `ticket_comment_id` int(11) NOT NULL,
  `key` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`ticket_upload_id`),
  KEY `ticket_id` (`ticket_id`,`ticket_comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7538 ;

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE IF NOT EXISTS `tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `token_type` varchar(30) NOT NULL,
  `date_valid` datetime NOT NULL,
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3254 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `work_phone` varchar(20) NOT NULL,
  `cell_phone` varchar(20) NOT NULL,
  `billing_first_name` varchar(50) NOT NULL,
  `billing_last_name` varchar(50) NOT NULL,
  `billing_address1` varchar(150) NOT NULL,
  `billing_city` varchar(150) NOT NULL,
  `billing_state` varchar(50) NOT NULL,
  `billing_zip` varchar(10) NOT NULL,
  `arb_subscription_id` varchar(13) NOT NULL,
  `role` tinyint(2) NOT NULL DEFAULT '5',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `last_login` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_u_idx` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2647 ;

-- --------------------------------------------------------

--
-- Table structure for table `websites`
--

CREATE TABLE IF NOT EXISTS `websites` (
  `website_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_package_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `os_user_id` int(11) NOT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1378 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_attachments`
--

CREATE TABLE IF NOT EXISTS `website_attachments` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28714 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_auto_price`
--

CREATE TABLE IF NOT EXISTS `website_auto_price` (
  `website_id` INT NOT NULL ,
  `category_id` INT NOT NULL ,
  `price` FLOAT NULL ,
  `sale_price` FLOAT NULL ,
  `alternate_price` FLOAT NULL ,
  `ending` FLOAT NULL ,
  `future` TINYINT(1) NULL ,
  PRIMARY KEY (`website_id`, `category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_blocked_category`
--

CREATE TABLE IF NOT EXISTS `website_blocked_category` (
  `website_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`website_id`,`category_id`),
  KEY `fk_wbc_idx` (`website_id`),
  KEY `fk_wbc2_idx` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_carts`
--

CREATE TABLE IF NOT EXISTS `website_carts` (
  `website_cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `website_shipping_method_id` int(11) NOT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45862 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_cart_items`
--

CREATE TABLE IF NOT EXISTS `website_cart_items` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83383 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_cart_item_options`
--

CREATE TABLE IF NOT EXISTS `website_cart_item_options` (
  `website_cart_item_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_option_list_item_id` int(11) NOT NULL,
  KEY `website_cart_item_id` (`website_cart_item_id`,`product_option_id`),
  KEY `fk_wcio_idx` (`website_cart_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_categories`
--

CREATE TABLE IF NOT EXISTS `website_categories` (
  `website_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `image_url` varchar(200) NOT NULL,
  `top` tinyint(1) NOT NULL DEFAULT '1',
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`,`category_id`),
  KEY `fk_wca_idx` (`website_id`),
  KEY `fk_wca2_idx` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_coupons`
--

CREATE TABLE IF NOT EXISTS `website_coupons` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=252 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_coupon_relations`
--

CREATE TABLE IF NOT EXISTS `website_coupon_relations` (
  `website_coupon_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  KEY `website_coupon_id` (`website_coupon_id`,`product_id`),
  KEY `fk_wcr_idx` (`website_coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_coupon_shipping_methods`
--

CREATE TABLE IF NOT EXISTS `website_coupon_shipping_methods` (
  `website_coupon_id` int(11) NOT NULL,
  `website_shipping_method_id` int(11) NOT NULL,
  KEY `fk_wcsm_idx` (`website_coupon_id`),
  KEY `fk_wcsm2_idx` (`website_shipping_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_files`
--

CREATE TABLE IF NOT EXISTS `website_files` (
  `website_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `file_path` varchar(200) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`website_file_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18187 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_industries`
--

CREATE TABLE IF NOT EXISTS `website_industries` (
  `website_id` int(11) NOT NULL,
  `industry_id` int(11) NOT NULL,
  PRIMARY KEY (`website_id`,`industry_id`),
  KEY `fk_wi_idx` (`website_id`),
  KEY `fk_wi2_idx` (`industry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_location`
--

CREATE TABLE IF NOT EXISTS `website_location` (
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
  `lat` varchar(20) DEFAULT NULL,
  `lng` varchar(20) DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `INDEX` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1055 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_notes`
--

CREATE TABLE IF NOT EXISTS `website_notes` (
  `website_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text CHARACTER SET latin1 NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_note_id`),
  KEY `website_id` (`website_id`,`user_id`),
  KEY `fk_wn_idx` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Website notes' AUTO_INCREMENT=10442 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_orders`
--

CREATE TABLE IF NOT EXISTS `website_orders` (
  `website_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `website_user_id` int(11) NOT NULL,
  `website_cart_id` int(11) NOT NULL,
  `website_shipping_method_id` int(11) NOT NULL,
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
  `status` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`website_order_id`),
  KEY `website_user_id` (`website_user_id`),
  KEY `fk_wo_idx` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2571 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_order_items`
--

CREATE TABLE IF NOT EXISTS `website_order_items` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3867 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_order_item_options`
--

CREATE TABLE IF NOT EXISTS `website_order_item_options` (
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

-- --------------------------------------------------------

--
-- Table structure for table `website_pagemeta`
--

CREATE TABLE IF NOT EXISTS `website_pagemeta` (
  `website_pagemeta_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_page_id` int(11) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`website_pagemeta_id`),
  UNIQUE KEY `website_page_id` (`website_page_id`,`key`),
  KEY `fk_pm_idx` (`website_page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7376 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_pages`
--

CREATE TABLE IF NOT EXISTS `website_pages` (
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
  `updated_user_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_page_id`),
  UNIQUE KEY `website_id` (`website_id`,`slug`),
  KEY `fk_wp_idx` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10855 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_page_product`
--

CREATE TABLE IF NOT EXISTS `website_page_product` (
  `website_page_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`website_page_id`,`product_id`),
  KEY `fk_wpp_idx` (`website_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_products`
--

CREATE TABLE IF NOT EXISTS `website_products` (
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
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`website_id`,`product_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_product_groups`
--

CREATE TABLE IF NOT EXISTS `website_product_groups` (
  `website_product_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`website_product_group_id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1221307 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_product_group_relations`
--

CREATE TABLE IF NOT EXISTS `website_product_group_relations` (
  `website_product_group_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`website_product_group_id`,`product_id`),
  KEY `fk_wpgr_idx` (`website_product_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_product_options`
--

CREATE TABLE IF NOT EXISTS `website_product_options` (
  `website_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `required` tinyint(1) NOT NULL,
  PRIMARY KEY (`website_id`,`product_id`,`product_option_id`),
  KEY `fk_website_product_options_idx` (`website_id`),
  KEY `fk_wpo_idx` (`product_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_product_option_list_items`
--

CREATE TABLE IF NOT EXISTS `website_product_option_list_items` (
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

-- --------------------------------------------------------

--
-- Table structure for table `website_reaches`
--

CREATE TABLE IF NOT EXISTS `website_reaches` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=110297 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_reach_comments`
--

CREATE TABLE IF NOT EXISTS `website_reach_comments` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2614 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_reach_meta`
--

CREATE TABLE IF NOT EXISTS `website_reach_meta` (
  `website_reach_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  KEY `website_reach_id` (`website_reach_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE IF NOT EXISTS `website_settings` (
  `website_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`website_id`,`key`),
  KEY `fk_website_settings_idx` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_shipping_methods`
--

CREATE TABLE IF NOT EXISTS `website_shipping_methods` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=881 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_tokens`
--

CREATE TABLE IF NOT EXISTS `website_tokens` (
  `website_token_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `match` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date_valid` datetime NOT NULL,
  PRIMARY KEY (`website_token_id`),
  KEY `key` (`key`,`match`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=224 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_top_brands`
--

CREATE TABLE IF NOT EXISTS `website_top_brands` (
  `website_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`website_id`,`brand_id`),
  KEY `fk_website_top_brands_idx` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `website_users`
--

CREATE TABLE IF NOT EXISTS `website_users` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76229 ;
