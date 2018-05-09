<?php
/*
 *
 * @Version       $Id: itprojectslist.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JLoader::import( 'joomla.application.component.modellist' );

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItprojectslist extends JModelList{

   /**
    * Itprojectslist data array for tmp store
    *
    * @var array
    */
   private $_data;

   /**
   * Pagination object
   * @var object
   */
   private $_pagination = null;

   /**
    * Constructor
    *
    */
     function __construct()
   {
      parent::__construct();
   }

   /**
    * @param null $ordering
    * @param null $direction
    */
   protected function populateState($ordering = null, $direction = null)
   {
      // Initialise variables.
      $app = JFactory::getApplication();
      // $session = JFactory::getSession();

      // Get pagination request variables
      // $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
      $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
      $limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');

      // In case limit has been changed, adjust it
      $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

      $this->setState('limit', $limit);
      $this->setState('limitstart', $limitstart);

      $filter_order = JFactory::getApplication()->input->get('filter_order');
      $filter_order_Dir = JFactory::getApplication()->input->get('filter_order_Dir');

      $this->setState('filter_order', $filter_order);
      $this->setState('filter_order_Dir', $filter_order_Dir);

      $this->setState('filter.access', true);

   }


   /**
    * Returns the query
    * @return string The query to be used to retrieve the rows from the database
    */
   private function _buildQuery()
   {
      // use alias t1 for easier JOINs writing
      $query  = 'SELECT t1.id AS project_id, t1.parent_id, t1.title AS project_name, t1.alias, t1.description, ';
      $query .= 't1.state, t1.lft, t1.rgt, t1.level, t1.access, t1.checked_out, t1.checked_out_time, t1.start_date, ';
      $query .= 't1.target_end_date, t1.actual_end_date, t1.access, ';
      $query .= 't1.created_on, t1.created_by, t1.modified_on, t1.modified_by ';
      $query .= 'FROM `#__it_projects` t1 ';
      $query .= $this->_buildQueryWhere() . $this->_buildQueryOrderBy();
      return $query;
   }

   /**
    * Returns the 'order by' part of the query
    * @return string the order by''  part of the query
    */
   private function _buildQueryOrderBy()
   {
       $app = JFactory::getApplication();

      // default field for records list
      $default_order_field = '`lft`';
      // Array of allowable order fields
       $allowedOrders = explode(',', 'title,description,state,start_date,target_end_date,actual_end_date,created_on,created_by,modified_on,modified_by');

      // retrive ordering info
      $filter_order = $app->getUserStateFromRequest('com_issuetracker.filter_order', 'filter_order', $default_order_field);
      $filter_order_Dir = strtoupper($app->getUserStateFromRequest('com_issuetracker.filter_order_Dir', 'filter_order_Dir', 'ASC'));

      // validate the order direction, must be ASC or DESC
      if ($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC') {
         $filter_order_Dir = 'ASC';
      }

      // if order column is unknown use the default
      if ((isSet($allowedOrders)) && !in_array($filter_order, $allowedOrders)){
         $filter_order = $default_order_field;
      }

      // $prefix = 't1';
      switch ( $filter_order ) {
         case 'title':
         case 'project_name':
            $filter_order = 'title';
            break;
      }
      // return the ORDER BY clause
      return " ORDER BY {$filter_order} {$filter_order_Dir}";
   }

   /**
    * @return string
    */
   private function _buildQueryWhere()
   {
      $app = JFactory::getApplication();

      // Do not display the root node.
      $where = ' WHERE ( t1.`state`=1 AND t1.`level` > 0 ) ';

      $search = $app->getUserStateFromRequest('com_issuetrackersearch', 'search', '');

      // Get params
      $params =    $app->getParams();
      $projids    = $params->get('project_ids', array());  // It is an array even if there is only one element!

      if ( ! empty($projids) && $projids[0] != "" ) {
         // Check if we have 0 in our array, if so ignore the where clause inclusion.
         $pids = implode(',', $projids);                   // Put in a form suitable for our query.
         if ( substr($pids, 0, 1) == ',')  $pids = substr($pids,1);   // Check that first character is not a comma.
         if (strncmp($pids, '0',1 ) != 0) {
            $where .= ' AND t1.`id` IN ('.$pids.')';
         }
      }

      // Filter by access level.
      if ($access = $this->getState('filter.access')) {
         $user = JFactory::getUser();
         $groups  = implode(',', $user->getAuthorisedViewLevels());
         $where .= ' AND t1.access IN ('.$groups.')';
      }

      if (!$search) return $where;

      $allowedSearch = explode(',', 'title,project_name,created_by,modified_by');
      $wheres = '';
      foreach($allowedSearch as $field){
         if (!$field) return '';
         $wheres .= " OR (t1.`$field` LIKE '%" . addSlashes($search) . "%') ";
      }
      $where .= " AND ( " . substr($wheres, 4) . ") ";

      return $where;
   }


   /**
    * Retrieves the data
    * @return array Array of objects containing the data from the database
    */
   public function getData()
   {
      // Lets load the data if it doesn't already exist
      if (empty( $this->_data ))    {
         $query = $this->_buildQuery();
         $this->_data = $this->_getList( $query, intval($this->getState('limitstart')), intval($this->getState('limit')));
      }
      $this->_data = IssueTrackerHelper::updateprojectname($this->_data);
      return $this->_data;
   }

   /**
    * Gets the number of published records
    * @return int
    */
   public function getTotal()
   {
      // $app = JFactory::getApplication();

      $db = JFactory::getDBO();
      $query   = $db->getQuery(true);

      $query->select(' COUNT(*) ');
      $query->from('#__it_projects AS t1');

      $where = $this->_buildQueryWhere();
      if ( empty($where) ) {
         // No where clause required.
      } else {
         $query .= $where;
      }

      $db->setQuery($query);
      $db->execute();
      return $db->loadResult();
   }

   /**
    * Gets the Pagination Object
    * @return object JPagination
    */
   public function getPagination()
   {
      // Load the content if it doesn't already exist
      if (empty($this->_pagination)) {
         JLoader::import('joomla.html.pagination');
         $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
      }
      return $this->_pagination;
   }
}