CREATE TABLE IF NOT EXISTS `#__it_issues_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `priority` int(11) DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category_date_priority` (`category`,`date`,`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log table for various messages for Issue Tracker.';

ALTER TABLE `#__it_people` DROP INDEX `#__it_people_name_uk`;
ALTER TABLE `#__it_people`
  ADD INDEX `#__it_people_name_uk` (person_name, person_email);

ALTER TABLE `#__it_issues`
 ADD `public` tinyint(3) NOT NULL DEFAULT '1' COMMENT 'Whether issue is public or private.' AFTER status,
 CHANGE progress progress MEDIUMTEXT CHARACTER SET utf8 COMMENT 'Any progress notes on the issue resolution.',
 ADD INDEX `idx_checkout` (`checked_out`),
 ADD INDEX `idx_state` (`state`),
 ADD INDEX `idx_createdby` (`created_by`),
 ADD UNIQUE INDEX `idx_alias` (`alias`);
 
ALTER TABLE `#__it_projects`
 ADD `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.' AFTER id,
 ADD `lft` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested table left' AFTER alias,
 ADD `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested table right' AFTER lft,
 ADD `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Nested table level.' AFTER rgt,
 ADD `access` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Required for nested table.' AFTER title,
 ADD `path` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Required for nested table.' AFTER access,
 CHANGE project_name title VARCHAR(255) NOT NULL COMMENT 'The unique name of the project.',
 CHANGE project_description description VARCHAR(4000) COMMENT 'A full description of the project.',
 CHANGE alias alias VARCHAR(255) COMMENT 'Project Alias.  Used to mask primary key of issue from random selection.',
 ADD INDEX `idx_left_right` (`lft`,`rgt`);

CREATE TABLE IF NOT EXISTS `#__it_attachment` (
 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the attachment.',
 `issue_id` VARCHAR(10)  NOT NULL COMMENT 'Foreign key to associated issue',
 `uid` int(11) NULL COMMENT 'User id of the user attaching the file',
 `title` VARCHAR(255)  NOT NULL COMMENT 'Title for attachment',
 `description` MEDIUMTEXT NOT NULL COMMENT 'Description of the file attachment',
 `filepath` MEDIUMTEXT NOT NULL COMMENT 'Path to the file in the system',
 `filename` VARCHAR(255)  NOT NULL COMMENT 'Original name of the file attachment',
 `hashname` text NOT NULL COMMENT 'Hash of file name and date string',
 `filetype` VARCHAR(255)  NOT NULL DEFAULT 'application/octet-stream' COMMENT 'Type of file attachment',
 `size` INT(10)  NOT NULL COMMENT 'Size of file attachment',
 `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which issues are presented.',
 `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
 `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
 `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
 `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
 `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
 `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
 `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
 PRIMARY KEY (`id`),
 KEY `#__it_attachment_issue_id_fk` (`issue_id`),
  CONSTRAINT `#__it_attachment_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `#__it_issues` (`alias`) 
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Attachments for raised issues.';
 
 