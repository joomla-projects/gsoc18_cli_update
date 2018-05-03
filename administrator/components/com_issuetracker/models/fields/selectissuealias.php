<?php
/*
 *
 * @Version       $Id: selectissuealias.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
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

/**
 * Class JFormField
 */
class JFormFieldSelectissuealias extends JFormField
{
   protected $type      = 'Selectissuealias';

   /**
    * @return mixed
    */
   protected function getInput() {

      $db = JFactory::getDBO();

      $groups = array();

      $groups[] = JHTML::_('select.option', 0, JText::_('COM_ISSUETRACKER_SELECT'));

      // Build the list of issue aliases
      $query = "SELECT alias AS value, CONCAT_WS(' - ', alias, issue_summary) AS text FROM `#__it_issues` ";
      $query .= " ORDER BY `alias`";
      $db->setQuery( $query );

      foreach( $db->loadObjectList() as $r){
         $groups[] = JHTML::_('select.option',  $r->value, $r->text );
      }

      return JHTML::_('select.genericlist',  $groups,  $this->name, 'class="inputbox"', 'value', 'text', $this->value);
   }
}