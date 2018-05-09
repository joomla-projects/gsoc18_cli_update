ALTER TABLE `#__it_projects`
ADD `assignee` int(11) NOT NULL DEFAULT '0' COMMENT 'A specified default assignee for the project.' AFTER state;
