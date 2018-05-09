ALTER TABLE `#__it_issues`
ADD `access` int(10) unsigned NOT NULL DEFAULT '0' AFTER resolution_summary;

UPDATE `#__it_issues` SET access=1 where access = 0;
UPDATE `#__it_projects` SET access=1 where access = 0;

DROP VIEW IF EXISTS `#__it_project_view1`;
CREATE VIEW `#__it_project_view1` AS
SELECT B.id, B.parent_id AS pid, C.level, C.title, C.access 
FROM `#__it_projects` AS B, `#__it_projects` AS C 
WHERE (B.lft BETWEEN C.lft AND C.rgt) 
AND C.level != 0 
ORDER BY B.lft, C.lft;

DROP VIEW IF EXISTS `#__it_project_view2`;
CREATE VIEW `#__it_project_view2` AS
SELECT id, GROUP_CONCAT(title ORDER BY level ASC SEPARATOR ' - ') AS title, access 
FROM `#__it_project_view1` GROUP BY id;

