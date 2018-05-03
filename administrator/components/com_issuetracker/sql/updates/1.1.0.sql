CREATE TABLE IF NOT EXISTS `#__it_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key of the it types table.',
  `type_name` varchar(60) NOT NULL COMMENT 'The unique name of the type.',
  `description` varchar(1024) DEFAULT NULL COMMENT 'The full text description of the type.',
  `state` tinyint(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
  `ordering` int(11) NOT NULL  COMMENT 'Default ordering column',
  `checked_out` int(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.',
  `created_on` datetime NOT NULL COMMENT 'Audit Column: Date the record was created.',
  `created_by` varchar(255) NOT NULL COMMENT 'Audit Column: The user who created the record.',
  `modified_on` datetime DEFAULT NULL COMMENT 'Audit Column: Date the record was last modified.',
  `modified_by` varchar(255) DEFAULT NULL COMMENT 'Audit Column: The user who last modified the record.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Issue types.  i.e. Defect , Enhancement etc.';

INSERT IGNORE INTO `#__it_types`(`id`,`type_name`,`description`,`created_on`,`created_by`) 
values (1,'Defect','The product has a defect or bug that prevents it working correctly.',sysdate(),USER())
, (2,'Enhancement','The product would be improved by this enhancement being applied.',sysdate(),USER())
, (3,'Documentation','The documentation needs correcting or changing.',sysdate(),USER())
, (4,'Suggestion','It would be a good idea if the product did as described.',sysdate(),USER())
, (5,'New','A new feature of the product.',sysdate(),USER())
, (6,'Task','The described task or sub-task needs to be completed or changed.',sysdate(),USER())
, (7,'Other','The issue is not described by any of the other defined types.',sysdate(),USER()); 

ALTER TABLE `#__it_issues`
 ADD issue_type int(11) DEFAULT '1' NOT NULL COMMENT 'Type of issue.  i.e. defect etc.' AFTER assigned_to_person_id,
 ADD CONSTRAINT `#__it_issues_type_fk` FOREIGN KEY (issue_type) REFERENCES `#__it_types` (id) ON UPDATE RESTRICT ON DELETE RESTRICT;
 
ALTER TABLE `#__it_people`
 DROP FOREIGN KEY `#__it_people_project_fk`,
 DROP FOREIGN KEY `#__it_people_role_fk`,
 ADD `published` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether visible in the front end.' AFTER email_notifications,
 ADD `ordering` int(11) NOT NULL DEFAULT '0' COMMENT 'Order in which people are presented.';
 
ALTER TABLE `#__it_people`
  ADD CONSTRAINT `#__it_people_project_fk` FOREIGN KEY (assigned_project) REFERENCES `#__it_projects` (project_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
  ADD CONSTRAINT `#__it_people_role_fk` FOREIGN KEY (person_role) REFERENCES `#__it_roles` (role_id) ON UPDATE RESTRICT ON DELETE RESTRICT;
  
ALTER TABLE `#__it_roles`
 CHANGE role_name role_name VARCHAR(60) NOT NULL COMMENT 'The unique name of the role.';
 
 ALTER TABLE `#__it_priority`
 CHANGE priority_name priority_name VARCHAR(60) NOT NULL COMMENT 'The unique name of the priority.',  
 CHANGE description description VARCHAR(1024) COMMENT 'The full text description of the priority.';

 ALTER TABLE `#__it_status`
  CHANGE status_name status_name VARCHAR(60) NOT NULL COMMENT 'The unique name of the status.',
  CHANGE description description VARCHAR(1024) COMMENT 'The full text description of the status.';

 ALTER TABLE `#__it_issues`
  DROP FOREIGN KEY `#__it_people_status_fk`;
 
 ALTER TABLE `#__it_status`
 CHANGE status_id id INT(11) AUTO_INCREMENT NOT NULL COMMENT 'The system generated unique identifier for the issue status.',
 ADD state TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.' AFTER description,
 ADD ordering INT(11) NOT NULL COMMENT 'Default ordering column',
 ADD checked_out INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
 ADD checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.';

ALTER TABLE `#__it_issues`
 ADD CONSTRAINT `#__it_issues_status_fk` FOREIGN KEY (status) REFERENCES `#__it_status` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT;

 ALTER TABLE `#__it_people`
  DROP FOREIGN KEY `#__it_people_role_fk`;
 
 ALTER TABLE `#__it_roles`
 CHANGE role_id id INT(11) AUTO_INCREMENT NOT NULL COMMENT 'The system generated unique identifier for the person role.',
 CHANGE description description VARCHAR(1024) COMMENT 'The full text description of the role.',
 ADD state TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.' AFTER description,
 ADD ordering INT(11) NOT NULL COMMENT 'Default ordering column',
 ADD checked_out INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
 ADD checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.';

ALTER TABLE `#__it_people`
 ADD CONSTRAINT `#__it_people_role_fk` FOREIGN KEY (`person_role`) REFERENCES `#__it_roles` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT;

 ALTER TABLE `#__it_issues`
  DROP FOREIGN KEY `#__it_people_priority_fk`;
 
 ALTER TABLE `#__it_priority`
 CHANGE priority_id id INT(11) AUTO_INCREMENT NOT NULL COMMENT 'The system generated unique identifier for the person role.',
 ADD state TINYINT(4) DEFAULT '1' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.' AFTER description,
 ADD ordering INT(11) NOT NULL COMMENT 'Default ordering column',
 ADD checked_out INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.',
 ADD checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.';

ALTER TABLE `#__it_issues`
 ADD CONSTRAINT `#__it_issues_priority_fk` FOREIGN KEY (`priority`) REFERENCES `#__it_priority` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT;
 
ALTER TABLE `#__it_issues`
 CHANGE related_project_id related_project_id int(11) NOT NULL COMMENT 'The project that the issue is related to.',
 DROP FOREIGN KEY `#__it_issues_project_fk`;

ALTER TABLE `#__it_people`
  DROP FOREIGN KEY `#__it_people_project_fk`;
 
ALTER TABLE `#__it_projects`
 CHANGE project_id id INT(11) AUTO_INCREMENT NOT NULL COMMENT 'The system generated unique identifier for the project.',
 CHANGE published state TINYINT(4) DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
 ADD checked_out INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.' AFTER ordering,
 ADD checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.';

ALTER TABLE `#__it_issues`
 ADD CONSTRAINT `#__it_issues_project_fk` FOREIGN KEY (`related_project_id`) REFERENCES `#__it_projects` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE `#__it_people`
CHANGE assigned_project assigned_project int(11) DEFAULT NULL COMMENT 'The project that the person is currently assigned to.';

ALTER TABLE `#__it_people`
 ADD CONSTRAINT `#__it_people_project_fk` FOREIGN KEY (`assigned_project`) REFERENCES `#__it_projects` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE `#__it_issues`
 CHANGE issue_id id INT(11) AUTO_INCREMENT NOT NULL COMMENT 'The system generated unique identifier for the issue.',
 CHANGE published state TINYINT(4) DEFAULT '0' COMMENT 'State of the specific record.  i.e.  Published, archived, trashed etc.',
 ADD checked_out INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.' AFTER ordering,
 ADD checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.';

ALTER TABLE `#__it_issues`
  DROP FOREIGN KEY `#__it_issues_identified_by_fk`,
  DROP FOREIGN KEY `#__it_issues_assigned_to_fk`;

ALTER TABLE `#__it_people`
 CHANGE person_id id INT(11) AUTO_INCREMENT NOT NULL COMMENT 'The system generated unique identifier for the person.',
 ADD checked_out INT(11) NOT NULL DEFAULT '0' COMMENT 'Checked out indicator.  User id of user editing the record.' AFTER ordering,
 ADD checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Time and date when the record was checked out.';

ALTER TABLE `#__it_issues`
  ADD CONSTRAINT `#__it_issues_assigned_to_fk` FOREIGN KEY (`assigned_to_person_id`) REFERENCES `#__it_people` (`id`),
  ADD CONSTRAINT `#__it_issues_identified_by_fk` FOREIGN KEY (`identified_by_person_id`) REFERENCES `#__it_people` (`id`);

