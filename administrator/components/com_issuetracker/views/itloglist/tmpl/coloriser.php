<?php
/*
 *
 * @Version       $Id: coloriser.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Class IssueTrackerLogColoriser
 */
class IssueTrackerLogColoriser
{
   /**
    * @param $file
    * @param bool $onlyLast
    * @return string
    */
   public static function colorise($file, $onlyLast = false)
   {
      $ret = '';

      $lines = @file($file);
      if(empty($lines)) return $ret;

      array_shift($lines);

      foreach($lines as $line) {
         $line = trim($line);
         if(empty($line)) continue;
         $type = substr($line,0,1);
         switch($type) {
            case '=':
               continue;
               break;

            case '+':
               $ret .= "\t".'<li class="tracker-changelog-added"><span></span>'.htmlentities(trim(substr($line,2)),ENT_QUOTES, "UTF-8")."</li>\n";
               break;

            case '-':
               $ret .= "\t".'<li class="tracker-changelog-removed"><span></span>'.htmlentities(trim(substr($line,2)),ENT_QUOTES, "UTF-8")."</li>\n";
               break;

            case '~':
               $ret .= "\t".'<li class="tracker-changelog-changed"><span></span>'.htmlentities(trim(substr($line,2)),ENT_QUOTES, "UTF-8")."</li>\n";
               break;

            case '!':
               $ret .= "\t".'<li class="tracker-changelog-important"><span></span>'.htmlentities(trim(substr($line,2)),ENT_QUOTES, "UTF-8")."</li>\n";
               break;

            case '#':
               $ret .= "\t".'<li class="tracker-changelog-fixed"><span></span>'.htmlentities(trim(substr($line,2)),ENT_QUOTES, "UTF-8")."</li>\n";
               break;

            default:
               if(!empty($ret)) {
                  $ret .= "</ul>";
                  if($onlyLast) return $ret;
               }
               if(!$onlyLast) $ret .= "<h3 class=\"tracker-changelog\">$line</h3>\n";
               $ret .= "<ul class=\"tracker-changelog\">\n";
               break;
         }
      }

      return $ret;
   }

   /**
    * @param $text
    * @return string
    */
   public static function colortext($text)
   {
      $ret = '<td></td>';

      if( is_null($text) || empty($text) ) return $ret;

      switch($text) {
         case 'Info':
            $ret = '<td class="tracker-log-info">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case 'Alert':
            $ret = '<td class="tracker-log-alert">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case 'Emergency':
            $ret = '<td class="tracker-log-emergency">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case 'Debug':
            $ret = '<td class="tracker-log-debug">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case 'Critical':
            $ret = '<td class="tracker-log-critical">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case 'Warning':
            $ret = '<td class="tracker-log-warning">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case 'Error':
            $ret = '<td class="tracker-log-error">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case 'Notice':
            $ret = '<td class="tracker-log-notice">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         default:
            $ret = '<td>'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;
      }
      return $ret;
   }
}