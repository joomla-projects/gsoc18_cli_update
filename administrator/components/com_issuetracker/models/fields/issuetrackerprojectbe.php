<?php
/*
 *
 * @Version       $Id: issuetrackerprojectbe.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
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
 * Class JFormFieldIssueTrackerProjectbe
 */
class JFormFieldIssueTrackerProjectbe extends JFormField
{
   protected $type      = 'IssueTrackerProject';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();

       //build the list of projects
      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
      . ' FROM #__it_projects AS a'
      . ' WHERE state in (0,1)'
      . ' ORDER BY a.lft';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;
      // $required   = ((string) $this->element['required'] == 'true') ? TRUE : FALSE;

      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

//      array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'OnChange="displayFormFields(this.form);" class="inputbox"', 'value', 'text', $this->value);
   }
}
