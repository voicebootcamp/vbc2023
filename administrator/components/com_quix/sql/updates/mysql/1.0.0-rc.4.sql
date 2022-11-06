-- update text commented
ALTER TABLE `#__quix_collections` CHANGE `type` `type` ENUM('layout','section','header','footer') NOT NULL DEFAULT 'section';
