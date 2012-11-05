CREATE TABLE `mobile_pages` (
  `mobile_page_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `updated_user_id` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`mobile_page_id`),
  KEY `website_id` (`website_id`)
) ENGINE=MyISAM;

ALTER TABLE `imaginer_system`.`website_pages` ADD COLUMN `mobile` TINYINT NOT NULL DEFAULT 0  AFTER `meta_keywords` ;
