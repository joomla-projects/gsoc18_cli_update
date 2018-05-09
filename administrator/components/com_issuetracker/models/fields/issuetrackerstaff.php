<?php
/*
 *
 * @Version       $Id: issuetrackerstaff.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.2.3
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
 * Class JFormFieldIssueTrackerStaff
 */
class JFormFieldIssueTrackerStaff extends JFormField
{
   protected $type      = 'IssueTrackerStaff';

   /**
    * @return mixed
    */
   protected function getInput()
   {
      $db = JFactory::getDBO();

       // build the list of staff members who are registered
      $query = 'SELECT a.person_name AS text, a.user_id AS value'
      . ' FROM #__it_people AS a'
      . ' WHERE a.staff = 1'
      . ' AND   a.user_id IS NOT NULL'
      . ' ORDER BY a.ordering';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      array_unshift($data, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PERSON').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $data,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);

   }
}