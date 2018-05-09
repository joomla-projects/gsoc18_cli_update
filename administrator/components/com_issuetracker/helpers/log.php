<?php
/*
 *
 * @Version       $Id: log.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 * Codes for JLog follow:
 * ALERT = 2
 * ALL = 30719
 * CRITICAL = 4
 * DEBUG = 128
 * EMERGENCY = 1
 * ERROR = 8
 * INFO = 64
 * NOTICE = 32
 * WARNING = 16
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//Import log libraries. Perhaps not necessary, but does not hurt
JLoader::import( 'joomla.log.log' );

/**
 * Class IssueTrackerHelperLog
 */
class IssueTrackerHelperLog
{
   /*
    * Public function to log an entry to the database table.
    *
    */
   /**
    * @param $comment
    * @param string $priority
    * @param string $category
    */
   public static function dbLog($comment, $priority = '', $category = '')
   {
      if ( is_null($priority) || empty($priority) ) $priority = JLog::INFO;
      // According to 11.4 docs category is an array object for the addLogger method.
      if ( is_null($category) || empty($category) ) $category = 'com_issuetracker';
      /*
      // Add the logger.
      JLog::addLogger( array( 'logger' => 'database' ), JLog::ALL, 'itlog' );
      */
      JLog::addLogger(array('logger' => 'database', 'db_type' => 'mysql', 'db_table' => '#__it_issues_log'), JLog::ALL, $category );

      // Add the message.
      if ( ! empty($comment) )
         JLog::add($comment, $priority, $category);

   }

   /*
    * Public function to log the array contents to the table
    * using a Json encode string.
    *
    */
   /**
    * @param $iarray
    * @param string $priority
    */
   public static function log_array($iarray, $priority = '')
   {
      if ( is_null($priority) || empty($priority) ) $priority = JLog::INFO;
      $category = 'com_issuetracker';
      JLog::addLogger(array('logger' => 'database', 'db_type' => 'mysql', 'db_table' => '#__it_issues_log'), JLog::ALL, $category );

      if ( count($iarray) != 0 )
         JLog::add(json_encode($iarray), $priority, $category);
   }

   /*
    * Just add an entry to the existing log
    *
    */
   /**
    * @param $comment
    * @param string $priority
    */
   public static function add_logmsg($comment, $priority = '')
   {
      if ( is_null($priority) || empty($priority) ) $priority = JLog::INFO;
      $category = 'com_issuetracker';
      JLog::addLogger(array('logger' => 'database', 'db_type' => 'mysql', 'db_table' => '#__it_issues_log'), JLog::ALL, $category );

      if ( ! empty($comment) )
         JLog::add($comment, $priority, $category);
   }
}
