ALTER TABLE `#__it_issues`
ADD `metadata` text NOT NULL AFTER access;

ALTER TABLE `#__it_projects`
ADD `metadata` text NOT NULL AFTER actual_end_date;