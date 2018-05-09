<?php defined( '_JEXEC' ) or die( 'Restricted access' ); die() ?>
Issue Tracker 1.6.11
==============================================================================
~ Backport sanmple data handling from 1.7.0
# Change calls to addReplyTo method due to Joomla 3.5.1 changes.
~ Correct trigger creation on it_people table.
# Correct method definition of _getAssetParentId in JTrigger table.
# Fix warning message from back end project list view.

Issue Tracker 1.6.10
==============================================================================
# Correct issue checkin/checkout which was not working upon a front end edit. Checkout not occurring.
+ Introduce option to control FE issue admin delete option.
# Add additional check in FE delete if issue is locked out.
# Modify admin notifications for more than one issue administrator.
# Updated XMAP plugin to latest 1.7.0 version.
+ Modify front end issue creation when the captcha challenge fails, populate the previously entered data fields in the re-displayed form.
~ Refine front redirection when issue edit is cancelled.
# Fix installation of tag content entries, preventing use of tags in component.
+ Permit tags to be added to an issue in the front end.
# Correct handling of detection of super users in sample data addition and removal.
# Remove attachments from file system when issue is deleted.
# Correct query getting assignee details for the cron email fetch tasks.
+ Added call to run Content plugins on issue description and progress text fields in front end.
- Remove progress field from the list of displayed fields in front end issue lists.
- Remove duplicated credit information.
~ Update copyright year to 2016
# Correct attachments file path width in back end view.

Issue Tracker 1.6.9
==============================================================================
+ New email tags changed_by_fullname and changed_by_username for email templates.
# Correct sending of assignee emails when identifier edits an issue in the front end (to add more information).
# Fix handling of custom fields in front end on issue save.
+ Permit access rules to apply to Custom Fields contained within email templates.

Issue Tracker 1.6.8
==============================================================================
~ Add check for REPORT tag in message template bodies, where it is not valid.
~ Change translator credits to link to a site article instead of being hard coded in the release.
! Remove Akeeba restore points since it is no longer supported by Akeeba in Joomla 3.4 and above.
# Correct language string used for autoclose status check. Missing underscore in string.
# Correct back end sort of in issue list on project field.
# Fix handling of phone key from profile in system plugin.
# Fix handling of email updates for issues.
+ Add debug logging parameter to display more specific debugging messages in log.
+ New XMAP plugin added for site map generation.
# Modify cron handling to handle alternative alias formats.
# Fix checking of custom fields access levels in FE edit form.
+ Add ability to specify the default progress record access, published state and privacy defaults.
# Fix problem with captcha not being checked on front end issue form submission.
# Modify BE issue list view so that 'private' issues can not be published in line with issue model.
# Modify issue list controller to filter out private issues for publish status so that change counts are correct.
# Allow for archive state value of 2 where it was formally -1. Both are now allowed for.
# Fix problem in FE when unpublished issue state being changed to published when the component default is published.
# Fix date display to allow for server offset in email messages and log display.
# Correct display of assignee name in FE form.
# Modify 'relaxed' scheduled email update detection of issue number in mail header.
# Change checks on 'assigned_to' field on issue save.

Issue Tracker 1.6.7
==============================================================================
# Fix update of alias upon issue save if one of the alternative formats chosen.
~ Change system plugin to use default notification values in synchronised users.
# Correct logic on call to message sending when alternative alias type used.
+ Complete implementation of access level controls upon custom fields.
~ Prevent error message in log about SMS configuration if we are not sending SMS anyway!
# Progress record update message not clear enough and missing issue number in log.
+ Add ability to specify custom fields in email template bodies.
~ Increase size of email template subject line to 64 characters.
+ Add ability to send an auto-close message to the user when an issue is auto closed by the cron task. Also provide new auto-close message template.
+ If Joomla user profile is enabled and phone number is a valid specification, synchronise with it_people table in system plugin.
+ Add alternative alias formats support to cron email fetch tasks.
# Correct a couple of malformed language strings. Clean up unused language strings.
+ Add check feature for issue identifier notification setting when an issue is edited/created by an issue admin/staff in the front end.
+ Add additional check in autoclose task to ensure that closed status and customer waiting status are not identical.
+ New message template for Issue Summary Report.
+ New message template for Issue Overdue Report.

Issue Tracker 1.6.6
==============================================================================
# Fix back end Ajax sort of projects.
~ Modify compound key on it_people person_name and email fields to be 240 chars together on new installs. Existing installs stay as they are.
~ Change People table phone index so that it is not a unique index.
~ Update copyright year to 2015
# Fix problem saving of a custom field group.
# Fix problem of install text not expanding out on a fresh install.
# Fix custom field group cancel in form.
~ Display published field as an option in custom field group item display.
~ Modify credits so that they have a link to the Transifex profile. Joomla 3 only.
# Fix SQL error on new installs.

Issue Tracker 1.6.5
==============================================================================
~ Remove last traces of all JRequest and $app->getCfg() usages which are deprecated.
~ Modify back end list views to use drag and drop ordering. (Joomla 3.3)
~ Modify back end item views to make better use of the page and standard Joomla layouts.
~ Modify display size of description field in backend item views for Type, Priorities, Status etc.
# Fix display of custom fields in back end edit when we have an already defined field to remove unwanted fields.
~ Change front end project drop down list to only display projects for which the user has access.
~ Update credits with new translations (Catalan, Chinese (Taiwan), Slovak and Swedish).
+ Add new feature to define different issue types per project. Joomla 3.3 only.
+ Add new feature to download issue attachments in the front end. See documentation for details and implications.
+ Add new identified_date filter to FE issue list. Only shown if identified_date is also shown.
~ New interface to AcySMS to permit sending of SMS notifications.
# Fix situation where a user update email notification was not being sent.
# Fix display of custom fields when only one project is specified on the create menu item which is not the default project.
# Fix form generated code when custom fields loaded from Ajax call.
+ Add ability to have multiple checkbox custom fields.
+ Removed last references to JParameter (it was removed in Joomla! 3.4 alpha)

Issue Tracker 1.6.4
==============================================================================
~ Only display advanced audit tab in back end if advanced auditing is enabled. Also controls display of trigger creation icon in Control Panel.
~ Add additional checks to front end form for the strange situation where user profile has the editor field missing.
# Correct display of custom field error reports. Mainly applicable to Joomla 3.x but involved a rewrite of some common code.
# Fix display of progress data in issue display in front end. Test was incorrect.
~ Add some additional checks for embedded https addresses in cron email. Also provide some optional messages on invalid words and links counts in email messages.
+ Add new checks to ensure access field is set for saving of progress data in issue save.
+ Add export functionality of Issues to create a cvs file in the back end. Joomla 3.x only.
~ Supply defaults for back end access settings for issues and progress records. Formally left to default.
~ Modify URL link in emailed issue messages to remove menu id.
# Fix front end issue selector for non published issues when being viewed by the original issue identifier.
~ Added log display to side panel for Joomla 3.x to make selection easier.
+ Add an optional control for the display of the project filter in front end issue list.
# Fix ordering direction parameter pickup in front end issue list.
+ Add quote checks around saving progress field in front end issue save method.
+ Add issue number above tabs in back end issue edit for existing, not a new issue.
# Correct default issue privacy flag when default if set to private in front end.
# Correct pagination in front end list views for Joomla 3.3.
~ Change email routine such that only the last progress record is sent rather than all of the progress records for an issue.
~ Change email routine (updates) such that the identifier is sent private as well as public progress records. Formally only progress records marked as public were emailed.
~ Continued update of phpDoc comments and code cleanup.

Issue Tracker 1.6.3
==============================================================================
# Fix undefined variable $isadmin in front end issue view.
# Fix undefined variable in front end issue model SQL statement identified_by. At same time refine query to handle registered user access of public or private issues.
# Fix line up of front end issue items seen in Joomla 3.3
~ Change redirection to calling page if the issue creation is canceled. Formally returned to the home page.
# Fix test that checks for a change in visibility from public to private and which changes the published state to unpublished to work correctly.
# Fix PHP error in dates helper file.
~ Add additional checks around the use of an editor in the front end form.
~ Update translation credits
+ Add option to display issue status in Latest Issues Module. Also make display of close date an option.
~ Make specification of single person mandatory in the front end menu item. Also add additional check in view itself.
# Fix popup issue view in Latest Issues Module.
+ New option to display issue type in front end list displays.
+ Extra checks for database log_bin setting turned on, SUPER privilege and log_bin_trust_function_creators setting.
~ Modify icon settings for delete icon for Joomla 3.x.
# Correct return address when an issue is deleted in the front end. It cannot return to the issue display since we have deleted it, so display home page.
~ Only display attachments tab in back end if attachments are enabled. Also checks if IMAP attachments are enabled.

Issue Tracker 1.6.2
==============================================================================
+ Add ability to specify additional parameters on mail fetch task to specify a mail server user and password, and the default project.
# Fix adding of blank lines when progress information added to table. Impacts front end and back end.
# Fix display of unpublished custom fields in back end when an issue is created.
~ Alter scheduled sending of summary and overdue reports to not send reports if email notification flag not set.
~ Change so that username is always generated when saving a user if not specified (when creating an issue).
~ Change redirection so that it displays the 'newly' created issue when issue is created in front end.
~ Modify front end behaviour such that a guest user raising an issue will not see it unless it is published. i.e. The default issue state is published.
# Fix strange looping problem seen on one system caused by a suspected PHP 5.3 bug.
# Fix SQL error when displaying staff assigned issues list in front end.
+ Add option to specify an alternative (simpler) issue number (alias).
~ Handle situation where a request to view an invalid issue results in issue not found more gracefully in front end.
# Fix log list view check all toggle.
# Fix single project menu view not passing project id to view.
# Fix custom field dropdown selection not picking up correct value.
~ Permit issue admin and staff member to ignore published state of an issue in the front end and to see the details.
# Fix front end projects list view links for Joomla 3.x.
~ Change placement of audit panel in back end progress edit.
# Correct project filter in front end issue list where all projects are displayable.
# Correct check for time added to date fields.
~ Change calendar fields all date fields are stored in the database as UTC and displayed in the appropriate time zone.
+ New date helper file for handling time zone conversions.
~ Modify handling of issue target resolution date field.
+ Add getStateQuery override routine to Issue Tracker finder plugin.
~ Simplify back end audit template files to remove duplication.
# Issue privacy field missing from Joomla 3.3 view.
+ Add issue privacy check into front end issue fetch.
~ Modify project drop down list to only display published and unpublished project.
~ Modify front end issue and project lists so that staff as well as admin can see unpublished projects in the project drop down list.
~ Modify front end issue display so that admin and staff can view progress information irrespective of component setting.

Issue Tracker 1.6.1
===============================================================================
# Fix saving of an issue in back end on Joomla 3.x systems.
# Fix handling of ISO-8859-2 characters for cron email fetching.
# Fix searching in pactions.
# Fix missing issue alias in progress records in back end on Joomla 3.x systems
+ Add auto close cron task.
~ Update translation credits
~ Change value for JQuery location to remove http: prefix, so that it works with https accessed sites.
~ Change drop down list for users to include email address so that it is easier to identify users since username is not unique.
+ Add filter of email addresses to scheduled email fetch task to help eliminate SPAM.

Issue Tracker 1.6.0
===============================================================================
+ Add ability to have custom fields for issues.
~ Change progress recording such that progress information is stored in its own table.
~ Change code to allow for changes in Joomla 3.2 API relating to redirection.
~ Review usage of strings in admin side of the site.
~ Remove restriction on only permitting one file to be attached in the front end.
+ Include the attachments present on an issue in the front end edit form, if attachments are enabled.
~ Only display Issue Resolution in front end if there is a resolution to display. i.e. Open issues will not display resolution details.
+ Add ability for an attachment to have a user provided title.
+ Add encoding for Cron submitted email for subject header.
+ Add check for ALTER privilege in DB as well as CREATE for badly configured DBs when checking DB  privileges.
+ Numerous minor fixes and changes.
