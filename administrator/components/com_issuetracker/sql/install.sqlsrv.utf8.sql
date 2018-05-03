SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_meta]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_meta]
(
 [id] Int IDENTITY NOT NULL,
 [version] Varchar(100) NULL,
 [type] Varchar(20) NULL,
	CONSTRAINT [PK_#__it_meta] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END; 

SET IDENTITY_INSERT #__it_meta ON;
IF NOT EXISTS (SELECT * FROM #__it_meta WHERE id = 1)
BEGIN
INSERT INTO #__it_meta (id, version, type)
SELECT 1, '1.6.6', 'component'
END;
SET IDENTITY_INSERT #__it_meta  OFF;

-- Table #__it_status

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_status]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_status]
(
 [id] Int IDENTITY NOT NULL,
 [status_name] Varchar(255) NOT NULL,
 [description] Varchar(1024) NULL,
 [state] Smallint DEFAULT 1 NULL,
 [ordering] Int NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_status] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Table #__it_roles

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_roles]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_roles]
(
 [id] Int IDENTITY NOT NULL,
 [role_name] Varchar(255) NOT NULL,
 [description] Varchar(1024) NULL,
 [state] Smallint DEFAULT 1 NULL,
 [ordering] Int NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_roles] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create triggers for table #__it_roles

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_attachment]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_attachment]
(
 [id] Int IDENTITY NOT NULL,
 [issue_id] Varchar(10) NOT NULL,
 [uid] Int NULL,
 [title] Varchar(255) NOT NULL,
 [description] Text NOT NULL,
 [filepath] Text NOT NULL,
 [filename] Varchar(255) NOT NULL,
 [hashname] Text NOT NULL,
 [filetype] Varchar(255) DEFAULT "application/octet-stream" NOT NULL,
 [size] Int NOT NULL,
 [ordering] Int DEFAULT 0 NOT NULL,
 [state] Smallint DEFAULT 0 NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_attachment] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Table #__it_priority

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_priority]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_priority]
(
 [id] Int IDENTITY NOT NULL,
 [priority_name] Varchar(255) NOT NULL,
 [response_time] Decimal(11,2) NOT NULL,
 [ranking] Int NOT NULL,
 [resolution_time] Decimal(11,2) NOT NULL,
 [description] Varchar(1024) NULL,
 [state] Smallint DEFAULT 1 NULL,
 [ordering] Int NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_priority] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Table #__it_types

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_types]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_types]
(
 [id] Int IDENTITY NOT NULL,
 [type_name] Varchar(255) NOT NULL,
 [description] Varchar(1024) NULL,
 [state] Smallint DEFAULT 1 NULL,
 [ordering] Int NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_types] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;
-- Create indexes for table #__it_attachment

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_attachment]') AND name = N'#__it_attachment_issue_id_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_attachment_issue_is_fk] ON [#__it_attachment]
(
	[issue_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

-- Table #__it_chistory

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_chistory]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_chistory]
(
 [id] Bigint IDENTITY NOT NULL,
 [table_name] Varchar(255) NOT NULL,
 [component] Varchar(255) NULL,
 [state] Smallint DEFAULT 1 NULL,
 [row_key] Int NOT NULL,
 [row_key_link] Text NOT NULL,
 [column_name] Text NOT NULL,
 [column_type] Char(255) NULL,
 [action] Char(255) NOT NULL,
 [old_value] Text NULL,
 [new_value] Text NULL,
 [change_by] Int NOT NULL,
 [change_date] Datetime2(9) DEFAULT CURRENT_TIMESTAMP NOT NULL,
	CONSTRAINT [PK_#__it_chistory] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Table #__it_custom_field

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_custom_field]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_custom_field]
(
 [id] Int IDENTITY NOT NULL,
 [name] Varchar(255) NOT NULL,
 [value] Text NOT NULL,
 [type] Varchar(255) NOT NULL,
 [tooltip] Text NOT NULL,
 [validation] Text NULL,
 [access] Int DEFAULT 0 NOT NULL,
 [group] Int NOT NULL,
 [state] Smallint DEFAULT 0 NOT NULL,
 [ordering] Int NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_custom_field] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create indexes for table #__it_custom_field
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_custom_field]') AND name = N'#__it_custom_field_group_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_custom_field_group_idx] ON [#__it_custom_field]
(
	[group] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_custom_field]') AND name = N'#__it_custom_field_state_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_custom_field_idx] ON [#__it_attachment]
(
	[state] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_custom_field]') AND name = N'#__it_custom_field_ordering_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_custom_field_ordering_idx] ON [#__it_custom_field]
(
	[ordering] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;


-- Table #__it_custom_field_group

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_custom_field_group]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_custom_field_group]
(
 [id] Int IDENTITY NOT NULL,
 [name] Varchar(255) NOT NULL,
 [state] Smallint DEFAULT 0 NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_custom_field_group] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

--  Table #__it_emails

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_emails]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_emails]
(
 [id] Int IDENTITY NOT NULL,
 [type] Varchar(32) NOT NULL,
 [description] Text NOT NULL,
 [subject] Varchar(32) NOT NULL,
 [body] Text NOT NULL,
 [ordering] Int DEFAULT 0 NOT NULL,
 [state] Smallint DEFAULT 0 NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_emails] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Table #__it_issues

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_issues]
(
 [id] Int IDENTITY NOT NULL,
 [asset_id] Int DEFAULT 0 NOT NULL,
 [alias] Varchar(10) NULL,
 [issue_summary] Varchar(255) NOT NULL,
 [issue_description] Varchar(4000) NULL,
 [identified_by_person_id] Int NOT NULL,
 [identified_date] Datetime2 NOT NULL,
 [related_project_id] Int NOT NULL,
 [assigned_to_person_id] Int NULL,
 [issue_type] Int DEFAULT 1 NOT NULL,
 [status] Int NOT NULL,
 [public] Smallint DEFAULT 1 NOT NULL,
 [state] Smallint DEFAULT 0 NULL,
 [ordering] Int DEFAULT 0 NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [priority] Int NOT NULL,
 [target_resolution_date] Datetime2 NULL,
 [progress] Text NULL,
 [actual_resolution_date] Datetime2 NULL,
 [resolution_summary] Varchar(4000) NULL,
 [access] Int DEFAULT 0 NOT NULL,
 [custom_fields] Text NULL,
 [metadata] Text NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_issues] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create indexes for table #__it_issues

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_alias_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_alias_idx] ON [#__it_issues]
(
	[alias] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_identified_by_person_id_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_identified_by_person_id_fk] ON [#__it_issues]
(
	[identified_by_person_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_assigned_to_person_id_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_assigned_to_person_id_fk] ON [#__it_issues]
(
	[assigned_to_person_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_related_project_id_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_related_project_id_fk] ON [#__it_issues]
(
	[related_project_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_status_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_status_fk] ON [#__it_issues]
(
	[status] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_priority_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_priority_fk] ON [#__it_issues]
(
	[priority] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_issue_type_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_issue_type_fk] ON [#__it_issues]
(
	[issue_type] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_checked_out_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_checked_out_idx] ON [#__it_issues]
(
	[checked_out] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_state_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_state_idx] ON [#__it_issues]
(
	[state] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues]') AND name = N'#__it_issues_created_by_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_created_by_idx] ON [#__it_issues]
(
	[created_by] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

-- Table #__it_issues_log

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_meta]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_issues_log]
(
 [id] Int IDENTITY NOT NULL,
 [priority] Int NULL,
 [message] Varchar(512) NULL,
 [date] Datetime2 NULL,
 [category] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_issues_log] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create indexes for table #__it_issues_log

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_issues_log]') AND name = N'#__it_issues_log_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_issues_log_idx] ON [#__it_issues_log]
(
	[category] ASC,
	[date] ASC,
	[priority] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

-- Table #__it_people

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_people]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_people]
(
 [id] Int IDENTITY NOT NULL,
 [user_id] Int NULL,
 [person_name] Varchar(255) NOT NULL,
 [alias] Varchar(10) NULL,
 [person_email] Varchar(100) NOT NULL,
 [person_role] Int NOT NULL,
 [username] Varchar(150) NOT NULL,
 [assigned_project] Int NULL,
 [issues_admin] Smallint DEFAULT 0 NOT NULL,
 [staff] Smallint DEFAULT 0 NOT NULL,
 [email_notifications] Smallint DEFAULT 0 NOT NULL,
 [sms_notify] Smallint DEFAULT 0  NOT NULL,
 [registered] Smallint DEFAULT 0 NOT NULL,
 [published] Smallint DEFAULT 0 NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
 [ordering] Int DEFAULT 0 NOT NULL,
	CONSTRAINT [PK_#__it_people] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create indexes for table #__it_people

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_people]') AND name = N'#__it_people_username_idx')
BEGIN
CREATE UNIQUE NONCLUSTERED INDEX [#__it_people_username_idx] ON [#__it_people]
(
	[username] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_people]') AND name = N'#__it_people_user_id_idx')
BEGIN
CREATE UNIQUE NONCLUSTERED INDEX [#__it_people_user_id_idx] ON [#__it_people]
(
	[user_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_people]') AND name = N'#__it_people_assigned_project_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_people_assigned_project_fk] ON [#__it_people]
(
	[assigned_project] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_people]') AND name = N'#__it_people_person_role_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_people_person_role_fk] ON [#__it_people]
(
	[person_role] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_people]') AND name = N'#__it_people_name_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_people_name_idx] ON [#__it_people]
(
	[person_name] ASC,
	[person_email] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

-- Table #__it_progress

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_progress]
(
 [id] Int IDENTITY NOT NULL,
 [asset_id] Int DEFAULT 0 NOT NULL,
 [issue_id] Int NOT NULL,
 [alias] Varchar(10) NULL,
 [public] Smallint DEFAULT 1 NOT NULL,
 [state] Smallint DEFAULT 0 NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [ordering] Int DEFAULT 0 NOT NULL,
 [progress] Text NULL,
 [lineno] Int NOT NULL,
 [access] Int DEFAULT 0 NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_progress] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create indexes for table #__it_progress

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND name = N'#__it_progress_issueid_lineno_idx')
BEGIN
CREATE UNIQUE NONCLUSTERED INDEX [#__it_progress_issueid_lineno_idx] ON [#__it_progress]
(
	[issue_id] ASC,
	[lineno] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND name = N'#__it_progress_issue_id_fk')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_progress_issue_id_fk] ON [#__it_progress]
(
	[issue_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND name = N'#__it_progress_checked_out_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_progress_checked_out_idx] ON [#__it_progress]
(
	[checked_out] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND name = N'#__it_progress_state_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_progress_state_idx] ON [#__it_progress]
(
	[state] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND name = N'#__it_progress_created_by_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_progress_created_by
	[created_by] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_progress]') AND name = N'#__it_progress_alias_idx')
BEGIN
CREATE NONCLUSTERED INDEX [#__it_progress_alias_idx] ON [#__it_progress]
(
	[alias] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

-- Table #__it_projects

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_projects]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_projects]
(
 [id] Int IDENTITY NOT NULL,
 [asset_id] Int DEFAULT 0 NOT NULL,
 [parent_id] Int DEFAULT 0 NOT NULL,
 [title] Varchar(255) NOT NULL,
 [access] Smallint DEFAULT 0 NOT NULL,
 [path] Varchar(255) NOT NULL,
 [alias] Varchar(255) NULL,
 [lft] Int DEFAULT 0 NOT NULL,
 [rgt] Int DEFAULT 0 NOT NULL,
 [level] Int DEFAULT 0 NOT NULL,
 [description] Varchar(4000) NULL,
 [state] Smallint DEFAULT 0 NULL,
 [assignee] Int DEFAULT 0 NOT NULL,
 [itypes] Text NULL,
 [customfieldsgroup] Int NOT NULL,
 [ordering] Int DEFAULT 0 NOT NULL,
 [checked_out] Int DEFAULT 0 NOT NULL,
 [checked_out_time] Datetime2 DEFAULT "0000-00-00 00:00:00" NOT NULL,
 [start_date] Datetime2 NOT NULL,
 [target_end_date] Datetime2 NULL,
 [actual_end_date] Datetime2 NULL,
 [metadata] Text NOT NULL,
 [created_on] Datetime2 NOT NULL,
 [created_by] Varchar(255) NOT NULL,
 [modified_on] Datetime2 NULL,
 [modified_by] Varchar(255) NULL,
	CONSTRAINT [PK_#__it_projects] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create indexes for table #__it_projects

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_projects]') AND name = N'#__it_projects_left_right_idx')
BEGIN
CREATE UNIQUE NONCLUSTERED INDEX [#__it_projects_left_right_idx] ON [#__it_projects]
(
	[lft] ASC,
   [rgt] ASC	
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

-- Table #__it_triggers

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__it_triggers]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__it_triggers]
(
 [id] Int IDENTITY NOT NULL,
 [table_name] Varchar(255) NOT NULL,
 [trigger_schema] Varchar(255) NOT NULL,
 [trigger_name] Varchar(255) NOT NULL,
 [trigger_type] Varchar(255) DEFAULT "AFTER" NOT NULL,
 [trigger_event] Varchar(255) NOT NULL,
 [trigger_text] Text NOT NULL,
 [columns] Text NOT NULL,
 [action_orientation] Varchar(10) DEFAULT "ROW" NOT NULL,
 [applied] Smallint DEFAULT 0 NULL,
 [created_by] Int NULL,
 [created_by_alias] Varchar(255) NULL,
 [created_on] Date NOT NULL,
	CONSTRAINT [PK_#__it_triggers] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

-- Create indexes for table #__it_triggers

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__it_triggers]') AND name = N'#__it_triggers_trigger_name_idx')
BEGIN
CREATE UNIQUE NONCLUSTERED INDEX [#__it_triggers_trigger_name_idx] ON [#__it_triggers]
(
	[trigger_name] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;


-- Create relationships section ------------------------------------------------- 

ALTER TABLE [#__it_attachment] ADD CONSTRAINT [#__it_attachment_issue_id_fk] FOREIGN KEY ([issue_id]) REFERENCES [#__it_issues] ([alias]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_issues] ADD CONSTRAINT [#__it_issues_assigned_to_person_id_fk] FOREIGN KEY ([assigned_to_person_id]) REFERENCES [#__it_people] ([user_id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_issues] ADD CONSTRAINT [#__it_issues_identified_by_person_id_fk] FOREIGN KEY ([identified_by_person_id]) REFERENCES [#__it_people] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_issues] ADD CONSTRAINT [#__it_issues_priority_fk] FOREIGN KEY ([priority]) REFERENCES [#__it_priority] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_issues] ADD CONSTRAINT [#__it_issues_related_project_id_fk] FOREIGN KEY ([related_project_id]) REFERENCES [#__it_projects] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_issues] ADD CONSTRAINT [#__it_issues_status_fk] FOREIGN KEY ([status]) REFERENCES [#__it_status] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_issues] ADD CONSTRAINT [#__it_issues_issue_type_fk] FOREIGN KEY ([issue_type]) REFERENCES [#__it_types] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_people] ADD CONSTRAINT [#__it_people_assigned_project_fk] FOREIGN KEY ([assigned_project]) REFERENCES [#__it_projects] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_people] ADD CONSTRAINT [#__it_people_person_role_fk] FOREIGN KEY ([person_role]) REFERENCES [#__it_roles] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

ALTER TABLE [#__it_progress] ADD CONSTRAINT [#__it_progress_issue_id_fk] FOREIGN KEY ([issue_id]) REFERENCES [#__it_issues] ([id]) ON UPDATE NO ACTION ON DELETE NO ACTION
go

-- Inserts to be added/completed/checked .

SET IDENTITY_INSERT #__it_status ON;
INSERT IGNORE INTO [#__it_status](id,status_name,description) 
VALUES (1,'Closed','Used when an issue is completed and no further change related to the issue is expected.')
, (2,'In-Progress','The issue is being actively worked by an individual.')
, (3,'On-Hold','The issue is currently awaiting some unspecified activity and is not currently being worked.')
, (4,'Open','The issue is open but no work has commenced to resolve it.')
, (5,'Undefined','The current status of this issue is unknown.');
SET IDENTITY_INSERT #__it_status OFF;

SET IDENTITY_INSERT #__it_roles ON;
INSERT IGNORE INTO [#__it_roles](id,role_name,description) 
VALUES (1,'CEO','Chief Executive Office.  Senior member of company.  Does not usually have any specific projects assigned.')
, (2,'Customer','Customer of the product or company.  Usually just reports problems, raises queries etc.')
, (3,'Lead','This role indicate an individual with direct responsibility for any assigned projects.')
, (4,'Manager','The person responsible for many projects and usually many staff, each of which is associated with one or more projects.')
, (5,'Member','A team member working or assigned to one or more projects but without overall responsibility for any one.')
, (6,'User','A user of the product.  Might be considered a customer but usually no financial transaction has occurred.');
SET IDENTITY_INSERT #__it_roles OFF;

SET IDENTITY_INSERT #__it_priority ON;
INSERT IGNORE INTO [#__it_priority](id,priority_name,response_time,ranking,resolution_time,description) 
VALUES (1,'High','0.5','70','4','Office, department, or user has completely lost ability to perform all their functions but does not lend itself to financial liability or loss.')
, (2,'Low','4','10','24','1 or 2 Users have a minor inconvenience with the functionality of a single product.')
, (3,'Medium','2','40','8','Office, department, or user has a marginal loss of functionality but has an alternate method of performing task without financial liability or loss.')
, (4,'Critical','0.25','90','2','Office, department, or user has completely lost ability to perform all their functions, which in turn may cause financial liability or loss.');
SET IDENTITY_INSERT #__it_priority OFF;

SET IDENTITY_INSERT #__it_types ON;
INSERT IGNORE INTO [#__it_types](id,type_name,description,created_on,created_by,modified_on,modified_by) 
values (1,'Defect','The product has a defect that prevents it working correctly.',null,'',null,null)
, (2,'Enhancement','The product could be improved if this enhancement were applied.',null,'',null,null)
, (3,'Documentation','The documentation needs correcting.',null,'',null,null)
, (4,'Suggestion','The product could be improved if this suggestion were implemented.',null,'',null,null)
, (5,'Other','The issue is not described by any of the other types.',null,'',null,null);
SET IDENTITY_INSERT #__it_types OFF;

SET IDENTITY_INSERT #__it_emails ON;
INSERT IGNORE INTO [#__it_emails](id,type,description,subject,body,ordering,state) 
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
<p>A new message was posted for your issue #<strong>[issue_id]</strong> &mdash; "<em>[title]</em>" by the System Task:</p>
<hr />
<div style="background-color: #fff; margin: 5px 10px; padding: 3px 6px; border-radius: 5px; border: thin solid gray;"><strong>This issue has been automatically closed.</strong> All issues which have been inactive for a long time are automatically closed. If you believe that this issue was closed in error, please contact us.</div>
<hr />
<p>You can view the issue at [url]</p>',0,1);

SET IDENTITY_INSERT #__it_emails OFF;

SET IDENTITY_INSERT #__it_projects ON;
INSERT IGNORE INTO [#__it_projects] (id, title, description, parent_id, lft, rgt, level, start_date, state, created_by, created_on, access)
values (1, 'Root', 'Root', 0, 0, 3, 0, GetDate(), 1, 'admin', GetDate(), 1);

INSERT IGNORE INTO [#__it_projects] (id, title, description, parent_id, lft, rgt, level, start_date, state, created_by, created_on, access)
values (10, 'Unspecified Project', 'Unspecified Project','1', 1, 2, 1, GetDate(), 1, 'admin', GetDate(), 1);
SET IDENTITY_INSERT #__it_projects OFF;

SET IDENTITY_INSERT #__it_people ON;
INSERT IGNORE INTO [#__it_people] (id, person_name, username, person_email, registered, person_role, created_by, created_on, assigned_project)
values (1, 'Anonymous', 'anon', 'anonymous@bademail.com', '0', '6', 'admin', GetDate(), '10');
SET IDENTITY_INSERT #__it_people OFF;

SET IDENTITY_INSERT #__it_custom_field_group ON;
INSERT IGNORE INTO [#__it_custom_field_group] (id,name,state,created_on,created_by) 
values (1,'Product Request',1,GetDate(),'admin');
SET IDENTITY_INSERT #__it_custom_field_group OFF;

SET IDENTITY_INSERT #__it_custom_field ON;
INSERT IGNORE INTO [#__it_custom_field] (id,name,value,type,validation,access,`group`,state,ordering,tooltip,created_on,created_by) 
values (1,'Product Details','[{"name":null,"value":"Product Details","displayInFrontEnd":"1","target":null,"alias":"","required":"1","showNull":"0"}]','header','',1,1,1,1,"",GetDate(),'admin'),
(2,'Joomla Version','[{"name":null,"value":"","target":null,"alias":"","required":"1","showNull":"0"}]','textfield','',1,1,1,2,"Details of the Joomla Version being used.",GetDate(),'admin'),
(3,'PHP Version','[{"name":null,"value":"","target":null,"alias":"","required":"0","showNull":"0"}]','textfield','',1,1,1,3,"Details of the PHP version.",GetDate(),'admin'),
(4,'Product Version','[{"name":null,"value":"","target":null,"alias":"","required":"1","showNull":"0"}]','textfield','',1,1,1,4,"Version of the product.",GetDate(),'admin'),
(5,'Database Type','[{"name":"MySQL","value":1,"target":null,"alias":"","required":"0","showNull":"0"},{"name":"MS-SQLSVR","value":2,"target":null,"alias":"","required":"0","showNull":"0"}]','radio','',1,1,1,5,"Details of the Database upon which Joomla is running.",GetDate(),'admin'),
(6,'Database Version','[{"name":null,"value":"","target":null,"alias":"","required":"0","showNull":"0"}]','textfield','',1,1,1,6,"Version number of the database.",GetDate(),'admin');
SET IDENTITY_INSERT #__it_custom_field OFF;