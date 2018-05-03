<?php
/*
 *
 * @Version       $Id: route.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die;

JLoader::import('joomla.application.component.helper');
JLoader::import('joomla.application.categories');

/**
 * Content Component Route Helper
 *
 * @static
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 * @since 1.5
 */
abstract class IssueTrackerHelperRoute
{
   protected static $lookup;

   /**
    * @param   integer $id  The route of the issue item
    * @return string
    */
   public static function getIssueRoute($id)
   {
      /*
      $needles = array(
         'itissue'  => array((int) $id)
      );
      */
      //Create the link

      if (empty($id)) {
         $link = 'index.php?option=com_issuetracker&view=itissueslist';
      } else {
         $link = 'index.php?option=com_issuetracker&view=itissues&id='. $id;
      }
/*
      if ($item = self::_findItem($needles)) {
         $link .= '&Itemid='.$item;
      }
      elseif ($item = self::_findItem()) {
         $link .= '&Itemid='.$item;
      }
*/
      return $link;
   }

   /**
    * @param $id
    * @return string
    */
   public static function getFormRoute($id)
   {
      //Create the link
      if ($id) {
         $link = 'index.php?option=com_issuetracker&task=itissues.edit&a_id='. $id;
      } else {
         $link = 'index.php?option=com_issuetracker&task=itissues.edit&a_id=0';
      }

      return $link;
   }

   /**
    * @param null $needles
    * @return null
    */
   protected static function _findItem($needles = null)
   {
      $app     = JFactory::getApplication();
      $menus      = $app->getMenu('site');

      // Prepare the reverse lookup array.
      if (self::$lookup === null)
      {
         self::$lookup = array();

         $component  = JComponentHelper::getComponent('com_issuetracker');
         $items      = $menus->getItems('component_id', $component->id);
         foreach ($items as $item)
         {
            if (isset($item->query) && isset($item->query['view']))
            {
               $view = $item->query['view'];
               if (!isset(self::$lookup[$view])) {
                  self::$lookup[$view] = array();
               }
               if (isset($item->query['id'])) {
                  self::$lookup[$view][$item->query['id']] = $item->id;
               }
            }
         }
      }

      if ($needles)
      {
         foreach ($needles as $view => $ids)
         {
            if (isset(self::$lookup[$view]))
            {
               foreach($ids as $id)
               {
                  if (isset(self::$lookup[$view][(int)$id])) {
                     return self::$lookup[$view][(int)$id];
                  }
               }
            }
         }
      }
      else
      {
         $active = $menus->getActive();
         if ($active && $active->component == 'com_issuetracker') {
            return $active->id;
         }
      }

      return null;
   }
}
