ALTER TABLE `#__it_projects`  DROP INDEX `idx_left_right`;
ALTER TABLE `#__it_projects` ADD KEY `idx_left_right` (`lft`,`rgt`);