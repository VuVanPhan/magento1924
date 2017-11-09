<?php
/**
 * Created by PhpStorm.
 * User: Vu Van Phan
 * Date: 12-10-2015
 * Time: 15:28
 */
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('sm_pagebuilder_page')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('sm_pagebuilder_page')}`(
	`page_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) DEFAULT NULL,
	`page_code` varchar(255) DEFAULT NULL,
	`page_shortcode` varchar(255) DEFAULT NULL,
	`status` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`show_from` date DEFAULT NULL,
	`show_to` date DEFAULT NULL,
	`customer_group` varchar(255) DEFAULT NULL,
	`position` int(11) unsigned NOT NULL DEFAULT '0',
	`prefix_class` varchar(255) DEFAULT NULL,
	`container` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`params` text DEFAULT NULL,
	`settings` text DEFAULT NULL,
	`layout_html` text DEFAULT NULL,
	`creation_time` datetime DEFAULT NULL,
	`update_time` datetime DEFAULT NULL,
	PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");