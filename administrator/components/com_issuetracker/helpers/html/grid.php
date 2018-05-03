<?php
/*
 * @Version       $Id: grid.php 1945 2015-03-03 11:09:50Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.7
 * @Copyright     Copyright (C) 2011 - 2014 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-03-03 11:09:50 +0000 (Tue, 03 Mar 2015) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

// Moved again in J3.1.4 (or there abouts.)
$jversion = new JVersion();
if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
   $filename = JPATH_ROOT . DS . 'libraries' . DS . 'cms' . DS . 'html' . DS . 'jgrid.php';
   $filenamea = JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'jgrid.php';

   if (file_exists($filename)) {
      include_once($filename);
   } else {
      include_once($filenamea);
   }
} else {
   include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'html' . DS . 'jgrid.php');
   JLoader::import('joomla.html.html.jgrid');
}

/**
 * Class IssuetrackerGrid
 */
class IssuetrackerGrid extends JHtmlJGrid
{

   /**
    * @param $value
    * @param $i
    * @param string $prefix
    * @param bool $enabled
    * @param string $checkbox
    * @return string
    */
   public static function isadmin($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled',  $options) ? $options['enabled']  : $enabled;
         $checkbox   = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',   $options) ? $options['prefix']   : '';
      }
      $states  = array(
         1  => array('notadministrator', 'COM_ISSUETRACKER_ADMIN',     'COM_ISSUETRACKER_NOT_ADMIN_ITEM', 'COM_ISSUETRACKER_ADMIN',     false, 'publish',   'publish'),
         0  => array('administrator',    'COM_ISSUETRACKER_NOT_ADMIN', 'COM_ISSUETRACKER_ADMIN_ITEM',     'COM_ISSUETRACKER_NOT_ADMIN', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }

   /**
    * @param $value
    * @param $i
    * @param string $prefix
    * @param bool $enabled
    * @param string $checkbox
    * @return string
    */
   public static function msgnotify($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled', $options) ? $options['enabled']     : $enabled;
         $checkbox   = array_key_exists('checkbox',   $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',  $options) ? $options['prefix']      : '';
      }
      $states  = array(
         1  => array('nonotify', 'COM_ISSUETRACKER_NOTIFY',    'COM_ISSUETRACKER_NO_NOTIFY_ITEM', 'COM_ISSUETRACKER_NOTIFY',    false, 'publish',   'publish'),
         0  => array('notify',   'COM_ISSUETRACKER_NO_NOTIFY', 'COM_ISSUETRACKER_NOTIFY_ITEM',    'COM_ISSUETRACKER_NO_NOTIFY', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }


   /**
    * @param $value
    * @param $i
    * @param string $prefix
    * @param bool $enabled
    * @param string $checkbox
    * @return string
    */
   public static function smsnotify($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled', $options) ? $options['enabled']     : $enabled;
         $checkbox   = array_key_exists('checkbox',   $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',  $options) ? $options['prefix']      : '';
      }
      $states  = array(
         1  => array('nosmsnotify', 'COM_ISSUETRACKER_SMSNOTIFY',    'COM_ISSUETRACKER_NO_SMSNOTIFY_ITEM', 'COM_ISSUETRACKER_SMSNOTIFY',    false, 'publish',   'publish'),
         0  => array('smsnotify',   'COM_ISSUETRACKER_NO_SMSNOTIFY', 'COM_ISSUETRACKER_SMSNOTIFY_ITEM',    'COM_ISSUETRACKER_NO_SMSNOTIFY', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }

   /**
    * @param $value
    * @param $i
    * @param string $prefix
    * @param bool $enabled
    * @param string $checkbox
    * @return string
    */
   public static function staff($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled', $options) ? $options['enabled']     : $enabled;
         $checkbox   = array_key_exists('checkbox',   $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',  $options) ? $options['prefix']      : '';
      }
      $states  = array(
         1  => array('notstaff', 'COM_ISSUETRACKER_ISSUES_STAFF',    'COM_ISSUETRACKER_NOT_STAFF_ITEM', 'COM_ISSUETRACKER_ISSUES_STAFF',     false, 'publish',   'publish'),
         0  => array('staff',   'COM_ISSUETRACKER_ISSUES_NOT_STAFF', 'COM_ISSUETRACKER_STAFF_ITEM',     'COM_ISSUETRACKER_ISSUES_NOT_STAFF', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }

   /**
    * @param $value
    * @param $i
    * @param string $prefix
    * @param bool $enabled
    * @param string $checkbox
    * @return string
    */
   public static function applied($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled',  $options) ? $options['enabled']  : $enabled;
         $checkbox   = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',   $options) ? $options['prefix']   : '';
      }
      $states  = array(
         1  => array('disabletrig', 'COM_ISSUETRACKER_ENABLE',   'COM_ISSUETRACKER_DISABLE', 'COM_ISSUETRACKER_ENABLE',  false, 'publish',   'publish'),
         0  => array('enabletrig',  'COM_ISSUETRACKER_DISABLE', 'COM_ISSUETRACKER_ENABLE',  'COM_ISSUETRACKER_DISABLE', false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }

   /**
    * @param $value
    * @param $i
    * @param string $prefix
    * @param bool $enabled
    * @param string $checkbox
    * @return string
    */
   public static function privacy($value, $i, $prefix = '', $enabled = true, $checkbox='cb')
   {
      if (is_array($prefix)) {
         $options    = $prefix;
         $enabled    = array_key_exists('enabled',  $options) ? $options['enabled']  : $enabled;
         $checkbox   = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
         $prefix     = array_key_exists('prefix',   $options) ? $options['prefix']   : '';
      }
      $states  = array(
         1  => array('isprivate', 'COM_ISUETRACKER_PRIVATE', 'COM_ISSUETRACKER_PUBLIC',  'COM_ISSUETRACKER_PRIVATE', false, 'publish',   'publish'),
         0  => array('ispublic',  'COM_ISSUETRACKER_PUBLIC', 'COM_ISSUETRACKER_PRIVATE', 'COM_ISSUETRACKER_PUBLIC',  false, 'unpublish', 'unpublish')
      );
      return self::state($states, $value, $i, $prefix, $enabled, true, $checkbox);
   }
}