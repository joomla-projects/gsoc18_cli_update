UPDATE `#__it_projects` SET alias='root' WHERE alias is null AND title = 'Root';

CREATE OR REPLACE VIEW `#__it_project_view1` AS
SELECT B.id, B.parent_id AS pid, C.level, C.title 
FROM `#__it_projects` AS B, `#__it_projects` AS C 
WHERE (B.lft BETWEEN C.lft AND C.rgt) 
AND C.level != 0 
ORDER BY B.lft, C.lft;

CREATE OR REPLACE VIEW `#__it_project_view2` AS
SELECT id, GROUP_CONCAT(title ORDER BY level ASC SEPARATOR ' - ') AS title 
FROM `#__it_project_view1` GROUP BY id;