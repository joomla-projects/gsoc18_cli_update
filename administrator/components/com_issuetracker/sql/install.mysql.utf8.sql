CREATE TABLE IF NOT EXISTS `#__it_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `version` varchar(100) COMMENT 'Version number of the installed component.',
  `type`    varchar(20)  COMMENT 'Type of extension.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__it_meta` (id, version, type) values (1, "1.6.11", "component");

CREATE TABLE IF NOT EXISTS `#__it_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the issue status.',
  `status_name` varchar(60) NOT NULL COMMENT 'The unique name of the status.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the status.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Issue statuses.  i.e. Open, closed, on-hold etc.';

CREATE TABLE IF NOT EXISTS `#__it_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the person role.',
  `role_name` varchar(60) NOT NULL COMMENT 'The unique name of the role.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the role.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='People Roles.  i.e. CEO, Member, Lead, Guest, Customer etc.';

CREATE TABLE IF NOT EXISTS `#__it_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the priority.',
  `priority_name` varchar(60) NOT NULL COMMENT 'The unique name of the priority.',
  `response_time` decimal(11,2) NOT NULL COMMENT 'The target response time expressed in hours.',
  `ranking` int(11) NOT NULL COMMENT 'The ranking of the priority expressed as a value between 0 and 100.  Higher numbers indicate higher priority.',
  `resolution_time` decimal(11,2) NOT NULL COMMENT 'The target resolution time expressed in hours.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the priority.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Issue priorities within the company.';

CREATE TABLE IF NOT EXISTS `#__it_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the issue type.',
  `type_name` varchar(60) NOT NULL COMMENT 'The unique name of the type.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the type.',
  `state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` INT(11) NOT NULL COMMENT 'Default ordering column',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Issue types.  i.e. Defect , Enhancement etc.';

CREATE TABLE IF NOT EXISTS `#__it_custom_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key of the table.',
  `name` varchar(255) NOT NULL COMMENT 'Name for the custom field.',
  `value` text NOT NULL COMMENT 'Default value for the field.',
  `type` varchar(255) NOT NULL COMMENT 'Type of custom field.',
  `tooltip` MEDIUMTEXT NOT NULL COMMENT 'Text for the tooltip of the field, if any.',
  `validation` MEDIUMTEXT DEFAULT NULL COMMENT 'Validation rules for the field.',
  `access` INT(11)  NOT NULL DEFAULT '0' COMMENT 'Access rules for the field.',
  `group` int(11) NOT NULL COMMENT 'Name of the group for which this field is part.',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` int(11) NOT NULL COMMENT 'Ordering of the field.',
  `checked_out` int(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',  
  PRIMARY KEY (`id`),
  KEY `group` (`group`),
  KEY `state` (`state`),
  KEY `ordering` (`ordering`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='Custom field definitions';

CREATE TABLE IF NOT EXISTS `#__it_custom_field_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key of the table.',
  `name` varchar(255) NOT NULL COMMENT 'Name of the custom field group.',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `checked_out` int(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Groups for Custom fields';

CREATE TABLE IF NOT EXISTS `#__it_projects` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the project.',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Link to parent project id.',
  `title` varchar(255) NOT NULL COMMENT 'The unique name of the project.',
  `alias` varchar(255) DEFAULT NULL COMMENT 'Project Alias.  Used to mask primary key of issue from random selection.',
  `description` varchar(4000) DEFAULT NULL COMMENT 'A full description of the project.',  
  `lft` INT(11) NOT NULL DEFAULT '0' COMMENT 'Nested table left',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested table right',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Nested table level.',
  `access` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Required for nested table.',
  `path` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Required for nested table.',  
  `state` TINYINT(4) DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `assignee` int(11) NOT NULL DEFAULT '0' COMMENT 'A specified default assignee for the project.',
  `itypes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Issue Types for this project.',
  `customfieldsgroup` int(11) NOT NULL COMMENT 'Custom field group associated with this project.',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which categories are presented.',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `start_date` datetime NOT NULL COMMENT 'The start date of the project.',
  `target_end_date` datetime DEFAULT NULL COMMENT 'The targeted end date of the project.',
  `actual_end_date` datetime DEFAULT NULL COMMENT 'The actual end date of the project.',
  `metadata` text NOT NULL,
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`),
  KEY `idx_left_right` (`lft`,`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 COMMENT='All projects currently underway.';

CREATE TABLE IF NOT EXISTS `#__it_people` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the person.',
  `user_id` int COMMENT 'The user identifier as recorded in the Joomla user table.',
  `person_name` varchar(255) NOT NULL COMMENT 'The unique name of the person.',
  `alias` varchar(10) DEFAULT NULL COMMENT 'Person Alias.  Used to mask primary key of person from random selection.',
  `person_email` varchar(100) NOT NULL COMMENT 'The email address of the person.',
  `person_role` int(11) NOT NULL COMMENT 'The role the person plays within the company.',
  `username` varchar(150) NOT NULL COMMENT 'The username of this person. Used to link login to person details.',
  `phone_number` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'User phone number.',
  `assigned_project` int UNSIGNED DEFAULT NULL COMMENT 'The project that the person is currently assigned to.',
  `issues_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates that the person is an Issues administrator.',
  `staff` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates that the person is a member of staff.',  
  `email_notifications` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Person has requested email notifications when their raised issues are changed.',
  `sms_notify` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Person receives SMS notifications when their issues are changed.',
  `registered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether user is registered.',
  `published` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether visible in the front end.',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which people are presented.',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `#__it_people_userid_uk` (`user_id`),
  UNIQUE KEY `#__it_people_username_uk` (`username`),
  KEY `#__it_people_phone_number_uk` (phone_number),
  KEY `#__it_people_project_fk` (`assigned_project`),
  KEY `#__it_people_role_fk` (`person_role`),
  KEY `#__it_people_name_uk` (`person_name`(150),`person_email`(90)),
  CONSTRAINT `#__it_people_project_fk` FOREIGN KEY (`assigned_project`) REFERENCES `#__it_projects` (`id`),
  CONSTRAINT `#__it_people_role_fk` FOREIGN KEY (`person_role`) REFERENCES `#__it_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 COMMENT='All people within the company.';

CREATE TABLE IF NOT EXISTS `#__it_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the issue.',
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ACL permissions field',
  `alias` varchar(10) DEFAULT NULL COMMENT 'Issue Alias.  Used to mask primary key of issue from random selection.',
  `issue_summary` varchar(255) NOT NULL COMMENT 'A brief summary of the issue.',
  `issue_description` varchar(4000) DEFAULT NULL COMMENT 'A full description of the issue.',
  `identified_by_person_id` int NOT NULL COMMENT 'The person who identified the issue.',
  `identified_date` datetime NOT NULL COMMENT 'The date the issue was identified.',
  `related_project_id` int UNSIGNED NOT NULL COMMENT 'The project that the issue is related to.',
  `assigned_to_person_id` int NULL COMMENT 'The person that the issue is assigned to.',
  `issue_type` int(11) DEFAULT '1' NOT NULL COMMENT 'The issue type.  i.e. defect etc.',
  `status` int(11) NOT NULL COMMENT 'The current status of the issue.',
  `public` tinyint(3) NOT NULL DEFAULT '1',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `checked_out` INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which issues are presented.',
  `priority` int(11) NOT NULL COMMENT 'The priority of the issue. How important it is to get resolved.',
  `target_resolution_date` datetime DEFAULT NULL COMMENT 'The date on which the issue is planned to be resolved.',
  `progress` mediumtext DEFAULT NULL COMMENT 'Any progress notes on the issue resolution.',
  `actual_resolution_date` datetime DEFAULT NULL COMMENT 'The date the issue was actually resolved.',
  `resolution_summary` varchar(4000) DEFAULT NULL COMMENT 'The description of the resolution of the issue.',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `custom_fields` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Custom field.',
  `metadata` text NOT NULL,
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`),
  KEY `#__it_issues_identified_by_fk` (`identified_by_person_id`),
  KEY `#__it_issues_assigned_to_fk` (`assigned_to_person_id`),
  KEY `#__it_issues_project_fk` (`related_project_id`),
  KEY `#__it_issues_status_fk` (`status`),
  KEY `#__it_issues_types_fk` (`issue_type`),
  KEY `#__it_issues_priority_fk` (`priority`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_alias` (`alias`),
  CONSTRAINT `#__it_issues_priority_fk` FOREIGN KEY (`priority`) REFERENCES `#__it_priority` (`id`),
  CONSTRAINT `#__it_issues_assigned_to_fk` FOREIGN KEY (`assigned_to_person_id`) REFERENCES `#__it_people` (`user_id`),
  CONSTRAINT `#__it_issues_identified_by_fk` FOREIGN KEY (`identified_by_person_id`) REFERENCES `#__it_people` (`id`),
  CONSTRAINT `#__it_issues_project_fk` FOREIGN KEY (`related_project_id`) REFERENCES `#__it_projects` (`id`),
  CONSTRAINT `#__it_issues_status_fk` FOREIGN KEY (`status`) REFERENCES `#__it_status` (`id`),
  CONSTRAINT `#__it_issues_type_fk` FOREIGN KEY (`issue_type`) REFERENCES `#__it_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 COMMENT='All issues related to the company projects being undertaken.';

CREATE TABLE IF NOT EXISTS `#__it_emails` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` VARCHAR(32) NOT NULL COMMENT 'Short name of the email message type.',
 `description` MEDIUMTEXT NOT NULL COMMENT 'Description of the email message type', 
 `subject` VARCHAR(64) NOT NULL COMMENT 'Email subject title for email message type',
 `body` longtext NOT NULL COMMENT 'Template for the email message itself.',
 `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which issues are presented.',
 `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
 `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT '0',
 `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
 `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
 `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
 `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
 `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 COMMENT='Email message templates for Issue Tracker notifications.';

CREATE TABLE IF NOT EXISTS `#__it_issues_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `priority` int(11) DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category_date_priority` (`category`,`date`,`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Log table for various messages for Issue Tracker.';
 
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
  CONSTRAINT `#__it_attachment_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `#__it_issues` (`alias`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Attachments for raised issues.';
 
CREATE TABLE IF NOT EXISTS `#__it_chistory` (
`id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key of the table.',
`table_name` VARCHAR(255) NOT NULL COMMENT 'The name of the table for which the change record applies',
`component` VARCHAR(255) COMMENT 'The component to which the change record applies.',
`state` TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.', 
`row_key` INT(11)  NOT NULL COMMENT 'The value of the primary key for the record.',
`row_key_link` TEXT(255)  NOT NULL COMMENT 'The value of the alias for the record if it exists, the value of the Primary key otherwise.',
`column_name` TEXT(255)  NOT NULL COMMENT 'The name of the table column being recorded.',
`column_type` TEXT(12)  COMMENT 'The type of column for the record.',
`action` TEXT(12)  NOT NULL COMMENT 'The action of the change record. i.e.INSERT, UPDATE or DELETE.',
`old_value` MEDIUMTEXT COMMENT 'For an DELETE or UPDATE action the former field value.',
`new_value` MEDIUMTEXT COMMENT 'For an UPDATE or INSERT action the new field value.',
`change_by` INT(11) NOT NULL COMMENT 'The Joomla id of the person who made the change where it can be determined otherwise the super user id.',
`change_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'The date when the change was made.',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci COMMENT='Change History (audit) record.';

CREATE TABLE IF NOT EXISTS `#__it_triggers` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary key of the table.',
`table_name` VARCHAR(255) NOT NULL COMMENT 'The name of the table to which the trigger applies.',
`trigger_schema` VARCHAR(255) NOT NULL COMMENT 'The database schema to which the trigger applies.',
`trigger_name` VARCHAR(255) NOT NULL COMMENT 'The name of the trigger.',
`trigger_type` VARCHAR(255) DEFAULT 'AFTER' NOT NULL COMMENT 'The type of trigger. i.e. BEFORE or AFTER.',
`trigger_event` VARCHAR(255) NOT NULL COMMENT 'The type of trigger. i.e. INSERT, UPDATE or DELETE.',
`trigger_text` MEDIUMTEXT NOT NULL COMMENT 'The actual trigger text from specified criteria.',
`columns` TEXT(255)  NOT NULL COMMENT 'The columns to which the trigger applies.',
`action_orientation` VARCHAR(10) NOT NULL DEFAULT 'ROW' COMMENT 'How the trigger is applied.',
`applied` TINYINT(4) DEFAULT '0' COMMENT 'State of the specific trigger.  i.e.  Active or Inactive.',
`created_by` INT(11)  COMMENT 'The Joomla id of the use who created the trigger text.',
`created_by_alias` VARCHAR(255) COMMENT 'The name of the person who created the trigger text.',
`created_on` DATE NOT NULL COMMENT 'Date the trigger text was created',
PRIMARY KEY (`id`),
CONSTRAINT uc_Jaudit_trigname UNIQUE (`trigger_name`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci COMMENT='Trigger text applied to tables';

CREATE TABLE IF NOT EXISTS `#__it_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The system generated unique identifier for the progress table.',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ACL permissions field',
  `issue_id` int(11) NOT NULL COMMENT 'The system generated unique identifier for the specific issue. FK to issue table.',
  `alias` varchar(10) DEFAULT NULL COMMENT 'Issue Alias. Used to mask primary key of issue from random selection.',
  `public` tinyint(3) NOT NULL DEFAULT '1',
  `state` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `checked_out` int(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which issues are presented.',
  `progress` mediumtext COMMENT 'Any progress notes on the issue resolution.',
  `lineno` int(11) NOT NULL COMMENT 'Value defining the default order of the various progress updates for a specific issue.',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_issueid_lineno` (`issue_id`,`lineno`),
  KEY `idx_progress_issueid_fk` (`issue_id`),
  KEY `idx_progress_checkout` (`checked_out`),
  KEY `idx_progress_state` (`state`),
  KEY `idx_progress_createdby` (`created_by`),
  KEY `idx_progress_alias` (`alias`),
  CONSTRAINT `idx_progress_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `#__it_issues` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Issue progress records.';
 
INSERT IGNORE INTO `#__it_status`(`id`,`status_name`,`description`) 
VALUES (1,'Closed','Used when an issue is completed and no further change related to the issue is expected.')
, (2,'In-Progress','The issue is being actively worked by an individual.')
, (3,'On-Hold','The issue is currently awaiting some unspecified activity and is not currently being worked.')
, (4,'Open','The issue is open but no work has commenced to resolve it.')
, (5,'Undefined','The current status of this issue is unknown.');

INSERT IGNORE INTO `#__it_roles`(`id`,`role_name`,`description`) 
VALUES (1,'CEO','Chief Executive Office.  Senior member of company.  Does not usually have any specific projects assigned.')
, (2,'Customer','Customer of the product or company.  Usually just reports problems, raises queries etc.')
, (3,'Lead','This role indicate an individual with direct responsibility for any assigned projects.')
, (4,'Manager','The person responsible for many projects and usually many staff, each of which is associated with one or more projects.')
, (5,'Member','A team member working or assigned to one or more projects but without overall responsibility for any one.')
, (6,'User','A user of the product.  Might be considered a customer but usually no financial transaction has occurred.');

INSERT IGNORE INTO `#__it_priority`(`id`,`priority_name`,`response_time`,`ranking`,`resolution_time`,`description`) 
VALUES (1,'High','0.5','70','4','Office, department, or user has completely lost ability to perform all their functions but does not lend itself to financial liability or loss.')
, (2,'Low','4','10','24','1 or 2 Users have a minor inconvenience with the functionality of a single product.')
, (3,'Medium','2','40','8','Office, department, or user has a marginal loss of functionality but has an alternate method of performing task without financial liability or loss.')
, (4,'Critical','0.25','90','2','Office, department, or user has completely lost ability to perform all their functions, which in turn may cause financial liability or loss.');

INSERT IGNORE INTO `#__it_types`(`id`,`type_name`,`description`,`created_on`,`created_by`,`modified_on`,`modified_by`) 
values (1,'Defect','The product has a defect that prevents it working correctly.',null,'',null,null)
, (2,'Enhancement','The product could be improved if this enhancement were applied.',null,'',null,null)
, (3,'Documentation','The documentation needs correcting.',null,'',null,null)
, (4,'Suggestion','The product could be improved if this suggestion were implemented.',null,'',null,null)
, (5,'Other','The issue is not described by any of the other types.',null,'',null,null);

INSERT IGNORE INTO `#__it_emails`(`id`,`type`,`description`,`subject`,`body`,`ordering`,`state`) 
values (1,'ass_close','Assignee - Issue Closure','Assigned Issue [issue_id] Closed','<p>The following issue that is assigned to you has been closed.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">RESOLUTION</span></p>
<p>[resolution]</p>',0,1)
, (2,'ass_new','Assignee - New Issue assignment.','Assigned Issue [issue_id] Create','<p>The following issue has been assigned to you.</p>
<p>You can update the issue at [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">USER INFORMATION</span></p>
<p>Username: [user_name]</p>
<p>Email: [user_email]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (3,'ass_update','Assignee - Issue Updated','Assigned Issue [issue_id] Update','<p>The following assigned issue has been updated.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>&nbsp; ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">PROGRESS</span></p>
<p>[progress]</p>',0,1)
, (4,'user_close','User - Issue Closure Message','Issue [issue_id] Closed','<p>Your raised issue has been closed.</p>
<p>You can view the issue resolution below or at: [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">RESOLUTION</span></p>
<p>[resolution]</p>',0,1)
, (5,'user_new','User - Issue Creation Message','Issue [issue_id] Created','<p>Thank you for submitting your issue.</p>
<p>You can view or update [requires login] the issue at: [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (6,'user_update','User - Issue Update Message','Your Issue [issue_id] Updated','<p>Your raised issue has been updated.</p>
<p>You can view the issue at: [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">PROGRESS </span></p>
<p>[progress]</p>',0,1)
, (7,'admin_new','Admin - New Issue Message','Issue [issue_id] Created','<p>The following issue has been created and the assignment may need checking.</p>
<p>You can update the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS </span></p>
<p>ID: [issue_id]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">USER INFORMATION </span></p>
<p>Fullname: [user_fullname]</p>
<p>Username: [user_name]</p>
<p>Email: [user_email]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (8,'admin_update','Admin - Issue Updated Message','Issue [issue_id] Updated','<p>The following issue has been updated. You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>Status: [status]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">USER INFORMATION</span></p>
<p>Fullname: [user_fullname]</p>
<p>Username: [user_name]</p>
<p>Email: [user_email]</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (9,'admin_close','Admin - Issue closure','Issue [issue_id] closed','<p>The following issue has been closed.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p><span style="text-decoration: underline;">&nbsp;</span></p>
<p><span style="text-decoration: underline;">RESOLUTION</span></p>
<p>[resolution]</p>',0,1)
, (13,'sms_close','SMS - Issue Closure','Issue [issue_id] Closed','<p>Project: [project]</p>
<p>Title: [title]</p>',0,1)
, (14,'sms_new','SMS - New Issue.','Issue [issue_id] Created','<p>Title: [title]</p>
<p>Project: [project]</p>',0,1)
, (15,'sms_update','SMS - Issue Updated','Issue [issue_id] Updated','<p>Project: [project]</p>
<p>Title: [title]</p>',0,1)
, (16, 'auto_close','User - Issue Auto Closed','Issue [issue_id] Auto Closed',
'<h3>Issue Tracker Issue Notification</h3>
<p>Hello [user_name],</p>
<p>A new message was posted for your issue #<strong>[issue_id]</strong> &mdash; "<em>[title]</em>":</p>
<hr />
<div style="background-color: #fff; margin: 5px 10px; padding: 3px 6px; border-radius: 5px; border: thin solid gray;"><strong>This issue has been automatically closed.</strong> All issues which have been inactive for a long time are automatically closed. If you believe that this issue was closed in error, please contact us.</div>
<hr />
<p>You can view the issue at [url]</p>',0,1)
, (17, 'summary_report', 'Issue Tracker Summary Report. Intended for Issue Administrators.', 'Issue Summary Report',
'<h3>Issue Tracker Summary Report</h3>
<hr />
[REPORT]
<hr />', 0, 1)
, (18, 'overdue_report', 'Issue Tracker Overdue Report. Intended for Issue Assignees.', 'Issue Overdue Report',
'<h3>Issue Tracker Overdue Report</h3>
<hr />
[REPORT]
<hr />', 0, 1);

INSERT IGNORE INTO `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, state, created_by, created_on, access)
values (1, 'Root', 'Root', 0, 0, 3, 0, now(), 1, 'admin', now(), 1);

INSERT IGNORE INTO `#__it_projects` (id, title, description, parent_id, lft, rgt, level, start_date, state, created_by, created_on, access)
values (10, 'Unspecified Project', 'Unspecified Project','1', 1, 2, 1, now(), 1, 'admin', now(), 1);

INSERT IGNORE INTO `#__it_people` (id, person_name, username, person_email, registered, person_role, created_by, created_on, assigned_project)
values (1, 'Anonymous', 'anon', 'anonymous@bademail.com', '0', '6', 'admin', now(), '10');

INSERT IGNORE INTO `#__it_custom_field_group` (id,name,state,created_on,created_by) 
values (1,'Product Request',1,NOW(),'admin');

INSERT IGNORE INTO `#__it_custom_field` (id,name,value,type,validation,access,`group`,state,ordering,tooltip,created_on,created_by) 
values (1,'Product Details','[{"name":null,"value":"Product Details","displayInFrontEnd":"1","target":null,"alias":"","required":"1","showNull":"0"}]','header','',1,1,1,1,"",NOW(),'admin'),
(2,'Joomla Version','[{"name":null,"value":"","target":null,"alias":"","required":"1","showNull":"0"}]','textfield','',1,1,1,2,"Details of the Joomla Version being used.",NOW(),'admin'),
(3,'PHP Version','[{"name":null,"value":"","target":null,"alias":"","required":"0","showNull":"0"}]','textfield','',1,1,1,3,"Details of the PHP version.",NOW(),'admin'),
(4,'Product Version','[{"name":null,"value":"","target":null,"alias":"","required":"1","showNull":"0"}]','textfield','',1,1,1,4,"Version of the product.",NOW(),'admin'),
(5,'Database Type','[{"name":"MySQL","value":1,"target":null,"alias":"","required":"0","showNull":"0"},{"name":"MS-SQLSVR","value":2,"target":null,"alias":"","required":"0","showNull":"0"}]','radio','',1,1,1,5,"Details of the Database upon which Joomla is running.",NOW(),'admin'),
(6,'Database Version','[{"name":null,"value":"","target":null,"alias":"","required":"0","showNull":"0"}]','textfield','',1,1,1,6,"Version number of the database.",NOW(),'admin');
