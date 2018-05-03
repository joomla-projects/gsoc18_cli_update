INSERT IGNORE INTO `#__it_emails`(`id`,`type`,`description`,`subject`,`body`,`ordering`,`state`) 
values (10,'int_close','Interest - Issue Closed','Issue [issue_id] Closed','<p>The following issue has been closed.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>Priority: [priority]</p>
<p>Project: [project]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">ASSIGNEE INFORMATION</span></p>
<p>Username: [assignee_uname]</p>
<p>Email: [assignee_email]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">RESOLUTION</span></p>
<p>[resolution]</p>',0,1)
, (11,'int_new','Interest - New Issue created.','Issue [issue_id] Created','<p>The following issue has created.</p>
<p>You can view the issue at [url]</p>
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
<p><span style="text-decoration: underline;">ASSIGNEE INFORMATION</span></p>
<p>Username: [assignee_uname]</p>
<p>Email: [assignee_email]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>',0,1)
, (12,'int_update','Interest - Issue Updated','Issue [issue_id] Updated','<p>The following issue has been updated.</p>
<p>You can view the issue at [url]</p>
<p><span style="text-decoration: underline;">ISSUE DETAILS</span></p>
<p>&nbsp; ID: [issue_id]</p>
<p>User: [user_name]</p>
<p>Date: [startdate]</p>
<p>Title: [title]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">ASSIGNEE INFORMATION</span></p>
<p>Username: [assignee_uname]</p>
<p>Email: [assignee_email]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">DESCRIPTION</span></p>
<p>[description]</p>
<p>&nbsp;</p>
<p><span style="text-decoration: underline;">PROGRESS</span></p>
<p>[progress]</p>',0,1);