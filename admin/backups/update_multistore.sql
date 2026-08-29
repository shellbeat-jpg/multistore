-- Module Multstore
ALTER TABLE `admin_access` ADD `domain_konfigurator` INT( 1 ) NOT NULL AFTER `customers_id` ;
ALTER TABLE `admin_access` ADD `domain_manager` INT( 1 ) NOT NULL AFTER `customers_id` ;
ALTER TABLE `admin_access` ADD `readwrite` INT( 1 ) DEFAULT '1' NOT NULL AFTER `customers_id` ;
UPDATE admin_access SET readwrite = '1';
UPDATE admin_access SET domain_konfigurator = '1';
UPDATE admin_access SET domain_manager = '1';
ALTER TABLE `categories` ADD `string_domains` varchar(255) NOT NULL AFTER `categories_id` ;
ALTER TABLE `categories_description` ADD `domain_id` INT( 24 ) NOT NULL AFTER `categories_id` ;
ALTER TABLE `categories_description` DROP PRIMARY KEY, ADD PRIMARY KEY ( `categories_id` , `language_id` );
ALTER TABLE `categories_description` DROP PRIMARY KEY, ADD PRIMARY KEY ( `categories_id` , `language_id` , `domain_id` );
ALTER TABLE `customers_basket` ADD `sid` varchar(255)  NOT NULL AFTER `customers_basket_date_added` ;
ALTER TABLE `customers_basket` ADD `id_domain` INT( 24 ) NOT NULL AFTER `customers_basket_date_added` ;
ALTER TABLE `customers_basket_attributes` ADD `id_domain` INT( 24 ) NOT NULL AFTER `products_options_value_id` ;
ALTER TABLE `customers` ADD `id_domain` INT( 24 ) NOT NULL DEFAULT '1' AFTER `customers_id` ;
ALTER TABLE `whos_online` ADD `id_domain` INT( 12 ) NOT NULL AFTER `customer_id` ;
ALTER TABLE `content_manager` ADD `string_domains` varchar(255) NOT NULL AFTER `content_id` ;
ALTER TABLE `module_newsletter` ADD `string_domains` varchar(255) NOT NULL AFTER `newsletter_id` ;
ALTER TABLE `orders` ADD `store_name` varchar(255) NOT NULL AFTER `orders_id` ;
ALTER TABLE `orders` ADD `id_languages` int(4) NOT NULL DEFAULT '2' AFTER `orders_id` ;
ALTER TABLE `orders` ADD `id_domain` int(24) NOT NULL DEFAULT '1' AFTER `orders_id` ;
ALTER TABLE `orders` ADD `orders_id_shop` INT( 64 ) NOT NULL AFTER `orders_id`;
ALTER TABLE `products_description` ADD `domain_id` INT( 24 ) NOT NULL AFTER `products_id` ;
ALTER TABLE `products_description` DROP PRIMARY KEY, ADD PRIMARY KEY ( `products_id` , `language_id` );
ALTER TABLE `products_description` DROP PRIMARY KEY, ADD PRIMARY KEY ( `products_id` , `language_id` , `domain_id` );
ALTER TABLE `products_images` ADD `string_domains` varchar(255) NOT NULL AFTER `products_id` ;
ALTER TABLE `products_to_categories` ADD `date_added` date NOT NULL AFTER `categories_id` ;
ALTER TABLE `products_to_categories` ADD `src_category` int(24) NOT NULL AFTER `categories_id` ;
ALTER TABLE `newsletter_recipients` ADD `id_domain` int(24) NOT NULL AFTER `customers_id` ;
ALTER TABLE countries ADD `string_domains` varchar(255) NOT NULL AFTER `countries_name`;
ALTER TABLE `customers_status` ADD `string_domains` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `shipping_status` ADD `shipping_status_days` INT(4) NOT NULL AFTER `shipping_status_name`;

INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES
('MULTISTORE', 'false', 17, 1000, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('MULTISTORE_LICENSE', '', 17, 1001, NULL, '0000-00-00 00:00:00', NULL, NULL),
('ENABLE_SSL', 'false', 1, 1, NULL, '2014-09-21 21:46:35', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('USE_SSL_PROXY', 'false', 1, 1, NULL, '2014-09-21 21:46:35', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('ORDERS_NR_TYPE', '1', 17, 1006, NULL, '2013-01-29 09:52:40', 'xtc_get_order_nr_title', 'xtc_cfg_pull_down_order_nr_list('),
('MS_MULTIGROUPS', 'false', 17, 1006, NULL, '2013-01-29 09:52:40', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('MS_MULTIIMG', 'false', 17, 1006, NULL, '2013-01-29 09:52:40', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'),
('MS_MULTIBASKET', 'false', 17, 1005, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
UPDATE configuration SET configuration_id = '0' WHERE configuration_key = 'MULTISTORE';
INSERT INTO `configuration` (`configuration_key`, `configuration_value`, `configuration_group_id`, `sort_order`, `use_function`, `set_function`) VALUES
('MODULE_CATEGORIES_MULTISTORE2CATEGORIES_STATUS', 'true', 6, 1, NULL, 'xtc_cfg_select_option(array(''true'', ''false''), '),
('MODULE_CATEGORIES_MULTISTORE2CATEGORIES_SORT_ORDER', '10', 6, 2, NULL, NULL),
('MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_SORT_ORDER', '10', 6, 2, NULL, NULL),
('MODULE_CATEGORIES_MULTISTORE4DESCRIPTIONS_STATUS', 'false', 6, 1, NULL, 'xtc_cfg_select_option(array(''true'', ''false''), '),
('MODULE_ORDER_MULTISTORE4ORDERS_STATUS', 'true', 6, 1, NULL, 'xtc_cfg_select_option(array(''true'', ''false''), '),
('MODULE_ORDER_MULTISTORE4ORDERS_SORT_ORDER', '10', 6, 2, NULL, NULL),
('MODULE_SHOPPING_CART_MULTISTORE4CART_STATUS', 'true', 6, 1, NULL, 'xtc_cfg_select_option(array(''true'', ''false''), '),
('MODULE_SHOPPING_CART_MULTISTORE4CART_SORT_ORDER', '10', 6, 2, NULL, NULL);
CREATE TABLE `admin_access_domains` (
  `domain_id` int(24) NOT NULL,
  `customers_id` int(24) NOT NULL,
  PRIMARY KEY (`domain_id`,`customers_id`)
);
CREATE TABLE `domains` (
  `domain_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_http` varchar(64) DEFAULT 'www.server.com',
  `domain_https` varchar(64) DEFAULT 'www.server.com',
  `domain_user` varchar(255) NOT NULL DEFAULT 'www.server.com',
  `login_strict` varchar(1) NOT NULL,
  `current_template` varchar(64) NOT NULL DEFAULT '',
  `current_css` varchar(64) NOT NULL DEFAULT '',
  `default_currency` varchar(64) NOT NULL DEFAULT 'EUR',
  `default_tax` varchar(64) NOT NULL DEFAULT '81',
  `order_id_next` INT(64) NOT NULL,
  `id_languages` int(24) DEFAULT NULL,
  `domain_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`domain_id`)
);
CREATE TABLE `domains_configuration` (
  `domain_id` int(11) NOT NULL,
  `language_id` int(24) NOT NULL,
  `constant` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `value` longtext COLLATE latin1_german1_ci NOT NULL,
  `source` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `id` int(24) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain_id` (`domain_id`,`language_id`,`constant`)
);
CREATE TABLE `languages_to_domains` (
  `languages_id` int(24) NOT NULL,
  `domain_id` int(24) NOT NULL,
  PRIMARY KEY (`languages_id`,`domain_id`)
);
UPDATE `content_manager` set string_domains = 1;
UPDATE `configuration` SET `configuration_value` = 'multistore4cart.php' WHERE `configuration_key` = 'MODULE_SHOPPING_CART_INSTALLED';
UPDATE `configuration` SET `configuration_value` = 'multistore4orders.php' WHERE `configuration_key` = 'MODULE_ORDER_INSTALLED';
INSERT INTO `shop_configuration` (`configuration_key`, `configuration_value`) VALUES ('ORDER_IDS_1', '');
UPDATE `content_manager` set string_domains = 1;
UPDATE `customers` set id_domain = 1 where customers_id = 1;

# Keep an empty line at the end of this file for the installer to work properly