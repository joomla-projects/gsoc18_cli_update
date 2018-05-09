<?php
/*
 *
 * @Version       $Id: cron.php 2280 2016-04-24 15:54:22Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.11
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-04-24 16:54:22 +0100 (Sun, 24 Apr 2016) $
 *
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

define('DEBUGIMAP', 0);

if (!defined('DS')) {
   define('DS',DIRECTORY_SEPARATOR);
}

if (! class_exists('IssuetrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

if (! class_exists('IssuetrackerHelperLog')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

if (! class_exists('IssuetrackerHelperCron')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'cron.php');
}

jimport('joomla.application.component.controlleradmin');

/** @noinspection PhpDocSignatureInspection */
class IssuetrackerControllerCron extends JControllerAdmin
{

   /**
    * @param array $config
    */
   public function __construct($config = array())
   {
      parent::__construct($config);
   }

   /**
    * Method to fetch Issue reports submitted via email.
    *
    * @internal param $none
    *
    * @return bool
    * @since   1.1.1
    */
   public function efetch()
   {
      $params  = JComponentHelper::getParams( 'com_issuetracker' );
      $logging = $params->get('enablelogging');
      $dlogging = $params->get('enabledebuglogging');
      $app = JFactory::getApplication();

      $input = JFactory::getApplication()->input;

      if ( $params->get('imap_enabled') == 0 ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ATTEMPT_TO_ACCESS_DISABLE_CRON_TASK_MSG', 'efetch'), JLog::WARNING);
         $app->redirect("index.php");
         return true;
      }

      if (!$input->get('secret') || $params->get('cron_key') != $input->get('secret')) {
         if ( $logging ) {
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_SECRET_MSG'), JLog::ERROR);
         }
         // JFactory::getApplication()->close();
         $app->redirect("index.php");
      }

      // Check whether we have any other command line parameters specified.
      // If so, use these in preference to the component settings for default project, mailbox user/password.
      // Paramaeters are specified by using &name=xyz etc on the web command line.
      $sname   = $input->get('uname','','raw');
      $spwd    = $input->get('pwd');
      $sproj   = $input->get('proj');

      if ( $logging )
         IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_EFETCH_STARTING_MSG'));

      // Code goes in here
      $db = JFactory::getDBO();

      // Fetch the connection parameters
      $server        = $params->get('imap_server');
      $username      = $params->get('imap_username');
      $password      = $params->get('imap_password');
      $connecttype   = $params->get('imap_connecttype');
      $imapport      = $params->get('imap_port');
      $pop3port      = $params->get('pop_port');
      $ssl           = $params->get('imap_ssl');
      $sslport       = $params->get('imap_ssl_port');
      $connect       = null;

      $def_project   = $params->get('imap_def_project');
      if ( empty($def_project) ) $def_project = $params->get('def_project', 10);

      // If parameters given on command line then use these.
      if ( ! empty($sname) || ! empty($spwd) || ! empty($sproj) ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog('Using command line parameters for connection.', JLog::INFO);
            // IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_COMMANDLINE_SETTINGS_SPECIFIED_MSG',$sname,$spwd,$sproj), JLog::ERROR);
         if ( !empty($sname) )   $username      = $sname;
         if ( !empty($spwd) )    $password      = $spwd;
         if ( !empty($sproj) )   {
            // Check that specified project value actually exists. If not use component default.
            $query  = "SELECT COUNT(*) FROM `#__it_projects` ";
            $query .= "WHERE id = ".$db->quote($sproj);
            $db->setQuery($query);
            $cnt = $db->loadResult();

            if ( $cnt ) {
               $def_project   = $sproj;
            } else {
               if ( $logging )
                  IssuetrackerHelperLog::dblog('Invalid project '.$sproj.' specified using component default '.$def_project.'.', JLog::WARNING);
                  // IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_INVALID_PROJECT_SPECIFIED_MSG',$sproj,$def_project), JLog::WARNING);
            }
         }
         if ( $logging )
            IssuetrackerHelperLog::dblog('Using command line parameter: '.$sname.'/'.$spwd.' Project '.$def_project, JLog::WARNING);
            // IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_COMMANDLINE_PARAMETERS_SPECIFIED',$sname,$spwd,$def_project), JLog::INFO);
         if (DEBUGIMAP) echo 'Using command line parameters: '.$sname.'/'.$spwd.' Project: '.$def_project."\n";
      }

      if ( empty($server) ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_NO_SERVER_SPECIFIED_MSG'), JLog::ERROR);
         $app->redirect("index.php");
         // exit(JText::_('COM_ISSUETRACKER_IMAP_NO_SERVER_SPECIFIED_MSG'));
      }

      if ( empty($username) ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_NO_USERNAME_SPECIFIED_MSG'), JLog::ERROR);
         $app->redirect("index.php");
         // exit(JText::_('COM_ISSUETRACKER_IMAP_NO_USERNAME_SPECIFIED_MSG'));
      }

      if ( empty($password) ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_NO_PASSWORD_SPECIFIED_MSG'), JLog::ERROR);
         $app->redirect("index.php");
         // exit(JText::_('COM_ISSUETRACKER_IMAP_NO_PASSWORD_SPECIFIED_MSG'));
      }

      // Configure connection port/type substring based on connection type and ssl parameter
      // The parameter novalidate-cert will stop cert errors of self-signed certs
      if ($connecttype == 1) {         //imap
         if($ssl) $connect = $sslport.'/novalidate-cert/imap/ssl';
         else $connect = $imapport;
         if ( $params->get('require_novalidate') ) $connect .= '/novalidate-cert';
      } elseif($connecttype == 2) {    //pop3
         if($ssl) $connect = $sslport.'/novalidate-cert/pop3/ssl';
         else $connect = $pop3port.'/pop3';
      } else {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_INVALID_CONNECT_TYPE_SPECIFIED_MSG'), JLog::ERROR);
         $app->redirect("index.php");
         // exit(JText::_('COM_ISSUETRACKER_IMAP_INVALID_CONNECT_TYPE_SPECIFIED_MSG'));
      }

      if (DEBUGIMAP) {
         echo 'Server:       '.$server."\n";
         echo 'Username:     '.$username."\n";
         echo 'Password:     '.$password."\n";
         echo 'Connect type: '.$connecttype."\n";
         echo 'SSL:          '.$ssl."\n";
         echo 'Ports:        '.$connect."\n";
      }

      // Open the connection to the mail server
      $mail = imap_open('{'.$server.':'.$connect.'}', $username, $password);
      if ($mail) {
         if (DEBUGIMAP) echo 'Server connection opened'."\n\n";
      } else {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_CANNOT_CONNECT_MSG',$server,$connect), JLog::ERROR);
         $app->redirect("index.php");
         // exit(JText::sprintf('COM_ISSUETRACKER_IMAP_CANNOT_CONNECT_MSG',$server,$connect)); // Cannot connect so exit
      }

      // Get the UNSEEN messages
      $emails = imap_search($mail, 'UNSEEN');

      $totalmessages = 0;
      $newcases      = 0;
      $existingcases = 0;
      $failsave      = 0;
      $attachcnt     = 0;
      $spamcnt       = 0;

      if (!$emails) {
         if (DEBUGIMAP) echo 'No new messages found'."\n";
         if ( $logging ) {
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NO_MAIL_FOUND_MSG'));
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_EFETCH_FINISHED_MSG'));
         }
         imap_close($mail);
         $app->redirect("index.php");
         // exit();
      } else {
         JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');

         $emailList = explode("\r\n",$params->get('email_list',''));

         // If restricting emails to registered users.
         $restrict   = $params->get('restrict_known_users');
         $remails    = array();
         if ( $restrict)
            $remails = IssuetrackerHelperCron::get_registered_emails();

         // Email messages are present, so process each message
         foreach ($emails as $message) {
            if (DEBUGIMAP) echo "\nMessage: ".$message."\n";
            $overview = imap_fetch_overview($mail, $message, 0);
            $subj = IssuetrackerHelperCron::decodeMimeString($overview[0]->subject);
            if (DEBUGIMAP) echo '  Subject:'.$subj."\n";

            $sender     = IssuetrackerHelperCron::getSenderAddress($mail, $message);
            $senderName = IssuetrackerHelperCron::getSenderName($mail, $message);

            // If restricting emails
            if ( $restrict && !empty($remails) ) {
               if ( ! in_array($sender, $remails)) {
                  if ( $logging )
                     IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_NOT_KNOWN_EMAIL_ADDR_MSG',$subj,$sender), JLog::WARNING);
                  if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_NOT_KNOWN_EMAIL_ADDR_MSG',$subj,$sender)."\n");
                  $spamcnt    += 1;
                  $failsave   += 1;
                  goto message_end;
               }
            }

            // Check if this is a banned email address.
            if (in_array($sender, $emailList)) {
               if ( $logging )
                  IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_BANNED_EMAIL_ADDR_MSG',$subj,$sender), JLog::WARNING);
               if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_BANNED_EMAIL_ADDR_MSG',$subj,$sender)."\n");
               $spamcnt    += 1;
               $failsave   += 1;
               goto message_end;
            }

            // Before we do anything else let us check that this is not SPAM.
            // Use the plain message text as the issue description
            $body = IssuetrackerHelperCron::getBody($mail, $message);

            $spam = 0;
            // Run spam checks on the message.
            $isSpam  = intval(IssuetrackerHelperCron::_isSpam($body, $sender));
            if ($isSpam) {
               if ( $logging )
                  IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_POSSIBLE_SPAM_MSG',$subj,$sender), JLog::WARNING);
               if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_IMAP_POSSIBLE_SPAM_MSG',$subj,$sender)."\n");
               $spamcnt    += 1;
               $failsave   += 1;
               $spam       = 1;
            }
            if ( $spam == 1 ) goto message_end;

            // Run check against Akismet if configured.
            $use_akismet   = $params->get('akismet_api_key','');
            if ( ! empty($use_akismet) && ($body != "[EMAIL - NO MESSAGE BODY FOUND]") ) {
               if ( IssuetrackerHelperCron::check_akismet($body, $params, $sender, $senderName) ) {
                  if ( $logging )
                     IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_AKISMET_DETECTED_SPAM_EMAIL_MSG',$subj,$sender), JLog::WARNING);
                  $spamcnt    += 1;
                  $failsave   += 1;
                  $spam       = 1;
               }
            }
            if ( $spam == 1 ) goto message_end;

            // Check if this is an issue update or a new issue.
            $cstrength = $params->get('reply_detection',0);

            // We first check the email for our custom header values, in which case it is probably a reply.
            // However note that a lot of email clients strip out custom headers so we cannot rely on that solely.
            // We then check the email header to see if the string [Issue:###] exists in the subject line?
            // If so, then we can probably assume that this is a response to an existing issue.
            $custval = IssuetrackerHelperCron::getCustomHeaders($mail, $message);
            if ( $cstrength == 0 || $cstrength == 1 )
               $pos = strpos($subj, '[Issue:');
            else
               $pos = strpos($subj, '[');

            if ( $pos === false ) {
               // No match was found in the subject headers.
               $existing = 0;
            } else {
               $existing = 1;
            }

            if ( $custval[0] != 0 ) {
               // We have custom values so use them. We only check for the first (the alias) here, since it we have one we assume we have both.
               $existing = 1;
            }

            if ( $existing == 0) {
               if (DEBUGIMAP) {
                  echo '  New case'."\n";
                  echo '    Sender:'.$sender."\n";
                  echo '    SenderName:'.$senderName."\n";
               }

               // Check if this a registered user by looking at the email address.
               $okay = true;
               $query   = "SELECT COUNT(*) as count FROM `#__it_people` ";
               $query  .= "WHERE person_email=".$db->quote($sender);
               $db->setQuery($query);
               $cnt     = $db->loadResult();

               if ($cnt <= 0 ) {
                  if (DEBUGIMAP) echo("  Sender is not currently registered.\n");
                  if ( $logging )
                     IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_SENDER_NOT_REGISTERED_MSG',$sender), JLog::INFO);
                  if ($params->get('imap_guest_create')) {
                     if (DEBUGIMAP) echo("  Public creation by non registered users possible.\n");
                  } else {
                     if (DEBUGIMAP) echo("  Public creation by non registered users disallowed.\n");
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_PUBLIC_CREATION_OF_ISSUES_DISALLOWED_BY_EMAIL_MSG'), JLog::WARNING);
                     $okay = false;
                  }
               }

               // Use the html message text as the issue description
               $body = IssuetrackerHelperCron::getBody($mail, $message, 'TEXT/HTML');

               if ($okay) {
                  // Build our issue record
                  $row = JTable::getInstance('Itissues', 'IssuetrackerTable');
                  if (!isset($row)) exit('Cannot open issue table for writing'); // We kill it here if we can't open the table

                  $row->id                      = ""; //auto-assigned by system
                  $row->priority                = $params->get('imap_def_priority');
                  if (empty($row->priority)) $row->priority = $params->get('def_priority');
                  $row->status                  = $params->get('imap_def_status');
                  if (empty($row->status)) $row->status = $params->get('def_status');
                  $row->related_project_id      = $def_project;
                  // $row->related_project_id      = $params->get('imap_def_project');
                  // if (empty($row->related_project_id)) $row->related_project_id = $params->get('def_project');
                  $row->assigned_to_person_id   = $params->get('imap_def_assignee');
                  if (empty($row->assigned_to_person_id) || $row->assigned_to_person_id == 0 ) $row->assigned_to_person_id = $params->get('def_assignee');
                  $row->identified_date         = date("Y/m/d H:i:s");

                  // $issue_summary = IssuetrackerHelperCron::safe($subj);
                  $issue_summary = $subj;
                  if ( strlen($issue_summary) <= 254 ) {
                     $row->issue_summary     = $issue_summary;
                  } else {
                     $row->issue_summary  = substr ($issue_summary, 0, 254);
                  }

                  $query  = "SELECT id from `#__it_people` WHERE person_email = '".IssuetrackerHelperCron::safe($sender)."'";
                  $db->setQuery($query);
                  if (DEBUGIMAP) print ("Query $query\n");
                  $res = $db->loadResult();
                  $def_identby   = $params->get('def_identifiedby','0');
                  $def_notify    = $params->get('def_notify', 0);

                  if ( empty($res) ) {
                     $row->identified_by_person_id = $def_identby;
                  } else {
                     $row->identified_by_person_id = $res;
                  }

                  $row->alias = IssuetrackerHelperCron::_generateNewAlias(10, $params->get('initial_site', 'Z'));
                  $row->issue_description = IssuetrackerHelperCron::clean_description($body);

                  // Populate the progress field with user details if not registered.
                  if ($row->identified_by_person_id == $def_identby) {
                     $cnewperson    = $params->get('create_new_person','0');
                     $def_role      = $params->get('def_role', '2');
                     $def_project   = $params->get('def_project', '10');

                     $row->progress = null;
                     if ( $cnewperson == '0' ) {
                        $row->progress .= JText::_('COM_ISSUETRACKER_REPORTED_BY_TEXT') . $senderName . "<br />";
                        $row->progress .= JText::_('COM_ISSUETRACKER_EMAIL_TEXT') .  $sender;
                     } else {
                        $Uname = ucwords(str_replace(array('.','_','-','@'),'_',substr($sender,0)));
                        $gnotify = $def_notify;
                        $identby = IssuetrackerHelperCron::create_new_person ( $senderName, $Uname, $sender, $gnotify, $def_role, $def_project);
                        if ( $identby == '' || $identby == 0 ) {
                           if ( $logging )
                              IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_PERSON_CREATE_ERROR_MSG',$senderName,$sender,$Uname),JLog::ERROR);
                           $identby = IssuetrackerHelperCron::_get_anon_user();
                           // Add details to progress field since we could not create the user.
                           $row->progress .= JText::_('COM_ISSUETRACKER_REPORTED_BY_TEXT') . $senderName . "<br />";
                           $row->progress .= JText::_('COM_ISSUETRACKER_EMAIL_TEXT') .  $sender . "<br />";
                        }
                        $row->identified_by_person_id = $identby;
                     }
                  }

                  if (!$row->check()) {
                     if ( $logging )
                        IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ISSUE_CHECK_ERROR_MSG',$senderName,$sender),JLog::ERROR);
                     $failsave += 1;
                     goto newrec_end;
                  }

                  // The JTable store routine itself raises calls to set session cookies, and session cache.  Hmmm
                  if ($row->store()) {
                     // Check if using alternative alias formats.
                     $iformat = $params->get('iformat', '0');
                     $oalias  = $row->alias;
                     if ( $iformat > 0 ) {
                        $rid     = $row->id;
                        $len     = 10;
                        $nalias = IssueTrackerHelper::checkAlias ($rid, $oalias, $len, $iformat );
                        $row->alias = $nalias;
                     }

                     // What about progress records?
                     if ( ! empty($row->progress) ) {
                        // Use the registered user group, make unpublished and set to private.
                        $rgroup           = 2;
                        $progresspublic   = 0;
                        $pstate           = 0;
                        $lineno           = 1;
                        $progtext         = str_replace(array("'", '"'), array("\\'", '\\"'), $row->progress);

                        // Save record in the table.
                        $query = 'INSERT INTO `#__it_progress` (issue_id, alias, progress, public, state, lineno, access) ';
                        $query .= 'VALUES('.$row->id .',"'. $row->alias.'","'. $progtext .'",'. $progresspublic .','. $pstate .','. $lineno .','. $rgroup .')';
                        $db->setQuery( $query );
                        $db->execute();
                     }
                     $row->progress = ''; // Empty out our issue progress field.

                     //get the case details for the email messages
                     $query  = "SELECT * ";
                     $query .= "FROM #__it_issues ";
                     $query .= "WHERE id = '".$row->id."' ";
                     $query .= "AND DATE_FORMAT(identified_date,'%Y-%m-%d-%H-%i-%s')='".date('Y-m-d-H-i-s',strtotime($row->identified_date))."' ";
                     $query .= "AND issue_summary=".$db->quote($row->issue_summary);
                     $db->setQuery($query);
                     $case = $db->loadAssoc();

                     // Get the assignee details
                     $query  = "SELECT person_email as email, person_name as name, email_notifications as anotify ";
                     $query .= "FROM `#__it_people` ";
                     $query .= "WHERE user_id=".$case['assigned_to_person_id']." ORDER BY username";
                     $db->setQuery($query);
                     $assignee = $db->loadAssoc();

                     // Notify the sender that the case was received unless debugging.
                     if ( ! DEBUGIMAP )
                        IssueTrackerHelper::send_email('user_new', $sender, $case);

                     // Notify assignee of new issue
                     if ( $assignee['anotify'] )
                        IssuetrackerHelper::send_email('ass_new', $assignee['email'], $case); //send new message
                     $newcases += 1;

                     if ($params->get('imap_attachments')) {
                        // Process attachments
                        $attachcnt += IssuetrackerHelperCron::process_attachments($mail, $message, $row, $params, $senderName);
                     }
                     if ($params->get('imap_deletemessages')) imap_delete($mail, $message);
                  } else {
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_ERROR_SAVING_EMAIL_ISSUE_MSG'), JLog::ERROR);
                     // echo("Error saving case\n");
                     $failsave += 1;
                  } // End if row->store
                  newrec_end:                     // Ext point for row->check failure.
               }
            } else {
               // We have custom header settings, or our searched string is present - so this could be an existing case
               $sender     = IssuetrackerHelperCron::getSenderAddress($mail, $message);

               $closed_status = $params->get('closed_status', '1');

               // Build up a db query to check if it is a known issue.
               $query  = "SELECT i.id, identified_by_person_id, assigned_to_person_id, progress, i.alias ";
               $query .= "FROM `#__it_issues` AS i ";

               // If custvals is populated then we have found our headers values, otherwise search the header.
               $iformat = $params->get('iformat', '0');
               // Modify regex dependant upon alias format!
               switch ($iformat) {
                  case 0:
                     // Current basic random number string 10 characters
                     if ( $cstrength == 0 || $cstrength == 1 )
                        $regex = '/\[Issue\:\s*([a-z0-9]{10,10})\s*\]/i';
                     else
                        $regex = '/\[[a-z:\s]{0,}([a-z0-9]{10,10})\s*\]/i';
                     break;
                  case 1:
                     // Leading character followed by zeros and then the number.
                     if ( $cstrength == 0 || $cstrength == 1 )
                       $regex = '/\[Issue\:\s*([a-z]{1,1}[0-9]{9,9})\s*\]/i';
                     else
                       $regex = '/\[[a-z:\s]{0,}([a-z]{1,1}[0-9]{9,9})\s*\]/i';
                     break;
                  case 2:
                    // Numeric string padded to right with blanks.
                    if ( $cstrength == 0 || $cstrength == 1 )
                       $regex = '/\[Issue\:\s*([0-9]{1,10})\s*\]/i';
                    else
                       $regex = '/\[[a-z:\s]*(\d+)\s*\]/i';
                    break;
                  default:
                     // Current basic random number string 10 characters
                     if ( $cstrength == 0 || $cstrength == 1 )
                        $regex = '/\[Issue\:\s*([a-z0-9]{10,10})\s*\]/i';
                     else
                        $regex = '/\[[a-z:\s]{0,}([a-z0-9]{10,10})\s*\]/i';
                     break;
               }

               if ( $dlogging )
                  IssuetrackerHelperLog::dblog(JText::sprintf('Mail title testing update: %s %s %s',$subj, $iformat,$cstrength), JLog::DEBUG);

               if ( $custval[0] == 0 || empty($custval[1]) ) {
                  // Try getting the issue (alias) from the subject header.
                  $hasIssue = preg_match($regex, $subj, $matches);

                  if ($hasIssue) {
                     $issuealias = $matches[1];
                     // We need to pad this for format 2.
                     if ( strlen($issuealias) != 10 )
                        $issuealias = str_pad($issuealias, 10, ' ', STR_PAD_RIGHT);
                  } else {
                     // Indicates alias not found. Effectively causes query to fail.
                     $issuealias = 0;
                     if (DEBUGIMAP) echo '    Regex found no alias in header. '.$subj.'\n';
                  }


                  $query .= "LEFT JOIN `#__it_people` AS p ";
                  $query .= "  ON i.identified_by_person_id = p.id  ";
                  // Add our where clause
                  $query .= "WHERE i.status != ".$closed_status;
                  $query .= " AND  i.alias=" . $db->quote($issuealias);
                  $query .= " AND  p.person_email = " . $db->quote($sender);
               } else {
                  $issuealias = $custval[1];
                  $issueid    = $custval[0];
                  $query .= "WHERE i.status != ".$closed_status;
                  $query .= " AND  i.alias=" . $db->quote($issuealias);
                  $query .= " AND  i.id = ". $db->quote($issueid);
               }

               $db->setQuery($query);
               if (DEBUGIMAP) echo '    Case Query:'.$query."\n";
               $case = $db->loadRow();
               $issueid = $case[0];

               if (DEBUGIMAP) {
                  echo '  Existing issue'."\n";
                  echo '    Subject:  '.$subj."\n";
                  echo '    Issue Id: '.$case[0]."\n";
                  echo '    Alias:    '.$case[4]."\n";
                  echo '    Issue Alias: '.$issuealias."\n";
               }

               // Make sure we got a case back
               if (!isset($case[0]) || count($case)<=0 ) {
                  if ( $logging )
                     IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_REPLY_EMAIL_NO_ISSUE_EXISTS_MSG',$issuealias), JLog::WARNING);
                  if (DEBUGIMAP) echo(JText::sprintf('COM_ISSUETRACKER_REPLY_EMAIL_NO_ISSUE_EXISTS_MSG',$issuealias));
                  $okay = false;
               } else {
                  $okay = true;
                  // We have found the issue so process it
                  // $issueid = $case[0];

                  if (DEBUGIMAP) echo '    Issue ID:'.$issueid."\n";
                  $senderName = IssuetrackerHelperCron::getSenderName($mail, $message);

                  if (DEBUGIMAP) {
                     echo '    Sender: '.$sender."\n";
                     echo '    SenderName: '.$senderName."\n";
                     echo '    Issue Id: '.$issueid."\n";
                     echo '    Identifier Id: '.$case[1]."\n";
                     echo '    Assignee Id: '.$case[2]."\n";
                  }

                  // Check to see if the sender is either the issue identifier, or an assignee.
                  $query  = "SELECT COUNT(*) as count FROM `#__it_people` AS u ";
                  $query .= "WHERE person_email = '".$sender."' ";
                  $query .= "AND ( id = '".$case[1]."' ";
                  $query .= "OR user_id = '".$case[2]."' )";
                  $db->setQuery($query);
                  if (DEBUGIMAP) echo '    Sender Query:'.$query."\n";
                  $cnt = $db->loadResult();

                  if ($cnt <= 0 ) {
                     if (DEBUGIMAP) echo("  Sender is not the identifier, assignee or administrator.\n");
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_REPLY_EMAIL_FROM_UNKNOWN_SENDER_MSG'), JLog::WARNING);
                     // Exit if the sender is not authorized to update this issue.
                     $okay = false;
                  }
               }

               if ($okay) {
                  // If user is recognised, update the progress history using the message information
                  // Get username of sender
                  $query = "SELECT username FROM `#__it_people` WHERE person_email=" . $db->quote($sender);
                  if (DEBUGIMAP) echo '    Query:'.$query."\n";
                  $db->setQuery($query);
                  $username = $db->loadResult();

                  // If we did not get a returned name, then set it to the sendername
                  if ( empty($username) || strlen($username) <=0) $u = $senderName;
                  else $u = $username;
                  if (DEBUGIMAP) echo '    SenderUsername:'.$u."\n";

                  // Get the html text of the message body text for the progress update.
                  $body = IssuetrackerHelperCron::getBody($mail, $message, "TEXT/HTML");

                  $sprefix = $params->get('reply_prefix');
                  $msghandler = $params->get('updmsg_handler', 0);
                  $hasreply = 0;
                  if ( !empty($sprefix) ) {
                     $hasreply = IssuetrackerHelperCron::hasReplyAboveLine($body, $sprefix);
                     if ( $hasreply )
                        $body = IssuetrackerHelperCron::extractMessage($body, $sprefix);
                  }

                  if ( $msghandler == 1  ||
                     ( $msghandler == 0 && $hasreply == 1 ) )  {
                     if (IssuetrackerHelperCron::update_progress($issueid, $body, $case[4]) ) {
                        // Get the issue details for the email messages
                        $query  = "SELECT * ";
                        $query .= "FROM `#__it_issues` ";
                        $query .= " WHERE id = ".$case[0];
                        if (DEBUGIMAP) echo '    Case Query:'.$query."\n";
                        $db->setQuery($query);
                        $case = $db->loadAssoc();   // Reuse variable.

                        // Get assignee details
                        $query  = "SELECT person_email, email_notifications , person_name ";
                        $query .= "FROM `#__it_people` ";
                        $query .= "WHERE person_email = ".$db->quote($sender);
                        if (DEBUGIMAP) echo '    Identifier Query:'.$query."\n";
                        $db->setQuery($query);
                        $identifier = $db->loadAssoc();

                        //get assignee details
                        $query  = "SELECT person_email, email_notifications , person_name ";
                        $query .= "FROM #__it_people ";
                        $query .= "WHERE user_id = '".$case['assigned_to_person_id']."'";
                        if (DEBUGIMAP) echo '    Assignee Query:'.$query."\n";
                        $db->setQuery($query);
                        $assignee = $db->loadAssoc();

                        // On an update, only notify user only if config is set to do so
                        if ($identifier['email_notifications']) {
                           IssuetrackerHelper::send_email('user_update', $sender, $case);
                        }

                        // Notify assignee of updates unless the assignee is the one doing the changes
                        if ( $assignee['email_notifications'] && ! ( $sender == $assignee['person_email'] ) )
                           IssuetrackerHelper::send_email('ass_update',$assignee['person_email'], $case);

                        if ($params->get('imap_deletemessages')) imap_delete($mail, $message);
                     } else {
                        if ( $logging )
                           IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ERROR_SAVING_ISSUE_UPDATE_MSG',$issuealias), JLog::ERROR);
                     } //end if updateprogress

                     if ($params->get('imap_attachments')) {
                        $attachcnt += IssuetrackerHelperCron::process_attachments($mail, $message, $case, $params, $senderName);
                     }
                  } else {
                     $okay = false;
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ERROR_NO_ABOVE_LINE_MSG',$issuealias), JLog::ERROR);
                     if (DEBUGIMAP) echo JText::sprintf('COM_ISSUETRACKER_ERROR_NO_ABOVE_LINE_MSG', $issuealias);
                  }
               }
               if ($okay)
                  $existingcases += 1;
               else
                  $failsave += 1;
            }
            message_end:                    // End message processing
            if (DEBUGIMAP) echo "\n\n";
            $totalmessages += 1;
         }
      }

      // Close the mailbox
      if ($params->get('imap_deletemessages') && $emails) imap_close($mail, CL_EXPUNGE);
      else imap_close($mail);

      if ( $logging )
         IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_IMAP_SUMMARY_MSG',$totalmessages,$newcases,$failsave,$existingcases,$attachcnt,$spamcnt), JLog::INFO);
      // print(JText::sprintf('COM_ISSUETRACKER_IMAP_SUMMARY_MSG',$totalmessages,$newcases,$failsave,$existingcases,$attachcnt,$spamcnt));

      if ( $logging )
         IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_EFETCH_FINISHED_MSG'));
      $app->redirect("index.php");
      return true;   // Never reached!
   }

   /**
    * Method to generate issue summary report and email.
    *
    * @internal param $none
    *
    * @return bool
    * @since   1.1.1
    */
   public function esummary()
   {
      $params  = JComponentHelper::getParams( 'com_issuetracker' );
      $logging = $params->get('enablelogging');

      $app = JFactory::getApplication();
      $input = JFactory::getApplication()->input;

      if ( $params->get('enable_frontendcron') == 0 ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ATTEMPT_TO_ACCESS_DISABLE_CRON_TASK_MSG', 'esummary'), JLog::WARNING);
         $app->redirect("index.php");
         return true;
      }

      if(!$input->get('secret') || $params->get('cron_key') != $input->get('secret')) {
         if ( $logging ) {
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_SECRET_MSG'), JLog::ERROR);
         }
         $app->redirect("index.php");
      }

      $sdate = $input->get('sdate');
      $edate = $input->get('edate');
      // If not specified display the last months worth of data.
      if ( empty($sdate) ) $sdate = date("Y-m-d", strtotime("-1 months"));
      if ( empty($edate) ) $edate = date("Y-m-d");

      // Code goes in here
      date_default_timezone_set('UTC');
      $date    = date(DATE_RFC2822);
      $db      = JFactory::getDBO();

      // Get list of email addresses to whom summary is to be sent.
      $query  = "SELECT distinct username, person_email ";
      $query .= "FROM `#__it_people` ";
      $query .= "WHERE ( issues_admin = 1 OR staff = 1 )";
      $query.= " AND email_notifications = 1";
      $db->setQuery($query);
      $emailaddrs = $db->loadAssocList();

      if ( empty($emailaddrs) && $logging ) {
         IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_WARNING_NO_REPORT_RECIPIENTS_MSG"),JLog::INFO);
         $app->redirect("index.php");
         // exit();
      }

      // Get summary report template if it exists
      $query = "SELECT subject, body FROM #__it_emails WHERE type = 'summary_report' AND state = 1";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      // Generate report.
      $rows = IssuetrackerHelperCron::issuesummary($sdate, $edate);

      // $SiteName   = $params->get('emailSiteName', '');
      $fromadr    = $params->get('emailFrom', '');
      $sender     = $params->get('emailSender', '');
      // $link       = $params->get('emailLink', '');
      $replyto    = $params->get('emailReplyto', '');
      $replyname  = $params->get('emailReplyname','');

      $subject = JText::_('COM_ISSUETRACKER_SUMMARY_REPORT_MSG');

      // Report header
      if ( empty($mdetails) ) {
         $message  =  '<div style="padding: 0 20px;">';
         $message .=  '<span style="display: block; font-size: 18px; font-weight: bold; margin: 25px 0 -15px 0;"> '. JText::_('COM_ISSUETRACKER_SUMMARY_REPORT_MSG') . ' - ' . $date . '</span><br /><br />';
         $message .= JText::_('COM_ISSUETRACKER_REPORT_PERIOD_TEXT').$sdate.' -> '.$edate.'<br />';
      } else {
         $message = JText::_('COM_ISSUETRACKER_REPORT_PERIOD_TEXT').$sdate.' -> '.$edate.'<br /><br />';
      }

      // Add table headers
      $message .=  "<table><tr>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_FIRST_OPENED_DATE')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_LAST_CLOSED_DATE')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_LAST_MODIFIED_DATE')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_TOTAL_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ONHOLD_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_INPROGRESS_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_CLOSED_ISSUES')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_NOPRIOR')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_HIGH')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_MEDIUM')."</th>";
      $message .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_OPEN_LOW')."</th>";
      $message .=  "</tr>";

      // Format row results
      foreach ($rows as $row) {
         $message .= "<tr>";
         $message .= "<td>" . $row->project_name . "</td>";
         // $message .= "<td>" . $row->project_id . "</td>";
         $message .= "<td>" . $row->first_identified . "</td>";
         $message .= "<td>" . $row->last_closed . "</td>";
         $message .= "<td>" . $row->last_modified . "</td>";
         $message .= '<td style="text-align: center">' . $row->total_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->onhold_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->inprogress_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->closed_issues . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_no_prior . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_high_prior . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_medium_prior . "</td>";
         $message .= '<td style="text-align: center">' . $row->open_low_prior . "</td>";
         $message .= "</tr>";
      }
      $message .= "</table>";

      if (empty($mdetails) ) {
         // Add date to bottom of report.
         $message .= '<div style="position: fixed; bottom: 0; height: 40px; width: 100%; margin: 20px 0 0 0; background: #000; color: #FFF; line-height: 40px;">' .
         'Report generated ' . $date . '</div>';
         $body    = $message;
      } else {
         $message .= '<br /><div style="height: 40px; width: 100%; margin: 20px 0 0 0; background: #000; color: #FFF; line-height: 40px;">' .
         'Report generated ' . $date . '</div>';
         $body = str_replace('[REPORT]', $message, $mdetails[1]);
      }

      // Clean the email data
      if ( empty($mdetails) ) {
         $subject = JMailHelper::cleanSubject( $subject );
      } else {
         $subject = JMailHelper::cleanSubject( $mdetails[0] );
      }

      $body    = JMailHelper::cleanBody( $body );
      $fromadr = JMailHelper::cleanAddress( $fromadr );

      if ( $logging )
         IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_SUMMARY_REPORT_MSG'));

      // For efficiency build up the recipient list so we only send one email.
      $recipient = array();
      reset( $emailaddrs);
      while (list($key, $val) = each( $emailaddrs)) {
         // $username = $emailaddrs[$key]['username'];
         $email    = $emailaddrs[$key]['person_email'];
         if ( JMailHelper::isEmailAddress( $email ) ) {
            $recipient[] = $email;
         }
      }

      $mail = JFactory::getMailer();
      $mail->isHTML(true);
      $mail->Encoding = 'base64';
      $mail->addRecipient($recipient);
      // if ( !empty($replyto) ) $mail->addReplyTo(array($replyto,$replyname));
      if ( !empty($replyto) ) $mail->addReplyTo($replyto,$replyname);
      $mail->setSender(array($fromadr, $sender));
      $mail->setSubject($subject);
      $mail->setBody($body);

      if (!$mail->Send()) {
         if ( $logging )
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_MAIL_SENDING_ERROR'),JLog::ERROR);
         return false;   // if there was trouble, return false for error checking in the caller
      }
      unset ($mail);

      $app->redirect("index.php");
      return true;   // Never reached!
   }

   /**
    * Method to generate issue overdue reports and email.
    *
    * @internal param $none
    *
    * @return bool
    * @since   1.1.1
    */
   public function eoverdue()
   {
      $params  = JComponentHelper::getParams( 'com_issuetracker' );
      $logging = $params->get('enablelogging');
      $app = JFactory::getApplication();
      $input = JFactory::getApplication()->input;

      if ( $params->get('enable_frontendcron') == 0 ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ATTEMPT_TO_ACCESS_DISABLE_CRON_TASK_MSG', 'eoverdue'), JLog::WARNING);
         $app->redirect("index.php");
         return true;
      }

      if(!$input->get('secret') || $params->get('cron_key') != $input->get('secret')) {
         if ( $logging ) {
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_SECRET_MSG'), JLog::ERROR);
         }
         // JFactory::getApplication()->close();
         $app->redirect("index.php");
      }

      $db = JFactory::getDBO();
      date_default_timezone_set('UTC');
      $date       = date(DATE_RFC2822);

      // $SiteName   = $params->get('emailSiteName', '');
      $fromadr    = $params->get('emailFrom', '');
      $sender     = $params->get('emailSender', '');
      // $link       = $params->get('emailLink', '');
      $replyto    = $params->get('emailReplyto', '');
      $replyname  = $params->get('emailReplyname','');

      $subject = JText::sprintf('COM_ISSUETRACKER_OVERDUE_REPORT_MSG','');

      // Get list of assignees to whom the report should be sent.
      // To be assigned the person has to be a staff member
      $query  = "SELECT distinct username, person_email, user_id ";
      $query .= "FROM `#__it_people` p ";
      $query .= "LEFT JOIN `#__it_issues` i  ";
      $query .= "ON p.user_id = i.assigned_to_person_id  ";
      $query .= "WHERE ( issues_admin = 1 OR staff = 1 )";
      $query.= " AND email_notifications = 1";
      $db->setQuery($query);
      $emailaddrs = $db->loadAssocList();

      if ( empty($emailaddrs) && $logging ) {
         IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_WARNING_NO_REPORT_RECIPIENTS_MSG"),JLog::INFO);
         $app->redirect("index.php");
         // exit();
      }

      // Get overdue report template if it exists
      $query = "SELECT subject, body FROM #__it_emails WHERE type = 'overdue_report' AND state = 1";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      // Report header and footer
      if ( empty($mdetails) ) {
         $rheader  =  '<div style="padding: 0 20px;">';
         $rheader .=  '<span style="display: block; font-size: 18px; font-weight: bold; margin: 25px 0 -15px 0;"> '. JText::sprintf('COM_ISSUETRACKER_OVERDUE_REPORT_MSG','') . ' ' . $date . '</span><br /><br />';

         // Prepare report footer.
         $rfooter  = '<div style="position: fixed; bottom: 0; height: 40px; width: 100%; margin: 20px 0 0 0; background: #000; color: #FFF; line-height: 40px;">' .
         'Report generated ' . $date . '</div>';
      } else {
         $rheader = '';
         $rfooter = '';
      }

      // Table headers
      $rheader .=  "<table><tr>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_NUMBER')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PRIORITY')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_IDENTIFIED_DATE')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_PROJECT_NAME')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_FIELD_STATUS_LABEL')."</th>";
      $rheader .=  '<th style="padding: 2px;">'.JText::_('COM_ISSUETRACKER_ISSUE_OVERDUE_DAYS')."</th>";
      $rheader .=  "</tr>";

      // $domain = $params->get('imap_site_base', '');
      // if (empty($domain)) $domain = JURI::root();

      // Create report for each assignee
      foreach ($emailaddrs as $eaddr) {
         $uid = $eaddr['user_id'];
         $message = '';

         $rows = IssuetrackerHelperCron::issueoverdue($uid);

         // Format row results
         foreach ($rows as $row) {
            $message .= "<tr>";
            $message .= '<td style="text-align: center">' . $row->alias . "</td>";
            $message .= "<td>" . $row->issue_summary . "</td>";
            $message .= '<td style="text-align: center">' . $row->priority . "</td>";
            $message .= '<td style="text-align: center">' . $row->ident_date . "</td>";
            $message .= "<td>" . $row->project_name . "</td>";
            $message .= '<td style="text-align: center">' . $row->status_name . "</td>";
            $message .= '<td style="text-align: center">' . $row->overdue . "</td>";
            $message .= "</tr>";
         }
         $message .= "</table>";

         if (empty($mdetails) ) {
            $body    = $rheader.$message.$rfooter;
         } else {
            $message = $rheader.$message;
            $body = str_replace('[REPORT]', $message, $mdetails[1]);
         }

         // Clean the email data
         if ( empty($mdetails) ) {
            $subject = JMailHelper::cleanSubject( $subject );
         } else {
            $subject = JMailHelper::cleanSubject( $mdetails[0] );
         }

         $body    = JMailHelper::cleanBody( $body );
         $fromadr = JMailHelper::cleanAddress( $fromadr );

         if ( $logging )
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_OVERDUE_REPORT_MSG',$eaddr['username']));

         // For efficiency build up the recipient list so we only send one email.
         $recipient = array();
         $recipient[] = $eaddr['person_email'];

         $mail = JFactory::getMailer();
         $mail->isHTML(true);
         $mail->Encoding = 'base64';
         $mail->addRecipient($recipient);
         // if ( !empty($replyto) ) $mail->addReplyTo(array($replyto,$replyname));
         if ( !empty($replyto) ) $mail->addReplyTo($replyto,$replyname);
         $mail->setSender(array($fromadr, $sender));
         $mail->setSubject($subject);
         $mail->setBody($body);

         if (!$mail->Send()) {
            if ( $logging )
               IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_MAIL_SENDING_ERROR'),JLog::ERROR);
            $app->redirect("index.php");
            return false;   // if there was trouble, return false for error checking in the caller
         }
         unset($mail);
      }

      $app->redirect("index.php");
      return true;   // Never reached!
   }

   /**
    * Method to generate issue overdue reports and email.
    *
    * @internal param $none
    *
    * @return bool
    * @since   1.1.1
    */
   public function autoclose()
   {
      $params  = JComponentHelper::getParams( 'com_issuetracker' );
      $logging = $params->get('enablelogging');
      $app     = JFactory::getApplication();
      $input   = JFactory::getApplication()->input;

      if ( $params->get('enable_frontendcron') == 0 ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ATTEMPT_TO_ACCESS_DISABLE_CRON_TASK_MSG', 'autoclose'), JLog::WARNING);
         $app->redirect("index.php");
         return true;
      }

      if ( !$input->get('secret') || $params->get('cron_key') != $input->get('secret') ) {
         if ( $logging )
            IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_IMAP_SECRET_MSG'), JLog::ERROR);
         $app->redirect("index.php");
      }

      $db         = JFactory::getDBO();
      date_default_timezone_set('UTC');
      $date       = date(DATE_RFC2822);

      // Get value from parameters for status  Awaiting customer.
      $cust_status   = $params->get('waiting_customer_status', 0);
      if ( $cust_status == 0 ) {
         if ($logging )
            IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_ERROR_NO_CUSTOMER_STATUS_MSG"),JLog::ERROR);
         $app->redirect("index.php");
      }

      $closed_status = $params->get('closed_status', '1');
      if ($closed_status == $cust_status) {
         if ($logging )
            IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_AUTOCLOSE_STATUSES_ARE_IDENTICAL_MSG"),JLog::ERROR);
         $app->redirect("index.php");
      }

      $query         = "SELECT status_name FROM `#__it_status` WHERE id IN (".$closed_status.','.$cust_status.")";
      $db->setQuery($query);
      $scol          = $db->loadColumn();
      $closed_text   = $scol[0];
      $cust_text     = $scol[1];

      $overdue_days = $params->get('overdue_days', 21);

      $olderthan  = $input->get('olderthan', 0);
      // Use value on command line if given otherwise use component setting.
      if ( $olderthan == 0 ) $olderthan = $overdue_days;

      $consider = date("Y-m-d", strtotime(-$olderthan . ' DAYS'));

      // Get all issues older than $consider days and in the 'waiting_user' category.
      // Criteria for the user is no progress update older than specifed date.
      $query  = "SELECT i.id, i.alias FROM `#__it_issues` AS i ";
      $query .= "LEFT JOIN (SELECT issue_id, modified_on, created_on, lineno ";
      $query .= "            FROM (SELECT t1.* FROM `#__it_progress` AS t1 ";
      $query .= "                  JOIN (SELECT issue_id, MAX(lineno) lineno FROM `#__it_progress` GROUP BY issue_id) AS t2 ";
      $query .= "                   ON t1.issue_id = t2.issue_id AND t1.lineno = t2.lineno) AS t3 ";
      $query .= "           ) AS p ON p.issue_id = i.id ";
      $query .= "WHERE i.status = ".intval($cust_status);
      $query .= "  AND (( (i.modified_on = '0000-00-00 00:00:00' OR i.modified_on is NULL ) && i.created_on <= '".$consider." 08:00:00' ) ";
      $query .= "      OR ( i.modified_on <= '".$consider." 08:00:00' ))  ";
      $query .= "  AND (( (p.modified_on = '0000-00-00 00:00:00' OR p.modified_on is NULL ) && p.created_on <= '".$consider." 08:00:00' ) ";
      $query .= "       OR ( p.modified_on <= '".$consider." 08:00:00' )); ";

      $db->setQuery($query);
      $issuelist = $db->loadAssocList();

      if ( empty($issuelist) ) {
         if ($logging) IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_INFO_NO_AUTOCLOSE_ISSUES_MSG"),JLog::INFO);
         $app->redirect("index.php");
      }

      // Now loop through the issues.
      while (list($key, $val) = each( $issuelist)) {
         $issue_id = $issuelist[$key]['id'];
         $alias    = $issuelist[$key]['alias'];

         $query  = "UPDATE `#__it_issues` ";
         $query .= "SET status = ".intval($closed_status);
         $query .= " WHERE `id` = ".$issue_id;
         $db->setQuery($query);
         $result = $db->execute();

         if (!$result) {
            if ($logging)
               IssueTrackerHelperLog::dblog(JText::sprintf("COM_ISSUETRACKER_ERROR_CLOSING_ISSUE_MSG", $alias, $issue_id),JLog::ERROR);
         } else {
            // Update progress and resolution with auto close message.
            $ntext = JText::sprintf("COM_ISSUETRACKER_AUTO_CRON_STATUS_CHANGED_MSG", $alias, $issue_id, $cust_text, $closed_text);
            IssueTrackerHelper::add_progress_change($issue_id, $ntext);
            IssueTrackerHelper::add_resolution_change($issue_id, $ntext);

            IssueTrackerHelper::send_auto_close_msg($issue_id);

            if ( $logging )
               IssueTrackerHelperLog::dblog(JText::sprintf("COM_ISSUETRACKER_CLOSED_ISSUE_MSG", $alias, $issue_id));
         }
      }

      $app->redirect("index.php");
      return true;   // Never reached!
   }

}