CREATE TABLE IF NOT EXISTS `#__osmembership_categories` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `published` tinyint UNSIGNED DEFAULT '0',
  `exclusive_plans` tinyint NOT NULL DEFAULT '0',
  `grouping_plans` tinyint NOT NULL DEFAULT '0',
  `access` int UNSIGNED NOT NULL DEFAULT '1',
  `ordering` int NOT NULL DEFAULT '0',
  `parent_id` int NOT NULL DEFAULT '0',
  `level` tinyint NOT NULL DEFAULT '1',
  `alias` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
CREATE TABLE IF NOT EXISTS `#__osmembership_field_plan` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` int DEFAULT '0',
  `plan_id` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_messages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_key` varchar(50) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_states` (
  `id` int NOT NULL AUTO_INCREMENT,
  `country_id` int NOT NULL DEFAULT '1',
  `state_name` varchar(64) DEFAULT NULL,
  `state_3_code` char(10) DEFAULT NULL,
  `state_2_code` char(10) DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT '1',
  `state_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `state_3_code` (`country_id`,`state_3_code`),
  UNIQUE KEY `state_2_code` (`country_id`,`state_2_code`),
  KEY `idx_country_id` (`country_id`),
  KEY `idx_state_name` (`state_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_k2items` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `article_id` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_emails` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_type` varchar(50) DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `sent_to` tinyint NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_documents` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `ordering` int NOT NULL DEFAULT '0',
  `title` varchar(224) DEFAULT NULL,
  `attachment` varchar(225) DEFAULT NULL,
  `update_package` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_plan_documents` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `document_id` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_plan_id` (`plan_id`),
  KEY `idx_document_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_taxes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `country` varchar(255) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT '0.00',
  `vies` tinyint UNSIGNED DEFAULT '0',
  `published` tinyint UNSIGNED DEFAULT '0',
  `state` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_sefurls` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `md5_key` text,
  `query` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_articles` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `article_id` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_urls` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `url` text,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_schedulecontent` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `article_id` int DEFAULT '0',
  `number_days` int DEFAULT '0',
  `ordering` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_schedule_k2items` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `item_id` int DEFAULT '0',
  `number_days` int DEFAULT '0',
  `ordering` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_coupon_plans` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `coupon_id` int DEFAULT '0',
  `plan_id` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_renewaldiscounts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int NOT NULL DEFAULT '0',
  `number_days` int NOT NULL DEFAULT '0',
  `discount_type` tinyint NOT NULL DEFAULT '0',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `title` varchar(255) DEFAULT NULL,
  `published` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_downloadids` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT '0',
  `download_id` varchar(50) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `published` tinyint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_downloadlogs` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` int DEFAULT '0',
  `document_id` int DEFAULT '0',
  `version` varchar(50) DEFAULT NULL,
  `download_date` datetime DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `server_ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_sppagebuilder_pages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int NOT NULL DEFAULT '0',
  `page_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_schedule_sppagebuilder_pages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `page_id` int DEFAULT '0',
  `number_days` int DEFAULT '0',
  `ordering` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_scheduledocuments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` int DEFAULT '0',
  `document` varchar(255) DEFAULT NULL,
  `number_days` int DEFAULT '0',
  `ordering` int UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_checkinlogs` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscriber_id` int DEFAULT '0',
  `checkin_date` datetime DEFAULT NULL,
  `success` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `#__osmembership_mitems` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `title_en` varchar(400) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `group` tinyint unsigned DEFAULT 0,
  `translatable` tinyint unsigned DEFAULT 1,
  `featured` tinyint unsigned DEFAULT 0,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;