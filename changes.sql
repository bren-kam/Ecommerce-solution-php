CREATE TABLE `gsr_system`.`products_amazon` (
  `product_amazon_id` INT NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_amazon_id`));


ALTER TABLE `gsr_system`.`website_carts`
ADD COLUMN `website_amazon_shipping_method_id` INT(11) NULL DEFAULT NULL AFTER `website_ashley_express_shipping_method_id`;

ALTER TABLE `gsr_system`.`product_import`
ADD COLUMN `amazon_eligible` VARCHAR(2) NULL DEFAULT NULL AFTER `type`;
