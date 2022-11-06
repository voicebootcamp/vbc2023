-- update text commented

CREATE TABLE IF NOT EXISTS `#__quix_collections` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`uid` VARCHAR(255)  NOT NULL ,
	`title` VARCHAR(255)  NOT NULL ,
	`type` ENUM('layout','section', 'header', 'footer') NOT NULL DEFAULT 'section',
	`catid` int(11) NOT NULL,
	`data` LONGTEXT NOT NULL ,
	`metadata` LONGTEXT NOT NULL,
	`language` VARCHAR(5)  NOT NULL ,
	`ordering` INT(11)  NOT NULL ,
	`state` TINYINT(1)  NOT NULL ,
	`access` INT(11)  NOT NULL ,
	`created_by` INT(11)  NOT NULL ,
	`checked_out` INT(11)  NOT NULL ,
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`params` LONGTEXT NOT NULL ,
	`version` int(10) unsigned NOT NULL DEFAULT '1',
	`hits` int(11) NOT NULL,
	`xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
	PRIMARY KEY (`id`),
	KEY `idx_access` (`access`),
	KEY `idx_catid` (`catid`),
	KEY `idx_state` (`state`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_xreference` (`xreference`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__quix_collection_map` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`cid` int(11) UNSIGNED NOT NULL,
	`pid` int(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
