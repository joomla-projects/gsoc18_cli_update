ALTER TABLE `#__it_attachment`
 DROP FOREIGN KEY `#__it_attachment_issue_id_fk`;
ALTER TABLE `#__it_attachment`
 ADD CONSTRAINT `#__it_attachment_issue_id_fk` FOREIGN KEY (issue_id) REFERENCES `#__it_issues` (alias) ON UPDATE RESTRICT ON DELETE CASCADE;

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

ALTER TABLE `#__it_projects`
ADD `customfieldsgroup` int(11) NOT NULL COMMENT 'Custom field group associated with this project.' AFTER assignee;

ALTER TABLE `#__it_issues`
ADD `custom_fields` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Custom fields.' AFTER access;

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
  CONSTRAINT `idx_progress_issue_id_fk` FOREIGN KEY (`issue_id`) REFERENCES `#__it_issues` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Issue progress records.';

INSERT IGNORE INTO `#__it_custom_field_group` (id,name,state,created_on,created_by) 
values (1,'Product Request',1,NOW(),'admin');

INSERT IGNORE INTO `#__it_custom_field` (id,name,value,type,validation,access,`group`,state,ordering,tooltip,created_on,created_by) 
values (1,'Product Details','[{"name":null,"value":"Product Details","displayInFrontEnd":"1","target":null,"alias":"","required":"1","showNull":"0"}]','header','',1,1,1,1,"",NOW(),'admin'),
(2,'Joomla Version','[{"name":null,"value":"","target":null,"alias":"","required":"1","showNull":"0"}]','textfield','',1,1,1,2,"Details of the Joomla Version being used.",NOW(),'admin'),
(3,'PHP Version','[{"name":null,"value":"","target":null,"alias":"","required":"0","showNull":"0"}]','textfield','',1,1,1,3,"Details of the PHP version.",NOW(),'admin'),
(4,'Product Version','[{"name":null,"value":"","target":null,"alias":"","required":"1","showNull":"0"}]','textfield','',1,1,1,4,"Version of the product.",NOW(),'admin'),
(5,'Database Type','[{"name":"MySQL","value":1,"target":null,"alias":"","required":"0","showNull":"0"},{"name":"MS-SQLSVR","value":2,"target":null,"alias":"","required":"0","showNull":"0"}]','radio','',1,1,1,5,"Details of the Database upon which Joomla is running.",NOW(),'admin'),
(6,'Database Version','[{"name":null,"value":"","target":null,"alias":"","required":"0","showNull":"0"}]','textfield','',1,1,1,6,"Version number of the database.",NOW(),'admin');