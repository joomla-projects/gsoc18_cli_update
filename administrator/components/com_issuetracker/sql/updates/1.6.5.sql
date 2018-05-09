ALTER TABLE `#__it_projects`
ADD `itypes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Issue Types for this project.' AFTER assignee;

ALTER TABLE `#__it_people`
  ADD `phone_number` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'User phone number.' AFTER username,
  ADD `sms_notify` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Person receives SMS notifications when their issues are changed.' AFTER email_notifications,
  ADD INDEX `#__it_people_phone_number_uk` (phone_number);
  
INSERT IGNORE INTO `#__it_emails`(`type`,`description`,`subject`,`body`,`ordering`,`state`) 
values ('sms_close','SMS - Issue Closure','Issue [issue_id] Closed','<p>Project: [project]</p>
<p>Title: [title]</p>',0,1)
, ('sms_new','SMS - New Issue.','Issue [issue_id] Created','<p>Title: [title]</p>
<p>Project: [project]</p>',0,1)
, ('sms_update','SMS - Issue Updated','Issue [issue_id] Updated','<p>Project: [project]</p>
<p>Title: [title]</p>',0,1);  