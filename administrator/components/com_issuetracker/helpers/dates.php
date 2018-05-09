<?php
/*
 *
 * @Version       $Id: dates.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.3
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');

/**
 * Class IssueTrackerDateHelper
 */
class IssueTrackerHelperDate
{
   /**
    * Return the date with the correct specified timezone offset.
    * If a user timezone is specified use that otherwsie use the server timezone.
    *
    * param : raw date string (date with no offset yet)
    * return : JDate object
    * @param string $str
    * @return \JDate
    */
   public static function dateWithOffSet($str='')
   {
      $userTZ  = self::getOffSet();
      $date    = new JDate( $str );

      $user    = JFactory::getUser();
      if($user->id != 0) {
            $userTZ  = $user->getParam('timezone');
      }

      if (empty($userTZ)) {
         $jversion = new JVersion();
         if ( version_compare( $jversion->getShortVersion(), '3.2', 'ge' ) ) {
            $userTZ = JFactory::getApplication()->get('offset');
         } else {
            $config  = JFactory::getConfig();
            $userTZ  = $config->get('offset');
         }
      }

      $tmp = new DateTimeZone( $userTZ );
      $date->setTimeZone( $tmp );

      return $date;
   }

   /**
    * Returns a date with timezone off set.  If an input value is supplied it is assumed to be a UTC date.
    *
    * @param string $str  Input UTC date or null.
    * @return mixed
    */
   public static function getDate($str='')
   {
      return self::dateWithOffSet($str);
   }

   /**
    * Gets the raw Unix timestamp.
    *
    * @param string $str
    * @return int
    */
   function geRawUnixTimeOld($str='')
   {
      $tzoffset   = self::getOffSet();
      $date       = self::dateWithOffSet( $str );

      $newdate = mktime( ($date->toFormat('%H')  - $tzoffset),
                     $date->toFormat('%M'),
                     $date->toFormat('%S'),
                     $date->toFormat('%m'),
                     $date->toFormat('%d'),
                     $date->toFormat('%Y'));
      return $newdate;
   }

   /**
    * Gets the offset.
    *
    * @param bool $numberOnly
    * @return float|mixed
    */
   public static function getOffSet16($numberOnly = false)
   {
      jimport('joomla.form.formfield');

      $user    = JFactory::getUser();
      if($user->id != 0) {
         $userTZ  = $user->getParam('timezone');
      }

      if(empty($userTZ)) {
         $jversion = new JVersion();
         if ( version_compare( $jversion->getShortVersion(), '3.2', 'ge' ) ) {
            $userTZ = JFactory::getApplication()->get('offset');
         } else {
            $config  = JFactory::getConfig();
            $userTZ  = $config->get('offset');
         }
      }

      if( $numberOnly ) {
         $newTZ      = new DateTimeZone($userTZ);
         $dateTime   = new DateTime( "now" , $newTZ );

         $offset     = $newTZ->getOffset( $dateTime ) / 60 / 60;
         return $offset;
      } else {
         return $userTZ;
      }
   }

   /**
    * @param bool $numberOnly
    * @return float|mixed
    */
   public static function getOffSet( $numberOnly   = false )
   {
      // return timezone object
      return self::getOffSet16($numberOnly);
   }

   /**
    * Gets the lapsed period between a specified time and now.
    *
    * @param $time
    * @return string
    */
   function getLapsedTime( $time )
   {
      $now  = self::getDate();
      $end  = self::getDate( $time );
      $time = $now->toUnix() - $end->toUnix();

      $tokens = array (
                     31536000    => 'COM_ISSUETRACKER_X_YEAR',
                     2592000     => 'COM_ISSUETRACKER_X_MONTH',
                     604800      => 'COM_ISSUETRACKER_X_WEEK',
                     86400       => 'COM_ISSUETRACKER_X_DAY',
                     3600        => 'COM_ISSUETRACKER_X_HOUR',
                     60          => 'COM_ISSUETRACKER_X_MINUTE',
                     1           => 'COM_ISSUETRACKER_X_SECOND'
                  );

      foreach( $tokens as $unit => $key ) {
         if ($time < $unit) {
            continue;
         }

         $units   = floor( $time / $unit );

         $string = $units > 1 ?  $key . 'S' : $key;
         $string = $string . '_AGO';

         $text   = JText::sprintf(strtoupper($string), $units);
         return $text;
      }
      // Should never get here.
      return "Problem determining Lapsed Time";
   }

   /**
    * Formatting routine for the date.
    *
    * @param $jdate
    * @param string $format
    * @return string
    */
   public static function toFormat($jdate, $format='%Y-%m-%d %H:%M:%S')
   {
      if(is_null($jdate)) {
         $jdate  = new JDate();
      }

      if( JString::stristr( $format, '%' ) !== false ) {
         $format = self::strftimeToDate( $format );
      }

      return $jdate->format( $format , true );

   }

   /**
    * Converts string format to a date.
    *
    * @param $format
    * @return mixed
    */
   public static function strftimeToDate( $format )
   {
      $strftimeMap = array(
         // day
         '%a' => 'D', // 00, Sun through Sat
         '%A' => 'l', // 01, Sunday through Saturday
         '%d' => 'd', // 02, 01 through 31
         '%e' => 'j', // 03, 1 through 31
         '%j' => 'z', // 04, 001 through 366
         '%u' => 'N', // 05, 1 for Monday through 7 for Sunday
         '%w' => 'w', // 06, 1 for Sunday through 7 for Saturday

         // week
         '%U' => 'W', // 07, Week number of the year with Sunday as the start of the week
         '%V' => 'W', // 08, ISO-8601:1988 week number of the year with Monday as the start of the week, with at least 4 weekdays as the first week
         '%W' => 'W', // 09, Week number of the year with Monday as the start of the week

         // month
         '%b' => 'M', // 10, Jan through Dec
         '%B' => 'F', // 11, January through December
         '%h' => 'M', // 12, Jan through Dec, alias of %b
         '%m' => 'm', // 13, 01 for January through 12 for December

         // year
         '%C' => '', // 14, 2 digit of the century, year divided by 100, truncated to an integer, 19 for 20th Century
         '%g' => 'y', // 15, 2 digit of the year going by ISO-8601:1988 (%V), 09 for 2009
         '%G' => 'o', // 16, 4 digit version of %g
         '%y' => 'y', // 17, 2 digit of the year
         '%Y' => 'Y', // 18, 4 digit version of %y

         // time
         '%H' => 'H', // 19, hour, 00 through 23
         '%I' => 'h', // 20, hour, 01 through 12
         '%l' => 'g', // 21, hour, 1 through 12
         '%M' => 'i', // 22, minute, 00 through 59
         '%p' => 'A', // 23, AM or PM
         '%P' => 'a', // 24, am or pm
         '%r' => 'h:i:s A', // 25, = %I:%M:%S %p, 09:34:17 PM
         '%R' => 'H:i', // 26, = %H:%M, 21:34
         '%S' => 's', // 27, second, 00 through 59
         '%T' => 'H:i:s', // 28, = %H:%M:%S, 21:34:17
         '%X' => 'H:i:s', // 29, Based on locale without date
         '%z' => 'O', // 30, Either the time zone offset from UTC or the abbreviation (depends on operating system)
         '%Z' => 'T', // 31, The time zone offset/abbreviation option NOT given by %z (depends on operating system)

         // date stamps
         '%c' => 'Y-m-d H:i:s', // 32, Date and time stamps based on locale
         '%D' => 'm/d/y', // 33, = %m/%d/%y, 02/05/09
         '%F' => 'Y-m-d', // 34, = %Y-%m-%d, 2009-02-05
         '%s' => '', // 35, Unix timestamp, same as time()
         '%x' => 'Y-m-d', // 36, Date stamps based on locale

         // misc
         '%n' => '\n', // 37, New line character \n
         '%t' => '\t', // 38, Tab character \t
         '%%' => '%'  // 39, Literal percentage character %
      );

      $dateMap = array(
         // day
         'd', // 01, 01 through 31
         'D', // 02, Mon through Sun
         'j', // 03, 1 through 31
         'l', // 04, Sunday through Saturday
         'N', // 05, 1 for Monday through 7 for Sunday
         'S', // 06, English ordinal suffix, st, nd, rd or th
         'w', // 07, 0 for Sunday through 6 for Saturday
         'z', // 08, 0 through 365

         // week
         'W', // 09, ISO-8601 week number of the year with Monday as the start of the week

         // month
         'F', // 10, January through December
         'm', // 11, 01 through 12
         'M', // 12, Jan through Dec
         'n', // 13, 1 through 12
         't', // 14, Number of days in the month, 28 through 31

         // year
         'L', // 15, 1 for leap year, 0 otherwise
         'o', // 16, 4 digit of the ISO-8601 year number. This has the same value as Y, except that it follows ISO week number (W)
         'Y', // 17, 4 digit of the year
         'y', // 18, 2 digit of the year

         // time
         'a', // 19, am or pm
         'A', // 20, AM or PM
         'B', // 21, Swatch Internet time 000 through 999
         'g', // 22, hour, 1 through 12
         'G', // 23, hour, 0 through 23
         'h', // 24, hour, 01 through 12
         'H', // 25, hour, 00 through 23
         'i', // 26, minute, 00 through 59
         's', // 27, second, 00 through 59
         'u', // 28, microsecond, date() always generate 000000

         // timezone
         'e', // 29, timezone identifier, UTC, GMT
         'I', // 30, 1 for Daylight Saving Time, 0 otherwise
         'O', // 31, +0200
         'P', // 32, +02:00
         'T', // 33, timezone abbreviation, EST, MDT
         'Z', // 34, Timezone offset in seconds, -43200 through 50400

         // full date/time
         'c', // 35, ISO-8601 date, 2004-02-12T15:19:21+00:00
         'r', // 36, RFC 2822 date, Thu, 21 Dec 2000 16:01:07 +0200
         'U'  // 37, Seconds since the Unix Epoch
      );

      foreach( $strftimeMap as $key => $value ) {
         $format = str_replace( $key, $value, $format );
      }

      return $format;
   }

   /**
    * Converts a given date from a given timezone to UTC.
    *
    * @param $jdate
    * @param string $tz
    * @return string
    */
   public static function datetoUTC($jdate, $tz=null)
   {
      if ( $tz == 'UTC') return $jdate;     // Already in UTC!

      if ( empty($tz) ) {
         $user    = JFactory::getUser();
         if($user->id != 0) {
            $tz  = $user->getParam('timezone');
         }

         if (empty($tz)) {
            $jversion = new JVersion();
            if ( version_compare( $jversion->getShortVersion(), '3.2', 'ge' ) ) {
               $tz = JFactory::getApplication()->get('offset');
            } else {
               $config  = JFactory::getConfig();
               $tz  = $config->get('offset');
           }
        }
      }

      if (is_null($jdate)) {
         $d1  = new JDate();
      } else {
         if ( $tz != 'UTC') {
            $d1 = new JDate($jdate, $tz);
         } else {
            $d1 = new JDate($jdate);
         }
      }

      return $d1->format('Y-m-d H:i:s', false, false);
   }
}
