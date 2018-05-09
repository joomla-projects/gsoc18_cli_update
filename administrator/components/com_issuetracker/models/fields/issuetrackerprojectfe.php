<?php
/*
 *
 * @Version       $Id: issuetrackerprojectfe.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Class JFormFieldIssueTrackerProjectfe
 */
class JFormFieldIssueTrackerProjectfe extends JFormField
{
   protected $type   = 'IssueTrackerProjectfe';

   /**
    * @return mixed
    */
   protected function getInput()
   {

      $user = JFactory::getUser();
      if ( IssueTrackerHelper::isIssueAdmin($user->id) || IssueTrackerHelper::isIssueStaff($user->id) ) {
         $isadmin = 1;
      } else {
         $isadmin = 0;
      }

      if ( $isadmin == 0 ) {
         // Get the Menu parameters to determine which projects have been selected.
         // Unless we are a Issue Administrator since we may be editing the issue.
         $minput = JFactory::getApplication()->input;
         $menuitemid = $minput->getInt( 'Itemid' );  // this returns the menu id number so we can reference parameters
         // $menu = JSite::getMenu();
         $menu = JFactory::getApplication()->getMenu();
         if ($menuitemid) {
            $menuparams = $menu->getParams( $menuitemid );
            $projects = $menuparams->get('projects');
         }
      }

      $db = JFactory::getDBO();

       // Build the list of projects.  Cannot filter in the query since we need to expand out the full project name.
       // Do not get root node.
      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid';
      $query .= ' FROM #__it_projects AS a';
      if ( $isadmin == 0 ) {
         $query .= ' WHERE a.state = 1 ';
      } else {
         $query .= ' WHERE a.state in (0,1) ';
      }

      $groups  = implode(',', $user->getAuthorisedViewLevels());
      $query  .= ' AND a.access IN ('.$groups.')';

      $query .= ' ORDER BY a.lft';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;
      // $required   = ((string) $this->element['required'] == 'true') ? TRUE : FALSE;

      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

      // Now filter out the rows we do not want.
      if ( $isadmin == 0 && ! empty($projects) && $projects[0] != 0 )
         $tree = $this->array_keep($tree, $projects);

      if (count($tree) > 1)
         array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'OnChange="getProjectTypes(this.form);displayFormFields(this.form);" class="inputbox"', 'value', 'text', $this->value);
   }

   /*
    * Function to filter the project tree retaining only the projects we desire.
    */
   /**
    * @param $array
    * @param $projects
    * @return array
    */
   function array_keep($array, $projects)
   {
      if ( empty($projects) || $projects[0] == 0 ) return $array;

      $thisarray = array ();
      foreach($array as $key) {
      $k = $key->value;
      foreach ( $projects as $item)
         if ( $k == $item)
            $thisarray[] = $key;
      }
      return $thisarray;
   }
}
