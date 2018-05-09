<?php
/*
 * Issue Tracker Model for Issue Tracker Component
 *
 * @Version       $Id: itprojects.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JLoader::import( 'joomla.application.component.model' );

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItprojects extends JModelLegacy{

   /**
    * Itprojects data array for tmp store
    *
    * @var array
    */
   private $_data;

   /**
    * Gets the data
    * @return mixed The data to be displayed to the user
    */
   public function getData(){
      if (empty( $this->_data )){
         $id = JFactory::getApplication()->input->get('id',0);
         $db = JFactory::getDBO();
         $query = "SELECT * FROM `#__it_projects` where `id` = {$id}";

         // Filter by access level.
         $user = JFactory::getUser();
         $groups  = implode(',', $user->getAuthorisedViewLevels());
         $query .= ' AND access IN ('.$groups.')';

         $db->setQuery( $query );
         $this->_data = $db->loadObject();
      }
      $this->_data = IssueTrackerHelper::updatepname($this->_data);

      $registry = new JRegistry;
      $registry->loadString($this->_data->metadata);
      $this->_data->metadata = $registry;

      return $this->_data;
   }
}
