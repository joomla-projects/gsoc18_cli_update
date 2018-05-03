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