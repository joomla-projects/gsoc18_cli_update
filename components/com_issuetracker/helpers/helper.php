<?php
/*
 *
 * @Version       $Id: helper.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.7
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.model');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
/**
 * Class IssueTrackerHelperSite
 */
class IssueTrackerHelperSite
{
   /**
    * Retrieve JTable objects.
    *
    * @param   string   $tableName  The table name.
    * @param   string   $prefix     JTable prefix.
    * @return  object   JTable object.
    **/
   public static function getTable( $tableName , $prefix = 'IssueTrackerTable' )
   {
      JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_issuetracker' . DS . 'tables' );

      $tbl    = JTable::getInstance( $tableName , $prefix );
      return $tbl;
   }

   /**
    * @param int $sw
    * @return mixed
    */
   public static function getProject_name($sw = 0)
   {
      // Get user to permit filtering by access level.
      $user = JFactory::getUser();
      $groups  = implode(',', $user->getAuthorisedViewLevels());

      $db = JFactory::getDBO();

      //build the list of categories
      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid';
      $query .= ' FROM #__it_projects AS a';
      $query .= ' WHERE a.state = 1';
      if ($sw == 1)
         $query .= " OR a.title = 'Unspecified Project' ";
      $query .= ' AND a.access IN ('.$groups.')';
      $query .= ' ORDER BY a.ordering';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;

      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));

      return $tree;
   }

   /**
    * @param int $inchead
    * @return array
    */
   public static function getProjects( $inchead = 0)
   {

      // Get user to permit filtering by access level.
      $user = JFactory::getUser();
      $groups  = implode(',', $user->getAuthorisedViewLevels());

      $db = JFactory::getDBO();
      $query = 'SELECT `title` AS text, `id` AS value, `parent_id` as parentid FROM `#__it_projects` WHERE state = 1 ';
      $query .= ' AND access IN ('.$groups.')';
      $query .= ' ORDER BY lft';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;

      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

      // Filter the projects based on the selected items in the parameters.
      // Done here since we would have had to add in the parent projects in to get the full name determined.
      $params     = JFactory::getApplication()->getParams();
      // $params  = $app->getParams();
      $projids = $params->get('project_ids', array());  // It is an array even if there is only one element!
      $out = array();

      // Special case if 'All' was selected.
      if ( in_array('0', $projids, true) ) {
         // Fetch all projids directly from db.
         $query = 'SELECT id FROM `#__it_projects` WHERE state = 1 ';
         $query .= ' AND access IN ('.$groups.')';
         $query .= ' ORDER BY lft';
         $db->setQuery( $query );
         $jversion = new JVersion();
         if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
            $projids = $db->loadColumn();
         } else {
            $projids = $db->loadResultArray();
         }
      }

      if ( ! empty($projids) && $projids[0] != "" ) {
         // Check if we have these ids in our $tree
         foreach ($projids as $key ) {
            foreach ($tree as $key2) {
               if ($key == $key2->value ) {
                  $out[] = $key2;
                  break;    // Exit inner foreach since we have found our match.
               }
            }
         }
      }

      if ( $inchead == 0 ) {
         array_unshift($out, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));
      }

      return $out;
   }

   /**
    * @return array
    */
   public static function getTypes()
   {
      $db = JFactory::getDBO();
      $db->setQuery( 'SELECT `id` AS value, `type_name` AS text FROM `#__it_types` ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      $options[] = JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_TYPE').' -' );

      foreach( $db->loadObjectList() as $r){
         $options[] = JHTML::_('select.option',  $r->value, $r->text );
      }
      return $options;
   }

   /**
    * @param $id
    * @return mixed
    */
   public static function getUserdefproj($id)
   {
      $db   = JFactory::getDBO();
      $sql = "SELECT assigned_project FROM ".$db->quoteName('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $projid = $db->loadResult();

      return $projid;
   }

   /**
    * @param $id
    * @return mixed
    */
   public static function isIssueAdmin($id)
   {
      $db   = JFactory::getDBO();
      $sql = "SELECT issues_admin FROM ".$db->quoteName('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $isadmin = $db->loadResult();

      return $isadmin;
   }
   /**
    *
    * Method to determine whether the user is an member of Staff
    *
    * @param $id
    * @return mixed
    */
   public static function isIssueStaff($id)
   {
      $db   = JFactory::getDBO();
      $sql  = "SELECT staff FROM ".$db->quoteName('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $isstaff = $db->loadResult();

      return $isstaff;
   }

   /**
    *
    * Method to get the id for the person in teh it_people table which is used by the issue identified_by_person_id field
    * given the users id.
    *
    * @param $id
    * @return mixed
    */
   public static function getitPersonid($id)
   {
      $db   = JFactory::getDBO();
      $sql = "SELECT id FROM ".$db->quoteName('#__it_people')." WHERE user_id=" . $db->Quote($id);

      $db->setQuery( $sql);
      $psnid = $db->loadResult();

      return $psnid;
   }

   /**
    * Returns the number of published projects possible in the front end All was specified.
    *
    * @return mixed
    */
   public static function noprojectstodisplay()
   {
      // Get user to permit filtering by access level.
      $user = JFactory::getUser();
      $groups  = implode(',', $user->getAuthorisedViewLevels());

      $db = JFactory::getDBO();

      $query = 'SELECT count(*) FROM #__it_projects AS a';
      $query .= ' WHERE a.state = 1';
      $query .= ' AND a.access IN ('.$groups.')';
      $db->setQuery( $query );
      $cnt = $db->loadResult();

      return $cnt;
   }
}