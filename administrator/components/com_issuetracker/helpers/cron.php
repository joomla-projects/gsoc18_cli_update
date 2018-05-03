<?php
/*
 *
 * @Version       $Id: cron.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//Import log libraries. Perhaps not necessary, but does not hurt
JLoader::import( 'joomla.log.log' );

define('DEBUGCRON', 0);

/**
 * Class IssueTrackerHelperCron
 */
class IssueTrackerHelperCron
{
   /**
    * Extracts the sender's email address from the message 'fromaddress'
    *
    * @param $mail
    * @param $message
    * @return string
    *
    */
   public static function getSenderAddress($mail, $message)
   {
      $sender = imap_headerinfo($mail, $message)->fromaddress;
      if (DEBUGCRON) echo '    Fromaddress:'.$sender."\n";
      // TODO Change to use preg_match.
      $start = strpos($sender, '<');
      // if (DEBUGCRON) echo '    Addr Start: '.$start."\n";

      $end = strrpos($sender, '>');
      // if (DEBUGCRON) echo '    Addr End: '.$end."\n";

      $sender = substr($sender, $start+1 , $end-$start-1);

      return $sender;
   }


   /**
    * Extracts our custom header details if any.
    *
    * @param $mail
    * @param $message
    * @return array
    */
   public static function getCustomHeaders($mail, $message)
   {
      $issueid = 0;
      $issuealias = null;

      $rawheaders  = imap_fetchheader($mail, $message);
      $rawheaders  = explode("\n", $rawheaders);
      if (is_array($rawheaders) && count($rawheaders)) {
         foreach ($rawheaders as $line) {
            $line  = trim($line);
            $line  = strtolower($line);
            if (substr($line, 0, 15) == 'x-it-issuealias') {
               $parts       = explode(':', $line, 2);
               $issuealias  = $parts[1];
            }
            if (substr($line, 0, 12) == 'x-it-issueid') {
               $parts       = explode(':', $line, 2);
               $issueid     = $parts[1];
            }
         }
      }

      if (DEBUGCRON) {
         echo '    Issue Id: '.$issueid."\n";
         echo '    Issue Alias: '.$issuealias."\n";
      }

      return array( $issueid, $issuealias);
   }

   /**
    * Extracts the sender's email address from the message 'fromaddress'
    *
    * @param $mail
    * @param $message
    * @return mixed|string
    */
   public static function getSenderName($mail, $message)
   {
      $sender = imap_headerinfo($mail, $message)->fromaddress;
      if (DEBUGCRON) echo '    From address: '.$sender."\n";
      // TODO Change to use preg_match
      $start = strpos($sender, '<');
      if (DEBUGCRON) echo '    Addr Start: '.$start."\n";

      $sender = trim(substr($sender, 0 , $start)); //get trimmed sender name

      if (strlen($sender)<=0) $sender = self::getSenderAddress($mail, $message); //if there is no name, just get the address

      $sender = str_replace('"','',$sender); //remove any quotes around name

      return $sender;
   }


   /**
    * Finds the body part of the message, gets its decoded text and returns it.
    *
    * If a specific format is requested return it otherwise return empty message.
    * If no specific format is requested, return HTML by default unless that is unavailable
    * in which case try and return plain text. If that is not available return empty message.
    *
    * @param $mail
    * @param $message
    * @param string $mtype
    * @return mixed
    */
   public static function getBody($mail, $message, $mtype = 'TEXT/PLAIN')
   {
      $plainText  = self::get_part($mail, $message, "TEXT/PLAIN");
      $HTML       = self::get_part($mail, $message, "TEXT/HTML");

      if ( ! empty($mtype) ) {
         if ( $mtype == "TEXT/PLAIN" && !empty($plainText) ) {
            $body = $plainText;
         } elseif ( $mtype == "TEXT/HTML" && !empty($HTML) ) {
            $body = $HTML;
         } else {
            // One of them could be empty if so use the other.
            if ( !empty($plainText) && empty($Html) ) {
               $body = $plainText;
            } else if (empty($plainText) && !empty($HTML) ) {
               $body = $HTML;
            } else {
               $body = JText::_("COM_ISSUETRACKER_EMPTY_EMAIL_BODY_MSG");
            }
         }
      } else {
         if (!empty($HTML)) {
            $body = $HTML;
         } elseif (!empty($plainText)) {
            $body = $plainText;
         } else {
            $body = JText::_("COM_ISSUETRACKER_EMPTY_EMAIL_BODY_MSG");
         }
      }

      if (DEBUGCRON) echo "\n".$body."\n\n";
      return $body;
   }

   /**
    * Update progress field with information.
    *
    * @param $id        integer The id of the issue.
    * @param $upd       string  The update to the progress record.
    * @param $alias     string  The issue alias.
    * @return mixed
    */
   public static function update_progress($id, $upd, $alias )
   {
      if ( empty($id) ) return false;
      if ( empty($upd) || $upd == '') return false;

      $db = JFactory::getDBO();

      // JLoader::import('joomla.utilities.date');
      // $jdate = new JDate();
      // $udate = $jdate->toSql();

      // TODO Need to cleanse input text to see if we have any duplicate information in the message previously supplied.
      $nn = self::getTextBetweenTags('body', $upd);
      $upd2 = "";
      foreach ($nn as $item) {
         $upd2 .= $item;
      }

      // Check the details and strip empty lines etc.
      $ntext = IssuetrackerHelperCron::safe($upd2);

      // Find the row number
      $query   = "SELECT max(lineno)+1, alias, public, state, access FROM `#__it_progress` WHERE issue_id = '".$id."'";
      $db->setQuery( $query );
      $prow    = $db->loadRow();
      $lineno  = $prow[0];

      if (empty($lineno)) {
         $lineno  = 1;
         $public  = 0;
         $state   = 0;
         $access  = 2;
      } else {
         $public = $prow[2];
         if ( $public == 0) {             // If a private issue.
            $state = 0;
         } else {
            $state = $prow[3];
         }
         $access  = $prow[4];
         $alias   = $prow[1];
      }

      $user = JFactory::getUser();
      $uname = $user->username;
      if (empty($uname) || $uname == '' ) $uname = 'Email';
      // Save record in the table.
      $query  = 'INSERT INTO `#__it_progress` (issue_id, alias, progress, public, state, lineno, access, created_by, created_on) ';
      $query .= "VALUES(".$id.",'". $alias ."','". $ntext ."',". $public .",". $state .",". $lineno .",". $access .",'". $uname ."', NOW() )";
      $db->setQuery( $query );

      if (DEBUGCRON) echo '    Insert Query:'.$query."\n";
      $res = $db->execute();
      if (!$res) {
         return false;
      }
      return true;
   }

   /**
    *
    * @get text between tags.  This is supposedly quicker than using a regex directly.
    *
    * @param string $tag The tag name
    * @param string $html The XML or XHTML string
    * @param int $strict Whether to use strict mode
    *
    * @return array
    *
    */
   public static function getTextBetweenTags($tag, $html, $strict=0)
   {
       /*** a new dom object ***/
       $dom = new domDocument;

       /*** load the html into the object ***/
       if ($strict==1) {
           $dom->loadXML($html);
       } else {
           $dom->loadHTML($html);
       }

       /*** discard white space ***/
       $dom->preserveWhiteSpace = false;

       /*** the tag by its tag name ***/
       $content = $dom->getElementsByTagname($tag);

       /*** the array to return ***/
       $out = array();
       foreach ($content as $item) {
           /*** add node value to the out array ***/
           $out[] = $item->nodeValue;
       }
       /*** return the results ***/
       return $out;
   }

   /**
    * The following functions (get_mime_type and get_part) recursively get the parts of the email
    * and return them correctly decoded message.  The functions come from
    * http://www.linuxscope.net/articles/mailAttachmentsPHP.html
    * with adjustments for our usage.
    *
    * @param $structure
    * @return string
    *
    */
   public static function get_mime_type(&$structure)
   {
      $primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
      if ($structure->subtype) {
         return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
      }
      return "TEXT/PLAIN";
   }

   /**
    * Method to get the message part.
    *
    * @param $stream
    * @param $msg_number
    * @param $mime_type
    * @param bool $structure
    * @param bool $part_number
    * @internal param number $msg_
    * @return bool|string
    */
   public static function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number    = false)
   {
      $charset = 'UTF-8';
      if (!$structure)   $structure = imap_fetchstructure($stream, $msg_number);

      if ($structure) {
         if ($mime_type == self::get_mime_type($structure)) {
            if (!$part_number) $part_number = "1";

            $text = imap_fetchbody($stream, $msg_number, $part_number);

            switch($structure->encoding) {
               case 1:
                  $body = imap_utf8($text);
                  break;
               case 3:
                  $body = imap_base64($text);
                  break;
               case 4:
                  $body = imap_qprint($text);
                  break;
               default:
                  $body = $text;
            }

            if ($structure->ifparameters) {
               foreach($structure->parameters as $it) {
                  if (strtolower($it->attribute) == 'charset') {
                     if ( strtoupper($it->value) != 'UTF-8' )
                        $body = iconv(strtoupper($it->value), 'UTF-8', $body);
                  }
               }
            }
            return $body;
         }

         if ($structure->type == 1) /* multipart */ {
            while(list($index, $sub_structure) = each($structure->parts)) {
               if ($part_number) $prefix = $part_number.'.';
               else $prefix = null;

               $data = self::get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix.($index + 1));
               if($data) return $data;
            }
         }
      }
      return false;
   }

   /**
    * Clean up the text for the description.
    *
    * @param $str
    * @return string
    */
   public static function clean_description($str)
   {
      // Extract any text between the body tags if there are any present.
      if (preg_match('/<body.*>/', $str)) {
         $nn = self::getTextBetweenTags('body', $str);
         $strg = "";
         foreach ($nn as $item) {
            $strg .= $item;
         }
      } else {
         $strg = $str;
      }
      return self::safe($strg);
   }


   /**
    * escapes input to stop sql injection and XSS attacks
    *
    * @param $str string The text to clean.
    * @return string
    */
   public static function safe($str)
   {
      //use of ENT_QUOTES necessary to prevent injection of single quotes
      // return htmlentities($str, ENT_QUOTES, 'UTF-8', FALSE);

      // Change any double quites to singles.
      $ntext = nl2br(str_replace ( "\"", "\"\"", $str)) ;

      // Change any UTF-8 non breaking spaces
      $ntext = preg_replace('#\xc2\xa0#', ' ', $ntext);

      // Remove blank lines.
      $ntext = preg_replace('#^(<br */?>\s*)+#i', '', $ntext);

      // Remove multiple consecutive <br /> with or without trailing spaces.
      $ntext = preg_replace('#(<br */?>\s*)+#i', '<br />', $ntext);

      // Remove new lines from end of strings
      $ntext = preg_replace('#(<br */?>\s*)+#i', '<br />', $ntext);

      return $ntext;

   }

   /**
    * Method to generate a new alias (Issue number)
    *
    * @param int|string $len - The length of the desired alias
    * @param string $fchar
    * @return string
    */
   public static function _generateNewAlias($len = 10, $fchar = 'Z')
   {
      // Possible seeds
      $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';

      list($usec, $sec) = explode(' ', microtime());
      $seed = (float) $sec + ((float) $usec * 100000);
      mt_srand($seed);

      // Start all front end issues with the letter Z
      $str = $fchar;
      $seeds_count = strlen($seeds);
      $length = $len - 1;
      for ($i = 0; $length > $i; $i++)
      {
         $str .= $seeds{mt_rand(0, $seeds_count - 1)};
      }
      return $str;
   }

   /**
    * @return mixed
    */
   public static function _get_anon_user()
   {
      if (empty($db)) { $db = JFactory::getDBO(); }
      $query = "SELECT id from `#__it_people` WHERE username = 'anon'";
      $db->setQuery( $query );
      $id = $db->loadResult();
      return $id;
   }

   /**
    *
    * Method to perform internal check for configured spam
    * @param $msg
    * @param $sender
    * @return int
    */
   public static function _isSpam($msg, $sender)
   {
      $params = JComponentHelper::getParams( 'com_issuetracker' );
      $debuglogging  = $params->get('enabledebuglogging');

      //filters first
      // $ipList = explode("\r\n",$params->get('ip_list',''));
      $emailList = explode("\r\n",$params->get('email_list',''));

      if (in_array($sender, $emailList)) return 1;

      //OK, filters have passed. Now check link count & words
      $wordList = explode("\r\n",$params->get('word_list',''));
      if (count($wordList) > 1) {
         foreach ($wordList as $word) {
            if (stristr($msg, $word)) {
               if ( $debuglogging )
                  IssuetrackerHelperLog::dblog('Message contains a word in the restricted list. '.$word. 'Sender: '.$sender, JLog::DEBUG);
               return 1;
            }
         }
      }

      // Check how many urls - This is a basic form of caching. Enhance to allow for https addresses as well.
      // Just because its https does not necessarily mean its valid. Also log them if requested.
      $no_http  = substr_count($msg, 'http://');
      $no_https = substr_count($msg, 'https://');
      if ( $debuglogging )
         IssuetrackerHelperLog::dblog('Message URL embedded link counts Http: '.$no_http.' Https: '.$no_https.' Sender: '.$sender, JLog::DEBUG);

      $lnksum = $no_http + $no_https;
      if ($lnksum  >= $params->get('link_count',3))   return 1;
      return 0;
   }

   /**
    * Public method to create a new entry in the it_people table
    * based upon the supplied user details.
    *
    * @param $Name
    * @param $Uname
    * @param $Email
    * @param $notify
    * @param $def_role
    * @param $def_project
    * @return mixed
    */
   public static function create_new_person($Name, $Uname, $Email, $notify, $def_role, $def_project)
   {
      if (empty($db)) { $db = JFactory::getDBO(); }
      // Check if we have this user already registered.
      $query  = "SELECT count(person_name) from `#__it_people` WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
      $db->setQuery( $query );
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         $query  = "INSERT into `#__it_people` (person_name, username, person_email, email_notifications, registered, person_role, assigned_project)";
         $query .= "values('".$Name."','".$Uname."', '".$Email."', '".$notify."', '0', '".$def_role."','".$def_project."')";
         $db->setQuery($query);
         $db->execute();
         //  if (!$ret) {
         //     $app = JFactory::getApplication('site');
         //     $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
         //  }
      } else {
         $query  = "UPDATE `#__it_people` set email_notifications = ".$notify." WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
         $db->setQuery($query);
         $db->execute();
         //  if (!$ret) {
         //     $app = JFactory::getApplication('site');
         //     $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
         //  }
      }

      $query = "SELECT id from `#__it_people` WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
      $db->setQuery( $query );
      $id = $db->loadResult();
      return $id;
   }

   /**
    * Public method to check if the specified directory exists.
    * If it does check if an index.html file exists.
    * If not create one.
    *
    * If directory does not exist create it and the index.html file.
    *
    * @param $dir
    * @return bool
    */
    public static function checkuploadDir( $dir )
    {
       $ndir = JPATH_ROOT.DS.$dir;
       if (!JFolder::exists( $ndir )) {
          try {
             JFolder::create( $ndir, 0755 );
          } catch (Exception $e) {
             Jlog::add($e->getMessage(), JLog::ERROR, 'com_issuetracker');
             return false;
          }

          $data = "<!DOCTYPE html><title></title>";
          try {
             JFile::write($ndir.DS."index.html", $data);
          } catch (Exception $e) {
             Jlog::add($e->getMessage(), JLog::ERROR, 'com_issuetracker');
             return false;
          }
       } else {
          if ( !JFile::exists ( $ndir.DS."index.html")) {
             $data = "<!DOCTYPE html><title></title>";
             try {
                JFile::write($ndir.DS."index.html", $data);
             } catch (Exception $e) {
                Jlog::add($e->getMessage(), JLog::ERROR, 'com_issuetracker');
                return false;
             }
          }
       }
       return true;
    }

   /**
    * Does the message have a "please reply above this line" line?
    *
    * @param array $body
    * @param $prefix
    * @return bool
    */
   public static function hasReplyAboveLine($body, $prefix)
   {
      $postfix = strrev($prefix);
      $regex = '/'.$prefix.'(.*)'.$postfix.'/s';
      // $regex = '/#-#- (.*) -#-#/s';
      // Add strip tags to body.
      $bbody = strip_tags($body);
      $result = preg_match_all($regex, $bbody, $matches);
      return ($result !== false) && ($result > 0);
   }

   /**
    * Extracts the part of the message which is above the reply line
    *
    * @param $body
    * @param string $cstring  Our text for the above line.
    * @return string
    */
   public static function extractMessage($body, $cstring)
   {
      $parts = explode($cstring, $body);
      return $parts[0];
   }

   /**
    * Methods to get the Issue Summary
    *
    * @param $sdate
    * @param $edate
    * @return object with data
    */
   public static function issueSummary ($sdate, $edate)
   {
      $db = JFactory::getDBO();

      $query  = "SELECT title as project_name, t2.id as project_id, ";
      $query .= "   DATE_FORMAT( MIN(identified_date), \"%d.%m.%Y\") AS first_identified, ";
      $query .= "   DATE_FORMAT( MAX(actual_resolution_date), \"%d.%m.%Y\") AS last_closed, ";
      $query .= "   DATE_FORMAT( MAX(t1.modified_on), \"%d.%m.%Y\") AS last_modified, ";
      $query .= "   COUNT(t1.id) AS total_issues, ";
      $query .= "   SUM(IF(status='4',1,0)) AS open_issues, ";              // Open = 4
      $query .= "   SUM(IF(status='3',1,0)) AS onhold_issues, ";            // On-Hold = 3
      $query .= "   SUM(IF(status='2',1,0)) AS inprogress_issues, ";        // In-Progress = 2
      $query .= "   SUM(IF(status='1',1,0)) AS closed_issues, ";       // Closed = 1
      $query .= "   SUM(IF(status='4',IF(priority IS NULL,1,0),0)) AS open_no_prior, ";
      $query .= "   SUM(IF(status='4',IF(priority='1',1,0),0))  AS open_high_prior, ";   // High = 1
      $query .= "   SUM(IF(status='4',IF(priority='3',1,0),0)) AS open_medium_prior, ";  // Medium = 2
      $query .= "   SUM(IF(status='4',IF(priority='2',1,0),0)) AS open_low_prior ";      // Low = 3
      $query .= "FROM #__it_issues t1 ";
      $query .= "RIGHT OUTER JOIN #__it_projects t2 ";
      $query .= " ON t1.related_project_id = t2.id ";
      $query .= "  WHERE ( t1.identified_date BETWEEN ".$db->quote($sdate)." AND " .$db->quote($edate)." ) ";
      $query .= "     OR ( t1.actual_resolution_date BETWEEN ".$db->quote($sdate)." AND ".$db->quote($edate).") ";
      $query .= "     OR ( t1.modified_on BETWEEN ".$db->quote($sdate)." AND ".$db->quote($edate).")";
      $query .= " AND t2.state IN (0,1) ";
      $query .= "GROUP BY related_project_id ";
      $query .= "HAVING COUNT(related_project_id) > 0 ";
      $query .= "ORDER BY t2.lft ";

      $db->setQuery($query);
      $rows = $db->loadObjectList();

      $rows = IssueTrackerHelper::updateprojectname($rows);

      return $rows;
   }

   /**
    * Methods to get the Issue Overdue rows
    *
    * @param $uid
    * @return object with data
    */
   public static function issueoverdue ($uid)
   {
      $db = JFactory::getDBO();

      $query  = "SELECT i.id, i.alias, pr.priority_name AS priority, i.issue_summary,  ";
      $query .= "  p.person_name assignee,  ";
      $query .= "  DATE_FORMAT(i.identified_date, \"%d.%m.%Y\") AS ident_date,  ";
      $query .= "  r.title AS project_name  ";
      $query .= "  ,r.id as project_id  ";
      $query .= "  , p.person_email ";
      $query .= "  , st.status_name ";
      $query .= "  , TIMESTAMPDIFF(DAY,identified_date,CURDATE()) AS overdue ";
      $query .= "FROM `#__it_issues` i ";
      $query .= "RIGHT OUTER JOIN `#__it_people` p ";
      $query .= "  ON i.assigned_to_person_id = p.id  ";
      $query .= "LEFT JOIN `#__it_projects` r  ";
      $query .= "ON i.related_project_id = r.id  ";
      $query .= "LEFT JOIN `#__it_priority` pr  ";
      $query .= " ON i.priority = pr.id  ";
      $query .= "LEFT JOIN `#__it_status` st  ";
      $query .= " ON i.status = st.id   ";
      $query .= "WHERE (actual_resolution_date IS NULL OR actual_resolution_date = '0000-00-00 00:00:00')  ";
      $query .= "  AND i.status != '1' ";
      $query .= "  AND TIMESTAMPDIFF(DAY,i.modified_on,CURDATE()) > 1  ";
      $query .= "  AND i.state >= 0 ";
      $query .= "  AND i.assigned_to_person_id = '".$uid."' ";
      $query .= "ORDER BY overdue DESC ";

      $db->setQuery($query);
      $rows = $db->loadObjectList();

      $rows = IssueTrackerHelper::updateprojectname($rows);

      return $rows;
   }

   /**
    * Process attachments based on suggestions
    * from http://stackoverflow.com/questions/2649579/downloading-attachments-to-directory-with-imap-in-php-randomly-works
    *
    * @param $mail       resource imap_stream  Mail from imap server
    * @param $message    int     Message number
    * @param $row        object  Record for issue in database
    * @param $params     object  Parameters for component
    * @param $senderName string  Name of the sender of the email
    * @return int        The number of attachments added.
    */
   public static function process_attachments($mail, $message, $row, $params, $senderName)
   {
      $logging    = $params->get('enablelogging');
      $maxfiles   = $params->get('max_files');
      $maxfsize   = $params->get('max_file_size');
      $maxfsize   = $maxfsize * 1024 * 1024; // Change to bytes.

      $attachcnt  = 0;
      $structure  = imap_fetchstructure($mail, $message);

      $attachments = array();
      if (isset($structure->parts) && count($structure->parts)) {
         for ($i = 0; $i < count($structure->parts); $i++) {
            $attachments[$i] = array(
               'is_attachment' => false,
               'filename' => '',
               'name' => '',
               'attachment' => '');

            if ($structure->parts[$i]->ifdparameters) {
               foreach($structure->parts[$i]->dparameters as $object) {
                  if(strtolower($object->attribute) == 'filename') {
                     $attachments[$i]['is_attachment'] = true;
                     $attachments[$i]['filename'] = $object->value;
                  }
               }
            }

            if ($structure->parts[$i]->ifparameters) {
               foreach ($structure->parts[$i]->parameters as $object) {
                  if (strtolower($object->attribute) == 'name') {
                     $attachments[$i]['is_attachment'] = true;
                     $attachments[$i]['name'] = $object->value;
                  }
               }
            }

            if ($attachments[$i]['is_attachment']) {
               $attachments[$i]['attachment'] = imap_fetchbody($mail, $message, $i+1);
               if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                  $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
               }
               elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                  $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
               }
            }
         } // for($i = 0; $i < count($structure->parts); $i++)
      } // if(isset($structure->parts) && count($structure->parts))

      $filecnt = 0;   // file counter for no of attachments in the email.
      if (count($attachments)!=0){
         foreach ($attachments as $at) {
            if ($at['is_attachment'] == 1 && $filecnt < $maxfiles ) {
               // Change the filename to be the original file name and use the hashname in the file_put_contents call.
               // Get a randomised name
               if (version_compare(JVERSION, '3.0', 'ge')) {
                  $serverkey = JFactory::getConfig()->get('secret','');
               } else {
                  $serverkey = JFactory::getConfig()->getValue('secret','');
               }

               $sig = $at['filename'].microtime().$serverkey;
               if (function_exists('sha256')) {
                  $mangledname = sha256($sig);
               } elseif (function_exists('sha1')) {
                  $mangledname = sha1($sig);
               } else {
                  $mangledname = md5($sig);
               }

               // Get default settings
               $def_path = $params->get('attachment_path', 'media/com_issuetracker/attachments');
               if ( substr($def_path, 0, 1) != '/')   $def_path = '/'.$def_path;
               if ( substr($def_path, -1) != '/')     $def_path = $def_path . '/';

               // ...and its full path
               $filepath = JPath::clean(JPATH_ROOT . $def_path . $mangledname);

               // Check directory exists and has a valid index.html file.
               if ( !IssuetrackerHelperCron::checkuploadDir ($def_path) ) {
                  // if ( !$this->checkuploadDir ($def_path) ) {
                  // Error message set in method.
                  // return false;
                  goto attrec_end;
               }

               file_put_contents($filepath, $at['attachment']);  // Save to the filesystem.
               // file_put_contents($at['filename'], $at['attachment']);  // Save to the filesystem. Current directory.
               if ( !empty($at['filename']) ) {
                  // JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');
                  $arow = JTable::getInstance('Attachment', 'IssuetrackerTable');

                  // Get the MIME type
                  if (function_exists('mime_content_type')) {
                     $mime = mime_content_type($filepath);
                  } elseif (function_exists('finfo_open')) {
                     $finfo = finfo_open(FILEINFO_MIME_TYPE);
                     $mime = finfo_file($finfo, $filepath);
                  } else {
                     $mime = 'application/octet-stream';
                  }

                  $nfsize = filesize($filepath);
                  if ( $nfsize <= $maxfsize ) {
                     JLoader::import('joomla.utilities.date');
                     $jdate = new JDate();

                     // Populate our row data.
                     $arow->filename      = $at['filename'];
                     $arow->description   = 'Attachment no: '.$message;
                     $arow->filepath      = $filepath;
                     $arow->hashname      = $mangledname;
                     $arow->filetype      = $mime;
                     $arow->size          = filesize($filepath);
                     $arow->created_on    = $jdate->toSql();
                     //         $ndata['enabled']     = 1;
                     $arow->issue_id      = $row->alias;
                     // $ndata['uid']         = $user->id;  // Could use admin here.
                     $arow->title         = $row->issue_summary;
                     $arow->state         = 1;
                     $arow->created_by    = $senderName;

                     /* Should use this code but it currently is not supported in J2.5.
                        try {
                           $arow->store();
                        } catch (RuntimeException $e) {
                           if ( $logging )
                              IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ERROR_SAVING_FILE_ATTACHMENT_MSG',$arow->filename).' Error: '.$e->getMessage(), JLog::ERROR);
                           echo $e->getMessage();
                        }
                     */

                     $result = $arow->store();
                     if ( !$result) {
                        if ( $logging )
                           IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ERROR_SAVING_FILE_ATTACHMENT_MSG',$arow->filename), JLog::ERROR);
                        // echo $this->getError();
                     } else {
                        if ( $logging )
                           IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_FILE_ATTACHMENT_SAVED_MSG',$arow->filename), JLog::INFO);
                     }
                     $attachcnt += 1;
                  } else {
                     if ( $logging )
                        IssuetrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_FILE_MAXSIZE_EXCEEDED_MSG',$at['filename']), JLog::WARNING);
                     // Remove file from our system.
                     unlink($filepath);
                  }
               }
               $filecnt += 1;
            } else {
               if ( $logging )
                  IssuetrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_FILE_MAX_EXCEEDED_MSG'), JLog::WARNING);
                  goto attrec_end;
            }
         }
      }
      attrec_end:
      return $attachcnt;
   }

   /**
    * Get all of the registered users into an array.
    * This will be quicker than checking each email separately
    *
    */
   public static function get_registered_emails()
   {
      $db = JFactory::getDBO();
      $query = "SELECT person_email from `#__it_people` ORDER by person_email";
      $db->setQuery($query);
      if (DEBUGIMAP) echo '    Sender Query:'.$query."\n";
      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
          $emails = $db->loadColumn();
      } else {
          $emails = $db->loadResultArray();
      }
      $rusers = array();

      if ( !empty($emails) ) {
         return $emails;
      }

      return $rusers;
   }


   /**
    *
    * Method to check whether the included text is spam using Akismet
    * Details from akismet.com
    *
    * Input is an array with the text in the comment_content element.  Other fields should get populated in the _getAkismet method.
    *
    * $data = array('blog' => 'http://yourblogdomainname.com',
    *   'user_ip' => '127.0.0.1',
    *   'user_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6',
    *   'referrer' => 'http://www.google.com',
    *   'permalink' => 'http://yourblogdomainname.com/blog/post=1',
    *   'comment_type' => 'comment',
    *   'comment_author' => 'admin',
    *   'comment_author_email' => 'test@test.com',
    *   'comment_author_url' => 'http://www.CheckOutMyCoolSite.com',
    *   'comment_content' => 'It means a lot that you would take the time to review our software.  Thanks again.');
    *
    * @param $data
    * @param $params
    * @param $sender
    * @param $senderName
    * @return bool
    * @throws Exception
    */
   public static function check_akismet($data, $params, $sender, $senderName)
   {
      $asiteref = $params->get('site_url');
      $apikey   = $params->get('akismet_api_key');
      if ( empty($apikey) ) {
         IssuetrackerHelperLog::dblog('No Akismet API key detected. NO spam check possible.', JLog::WARNING);
         return true;
      }
      $akismet = new Akismet($asiteref, $apikey);
      if (!$akismet->isKeyValid()){
         throw new Exception(JText::_('COM_ISSUETRACKER_AKISMET_INVALID_API_KEY'));
      }

      // Use author set to 'viagra-test-123' to get a positive test back.
      $akismet->setCommentAuthor($senderName);
      $akismet->setCommentAuthorEmail($sender);

      // Need the following for the Akismet call.
      $akismet->setUserIP('127.0.0.1');

      if ( empty($asiteref) ) {
         $akismet->setreferrer($asiteref);
      }
      // $akismet->setInreplyto(NULL);
      // $akismet->setReferences(NULL);

      $akismet->setCommentContent($data);
      $akismet->setCommentType('comment');

      try {
         if ($akismet->isCommentSpam()) {
            // Its defined as spam just return true
            return true;
         }
      } catch (Exception $e) {
         if (JDEBUG) JError::raiseWarning(500, $e->getMessage());
         return false;
      }

      return false;
   }

   /**
    *  Receive a string with a mail header and returns it
    * decoded to a specified charset.
    * If the charset specified into a piece of text from header
    * isn't supported by "mb", the "fallbackCharset" will be
    * used to try to decode it.
    *
    * @param $mimeStr
    * @param string $inputCharset
    * @param string $targetCharset
    * @param string $fallbackCharset
    * @return string $decodedStr
    */
   public static function decodeMimeString($mimeStr, $inputCharset='utf-8', $targetCharset='utf-8', $fallbackCharset='iso-8859-1') {
      $encodings=self::mb_list_lowerencodings();
      $inputCharset=strtolower($inputCharset);
      $targetCharset=strtolower($targetCharset);
      $fallbackCharset=strtolower($fallbackCharset);

      $decodedStr='';
      $mimeStrs=imap_mime_header_decode($mimeStr);
      for ($n=sizeOf($mimeStrs), $i=0; $i<$n; $i++) {
         $mimeStr=$mimeStrs[$i];
         $mimeStr->charset=strtolower($mimeStr->charset);
         if (($mimeStr == 'default' && $inputCharset == $targetCharset)
            || $mimeStr->charset == $targetCharset) {
            $decodedStr.=$mimeStr->text;
         } else {
            $decodedStr.=mb_convert_encoding(
            $mimeStr->text, $targetCharset,
            (in_array($mimeStr->charset, $encodings) ?
            $mimeStr->charset : $fallbackCharset)
            );
         }
      } return $decodedStr;
   }

   /**
    * return supported encodings in lowercase.
    *
    */
   static function mb_list_lowerencodings() { $r=mb_list_encodings();
      for ($n=sizeOf($r); $n--; ) { $r[$n]=strtolower($r[$n]); } return $r;
   }
}