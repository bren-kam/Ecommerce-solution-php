----- Change Categories to remove unneeded columns -----

-- WARNING: Make sure it's not being used on front websites
ALTER TABLE `categories` DROP `page_title` ,
DROP `meta_description` ,
DROP `meta_keywords` ;

----- Change Product Options to rename columns ------

-- WARNING: Make sure it's not being used on front websites
ALTER TABLE `product_options` CHANGE `option_type` `type` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `option_title` `title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE `option_name` `name` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

----- Remove Product Options Unique -----
DROP TABLE `product_options_unique`;

------ Change Attribute Items to rename columns -----
ALTER TABLE `attribute_items` CHANGE `attribute_item_name` `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

------ Change Website Pages to default to active -----
ALTER TABLE `website_pages` CHANGE `status` `status` TINYINT( 1 ) NOT NULL DEFAULT '1';