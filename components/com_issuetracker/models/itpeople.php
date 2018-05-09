<?php
/*
 * Issue Tracker Model for Issue Tracker Component
 *
 * @Version       $Id: itpeople.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
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
class IssueTrackerModelItpeople extends JModelLegacy
{
   /**
    * Itpeople data array for tmp store
    *
    * @var array
    */
   private $_data;

    /**
     * Returns the query
     * @param $id
     * @return string The query to be used to retrieve the rows from the database
     */
   private function _buildQuery($id){
      // use alias t1 for easier JOINs writing
      // Create a new query object.
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);
      $query->select(
         $this->getState(
            'list.select',
            't1.*'
         )
      );

      $query->from('#__it_people AS t1');

      // Join over the it_projects table.
      $query->select('t2.title AS project_name, t2.id AS project_id');
      $query->join('LEFT', '#__it_projects AS t2 ON t2.id = t1.assigned_project');

      // Outer join over the it_roles table.
      $query->select('t3.role_name AS role_name, t3.id AS role_id');
      $query->join('LEFT OUTER', '#__it_roles AS t3 ON t3.id = t1.person_role');

      $query = $query . $this->_buildQueryWhere($id);
      return $query;
   }

   /**
    * @param $id
    * @return string
    */
   private function _buildQueryWhere($id) {
      // $app = JFactory::getApplication();

      $where = " WHERE t1.`id` = {$id} ";

      return $where;
   }


   /**
    * Retrieves the data
    * @return array Array of objects containing the data from the database
    */
   public function getData(){
      // Lets load the data if it doesn't already exist
      if (empty( $this->_data )) {
         $id = JFactory::getApplication()->input->get('id',0);
         $db = JFactory::getDBO();
         $query = $this->_buildQuery($id);
         $db->setQuery( $query );
         $this->_data = $db->loadObject();
      }
      $this->_data = IssueTrackerHelper::updatepname($this->_data);

      return $this->_data;
   }
}
