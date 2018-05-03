ALTER TABLE `#__it_emails`
CHANGE subject subject VARCHAR(64) NOT NULL COMMENT 'Email subject title for email message type';

INSERT IGNORE INTO `#__it_emails`(`id`, `type`, `description`, `subject`, `body`, `ordering`, `state`) 
values (16, 'auto_close','User - Issue Auto Closed','Issue [issue_id] Auto Closed',
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
, (18, 'overdue_report', 'Issue Tracker Overdue Report. Intended for Issue Assignees.', 'Issue Summary Report',
'<h3>Issue Tracker Overdue Report</h3>
<hr />
[REPORT]
<hr />', 0, 1);