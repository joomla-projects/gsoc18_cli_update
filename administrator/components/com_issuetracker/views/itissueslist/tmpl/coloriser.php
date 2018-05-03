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
 * Class IssueTrackerPriorityColoriser
 */
class IssueTrackerPriorityColoriser
{
   /**
    * @param $text
    * @param $rank
    * @return string
    */
   public static function colortext($text, $rank)
   {
      $ret = '<td></td>';

      if( is_null($text) || empty($text) ) return $ret;

      switch($rank) {
         case '1':
         case '2':
         case '3':
         case '4':
         case '5':
            $ret = '<td class="issue-priority-1">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '6':
         case '7':
         case '8':
         case '9':
         case '10':
            $ret = '<td class="issue-priority-2">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '11':
         case '12':
         case '13':
         case '14':
         case '15':
            $ret = '<td class="issue-priority-3">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '16':
         case '17':
         case '18':
         case '19':
         case '20':
            $ret = '<td class="issue-priority-4">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '21':
         case '22':
         case '23':
         case '24':
         case '25':
            $ret = '<td class="issue-priority-5">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '26':
         case '27':
         case '28':
         case '29':
         case '30':
            $ret = '<td class="issue-priority-6">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '31':
         case '32':
         case '33':
         case '34':
         case '35':
            $ret = '<td class="issue-priority-7">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '36':
         case '37':
         case '38':
         case '39':
         case '40':
            $ret = '<td class="issue-priority-8">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '41':
         case '42':
         case '43':
         case '44':
         case '45':
            $ret = '<td class="issue-priority-9">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '46':
         case '47':
         case '48':
         case '49':
         case '50':
            $ret = '<td class="issue-priority-10">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '51':
         case '52':
         case '53':
         case '54':
         case '55':
            $ret = '<td class="issue-priority-11">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '56':
         case '57':
         case '58':
         case '59':
         case '60':
            $ret = '<td class="issue-priority-12">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '61':
         case '62':
         case '63':
         case '64':
         case '65':
            $ret = '<td class="issue-priority-13">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '66':
         case '67':
         case '68':
         case '69':
         case '70':
            $ret = '<td class="issue-priority-14">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '71':
         case '72':
         case '73':
         case '74':
         case '75':
            $ret = '<td class="issue-priority-15">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '76':
         case '77':
         case '78':
         case '79':
         case '80':
            $ret = '<td class="issue-priority-16">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '81':
         case '82':
         case '83':
         case '84':
         case '85':
            $ret = '<td class="issue-priority-17">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '86':
         case '87':
         case '88':
         case '89':
         case '90':
            $ret = '<td class="issue-priority-18">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '91':
         case '92':
         case '93':
         case '94':
         case '95':
            $ret = '<td class="issue-priority-19">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         case '96':
         case '97':
         case '98':
         case '99':
         case '100':
            $ret = '<td class="issue-priority-20">'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;

         default:
            $ret = '<td>'.htmlentities($text,ENT_QUOTES, "UTF-8").'</td>';
            break;
      }
      return $ret;
   }
}