ALTER TABLE `#__it_people`
 ADD `user_id` int COMMENT 'The user identifier as recorded in the Joomla user table.' AFTER id,
 ADD `registered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether user is registered.' AFTER email_notifications,
 ADD `staff` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates that the person is a member of staff.' AFTER issues_admin,
 ADD UNIQUE INDEX `#__it_people_userid_uk` (user_id);
 
UPDATE `#__it_people` SET user_id = id, registered = 1 WHERE id != 1; 

UPDATE `#__it_people` SET staff = 1 WHERE user_id IN (SELECT distinct assigned_to_person_id FROM `#__it_issues`);

ALTER TABLE `#__it_issues` ADD `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'ACL permissions field' AFTER `id`;

ALTER TABLE `#__it_issues`
 DROP FOREIGN KEY `#__it_issues_assigned_to_fk`;

ALTER TABLE `#__it_issues`
 ADD CONSTRAINT `#__it_issues_assigned_to_fk` FOREIGN KEY (assigned_to_person_id) REFERENCES `#__it_people` (user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;

CREATE TABLE IF NOT EXISTS `#__it_emails` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` VARCHAR(32) NOT NULL COMMENT 'Short name of the email message type.',
 `description` MEDIUMTEXT NOT NULL COMMENT 'Description of the email message type', 
 `subject` VARCHAR(32) NOT NULL COMMENT 'Email subject title for email message type',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 COMMENT='Email message templates for Issue Tracker notifications.';
 
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
<p>[resolution]</p>',1,1);
