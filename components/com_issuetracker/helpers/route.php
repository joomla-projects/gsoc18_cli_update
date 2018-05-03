<?php
/**
 *
 * @Version       $Id: route.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted Access');

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
    * @param   int   $id
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
     * @param $projid
     * @param int $language
     * @return string
     * @internal param \The $int route of the project item
     */
   public static function getProjectRoute($projid, $language = 0)
   {
      $id = (int) $projid;

      if ($id < 1) {
         $link = '';
      } else {
         $link = 'index.php?option=com_issuetracker&view=itprojects&id='.$id;

         if ($language && $language != "*" && JLanguageMultilang::isEnabled()) {
            $db      = JFactory::getDBO();
            $query   = $db->getQuery(true);
            $query->select('a.sef AS sef');
            $query->select('a.lang_code AS lang_code');
            $query->from('#__languages AS a');

            $db->setQuery($query);
            $langs = $db->loadObjectList();
            foreach ($langs as $lang) {
               if ($language == $lang->lang_code) {
                  $link .= '&lang='.$lang->sef;
//                  $needles['language'] = $language;
               }
            }
         }
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
      $menus   = $app->getMenu('site');

      // Prepare the reverse lookup array.
      if (self::$lookup === null)
      {
         self::$lookup = array();

         $component  = JComponentHelper::getComponent('com_issuetracker');
         $items      = $menus->getItems('component_id', $component->id);
         foreach ($items as $item) {
            if (isset($item->query) && isset($item->query['view'])) {
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

      if ($needles) {
         foreach ($needles as $view => $ids) {
            if (isset(self::$lookup[$view])) {
               foreach($ids as $id) {
                  if (isset(self::$lookup[$view][(int)$id])) {
                     return self::$lookup[$view][(int)$id];
                  }
               }
            }
         }
      } else {
         $active = $menus->getActive();
         if ($active && $active->component == 'com_issuetracker') {
            return $active->id;
         }
      }

      return null;
   }

   /**
    * @param $id
    * @return mixed
    */
   public static function getIssuePermalink( $id )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
      $issue = IssueTrackerHelperSite::getTable( 'Itissues' , 'IssueTrackerTable' );
      $issue->load( $id );

      return $issue->alias;
   }

   /**
    * @param $id
    * @return mixed
    */
   public static function getPersonPermalink( $id )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
      $person = IssueTrackerHelperSite::getTable( 'Itpeople' , 'IssueTrackerTable' );
      $person->load( $id );

      return $person->alias;
   }

   /**
    * @param $id
    * @return mixed
    */
   public static function getProjectPermalink( $id )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );
      $project = IssueTrackerHelperSite::getTable( 'Itprojects' , 'IssueTrackerTable' );
      $project->load( $id );

      return $project->alias;
   }


   /**
    * @param string $view
    * @return mixed
    */
   public static function getItemId( $view='' )
   {
      static $items  = null;

      if( !isset( $items[ $view ] ) ) {
         $db   = JFactory::getDBO();

         switch($view) {
            case 'itissues':
               $view='itissues';
               break;
            case 'itpeople':
               $view='itpeople';
               break;
            case 'itpeoplelist':
               $view='itpeoplelist';
               break;
            case 'itprojects':
               $view='itprojects';
               break;
            case 'itprojectslist':
               $view='itprojectslist';
               break;
            case 'itissueslist':
            default:
               $view='itissueslist';
               break;
         }

         $query   = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName( '#__menu' ) . ' '
               . 'WHERE ' . $db->quoteName( 'link' ) . '=' . $db->Quote( 'index.php?option=com_issuetracker&view='.$view ) . ' '
               . 'AND ' . $db->quoteName( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
         $db->setQuery( $query );
         $itemid = $db->loadResult();


         // @rule: Try to fetch based on the current view.
         if( empty( $itemid ) ) {
            $query   = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName( '#__menu' ) . ' '
                  . 'WHERE ' . $db->quoteName( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_issuetracker&view=' . $view . '%' ) . ' '
                  . 'AND ' . $db->quoteName( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         if(empty($itemid)) {
            $query   = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName( '#__menu' ) . ' '
                  . 'WHERE ' . $db->quoteName( 'link' ) . '=' . $db->Quote( 'index.php?option=com_issuetracker&view=itissueslist' ) . ' '
                  . 'AND ' . $db->quoteName( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         //last try. get anything view that from issuetracker
         if(empty($itemid)) {
            $query   = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName( '#__menu' ) . ' '
                  . 'WHERE ' . $db->quoteName( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_issuetracker&view=%' ) . ' '
                  . 'AND ' . $db->quoteName( 'published' ) . '=' . $db->Quote( '1' ) . ' ORDER BY `id` LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         // if still failed the get any item id, then get the joomla default menu item id.
         if( empty($itemid) ) {
            $query   = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName( '#__menu' ) . ' '
                  . 'WHERE `home` = ' . $db->Quote( '1' ) . ' '
                  . 'AND ' . $db->quoteName( 'published' ) . '=' . $db->Quote( '1' ) . ' ORDER BY `id` LIMIT 1';
            $db->setQuery( $query );
            $itemid = $db->loadResult();
         }

         $items[ $view ]   = !empty($itemid)? $itemid : 1;
      }
      return $items[ $view ];
   }

   /**
    * Returns true if specific projects associated with the issues is part of the list displayed by the specified menu.
    *
    * @param $menuid
    * @param $id
    * @param $a_id
    * @return boolean
    */
   public static function checkIssue( $menuid, $id, $a_id )
   {
      if ( empty($id) || $id == 0 ) {
         $ccid = $a_id;
      } else {
         $ccid = $id;
      }
      jimport( 'joomla.html.parameter' );
      $app     = JFactory::getApplication();
      $menu    = $app->getMenu('site');

      $item    = $menu->getItem($menuid);
      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $params = $item->params;
      } else {
         $params  = new JRegistry($item->params);
      }

      $pids    = $params->get('project_ids', array(), 'array');

      // Get project for the issue
      $db      = JFactory::getDBO();
      $query   = 'SELECT ' . $db->quoteName('related_project_id') . ' FROM ' . $db->quoteName( '#__it_issues' )
               . ' WHERE `id` = ' . $db->Quote( $ccid );
      $db->setQuery( $query );
      $pid     = $db->loadResult();

      if (in_array( $pid, $pids)) return true;

      return false;
   }
}