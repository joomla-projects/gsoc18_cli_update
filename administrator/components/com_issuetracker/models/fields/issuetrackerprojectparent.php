<?php
/*
 *
 * @Version       $Id: issuetrackerprojectparent.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.2
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
 * Class JFormFieldIssueTrackerProjectParent
 */
class JFormFieldIssueTrackerProjectParent extends JFormField
{
   protected $type      = 'IssueTrackerProjectParent';

   /**
    * @return mixed
    */
   protected function getInput()
   {
      $tree = array();
      // $pid = JRequest::getVar('id',0);
      $pid = JFactory::getApplication()->input->get('id',0);
      if ($pid == 0 ) {
         $catID = -1;
      } else {
         $catID = $pid;
      }

      $tree = IssueTrackerHelper::get_filtered_Project_name($catID);

      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}
