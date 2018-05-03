SET QUOTED_IDENTIFIER ON;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_attachment]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_attachment]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_issues_log]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_issues_log]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_progress]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_custom_field]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_custom_field]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_custom_field_group]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_custom_field_group]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_meta]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_meta]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_issues]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_people]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_people]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_projects]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_projects]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_status]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_status]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_roles]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_roles]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_priority]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_priority]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_types]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_types]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_emails]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_emails]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_chistory]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_chistory]
END;
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_triggers]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__it_triggers]
END;

-- DROP PROCEDURE IF EXISTS `#__add_it_sample_data`;
-- DROP PROCEDURE IF EXISTS `#__create_sample_issues`;
-- DROP PROCEDURE IF EXISTS `#__create_sample_people`;
-- DROP PROCEDURE IF EXISTS `#__create_sample_projects`;
-- DROP PROCEDURE IF EXISTS `#__remove_it_sample_data`;