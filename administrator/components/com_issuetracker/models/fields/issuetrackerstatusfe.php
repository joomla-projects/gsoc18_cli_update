<?php
/*
 *
 * @Version       $Id: issuetrackerstatusfe.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
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
 * Class JFormFieldIssueTrackerStatusfe
 */
class JFormFieldIssueTrackerStatusfe extends JFormField
{
   protected $type      = 'IssueTrackerStatusfe';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();
      //build the list of status
      $query = 'SELECT a.status_name AS text, a.id AS value'
      . ' FROM #__it_status AS a'
      . ' WHERE a.state = 1'
      . ' ORDER BY a.status_name';
      $db->setQuery( $query );

      $tree = array();
      foreach( $db->loadObjectList() as $r){
         $tree[] = JHTML::_('select.option',  $r->value, $r->text );
      }

      array_unshift($tree, JHTML::_('select.option', '0', JText::_('JALL'), 'value', 'text'));
      array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_STATUS').' -', 'value', 'text'));
      return JHTML::_('select.genericlist',  $tree,  $this->name, 'class="inputbox" multiple="multiple"', 'value', 'text', $this->value);
   }
}