#
#<?php die('Forbidden.'); ?>
#Date: 2022-11-03 22:10:02 UTC
#Software: Joomla! 4.1.4 Stable [ Kuamini ] 24-May-2022 16:00 GMT

#Fields: datetime	priority clientip	category	message
2022-11-03T22:10:02+00:00	INFO 63.239.71.146	update	Starting installation of new version.
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	Finalising installation.
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	Start of SQL updates.
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	The current database version (schema) is 4.1.3-2022-04-08.
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-05-15. Query text: CREATE TABLE IF NOT EXISTS `#__user_mfa` (   `id` int NOT NULL AUTO_INCREMENT,  .
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-05-15. Query text: DELETE FROM `#__postinstall_messages` WHERE `condition_file` = 'site://plugins/t.
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-05-15. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-05-15. Query text: UPDATE `#__extensions` AS `a` 	INNER JOIN `#__extensions` AS `b` on `a`.`element.
2022-11-03T22:10:07+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-05-15. Query text: DELETE FROM `#__extensions` WHERE `type` = 'plugin' AND `folder` = 'twofactoraut.
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-05-15. Query text: INSERT IGNORE INTO `#__postinstall_messages` (`extension_id`, `title_key`, `desc.
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-05-15. Query text: INSERT IGNORE INTO `#__mail_templates` (`template_id`, `extension`, `language`, .
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-06-15. Query text: ALTER TABLE `#__mail_templates` MODIFY `htmlbody` mediumtext NOT NULL COLLATE 'u.
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-06-19. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.0-2022-06-22. Query text: UPDATE `#__extensions` SET `locked` = 1 WHERE  (`type` = 'plugin' AND     (     .
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.1-2022-08-23. Query text: DELETE FROM `#__extensions` WHERE `name` = 'plg_fields_menuitem' AND `type` = 'p.
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Ran query from file 4.2.3-2022-09-07. Query text: DELETE FROM `#__template_overrides` WHERE `template` NOT IN (SELECT `name` FROM .
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	End of SQL updates.
2022-11-03T22:10:08+00:00	INFO 63.239.71.146	update	Deleting removed files and folders.
2022-11-03T22:10:13+00:00	INFO 63.239.71.146	update	Cleaning up after installation.
2022-11-03T22:10:14+00:00	INFO 63.239.71.146	update	Update to version 4.2.3 is complete.
