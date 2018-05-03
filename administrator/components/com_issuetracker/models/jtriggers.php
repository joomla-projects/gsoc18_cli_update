<?php
/*
 *
 * @Version       $Id: jtriggers.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Issue Tracker records.
 */
class IssuetrackerModeljtriggers extends JModelList {

   /**
    * Constructor.
    *
    * @param    array $config   An optional associative array of configuration settings.
    * @see        JController
    * @since    1.6
    */
   public function __construct($config = array()) {
      if (empty($config['filter_fields'])) {
         $config['filter_fields'] = array(
                 'id', 'a.id',
                 'table_name', 'a.table_name',
                 'trigger_schema', 'a.trigger_schema',
                 'trigger_name', 'a.trigger_name',
                 'trigger_type', 'a.trigger_type',
                 'trigger_event', 'a.trigger_event',
                 'trigger_text', 'a.trigger_text',
                 'columns','a.columns',
                 'action_orientation','a.action_orientation',
                 'applied','a.applied',
                 'created_by', 'a.created_by',
                 'created_by_alias', 'a.created_by_alias',
                 'created_on', 'a.created_on',
         );
      }

      parent::__construct($config);
   }

   /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    * @param null $ordering
    * @param null $direction
    */
   protected function populateState($ordering = null, $direction = null) {
      // Initialise variables.
      $app = JFactory::getApplication('administrator');

      // Load the filter state.
      $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
      $this->setState('filter.search', $search);

//      $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
//      $this->setState('filter.state', $published);

      $tablename = $app->getUserStateFromRequest($this->context . '.filter.tablename', 'filter_tablename', '', 'string');
      $this->setState('filter.tablename', $tablename);
/*
      $component = $app->getUserStateFromRequest($this->context . '.filter.component', 'filter_component', '', 'string');
      $this->setState('filter.component', $component);
*/
      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.table_name', 'asc');
   }

   /**
    * Method to get a store id based on model configuration state.
    *
    * This is necessary because the model is used by the component and
    * different modules that might need different sets of data or different
    * ordering requirements.
    *
    * @param  string      $id   A prefix for the store id.
    * @return string      A store id.
    * @since  1.6
    */
   protected function getStoreId($id = '') {
      // Compile the store id.
      $id.= ':' . $this->getState('filter.search');
      $id.= ':' . $this->getState('filter.state');

      return parent::getStoreId($id);
   }

   /**
    * Build an SQL query to load the list data.
    *
    * @return JDatabaseQuery
    * @since  1.6
    */
   protected function getListQuery() {
      // Create a new query object.
      $db = $this->getDbo();
      $query = $db->getQuery(true);

      // Select the required fields from the table.
      $query->select( $this->getState( 'list.select', 'a.*' ) );
      $query->from('`#__it_triggers` AS a');

      // Join over the user field 'created_by'
      $query->select('created_by.name AS created_by');
      $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

      // Filter by published state
/*
      $published = $this->getState('filter.state');
      if (is_numeric($published)) {
         $query->where('a.state = '.(int) $published);
      } else if ($published === '') {
         $query->where('(a.state IN (0, 1))');
      }
*/
      // Filter by tablename
      $tablename = $this->getState('filter.tablename');
      if ( !empty($tablename) && $tablename != 'All' ) {
         $query->where("a.table_name = '". $tablename."'");
      }
/*
      // Filter by component
      $component = $this->getState('filter.component');
      if ( !empty($component) && $component != 'All') {
         $query->where("a.component = '". $component."'");
      }
*/
      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = ' . (int) substr($search, 3));
         } else {
            $search = $db->Quote('%' . $db->escape($search, true) . '%');
            $query->where('( a.table_name LIKE '.$search.'  OR  a.trigger_text LIKE '.$search.' )');
         }
      }

      // Add the list ordering clause.
      $orderCol = $this->state->get('list.ordering');
      $orderDirn = $this->state->get('list.direction');
      if ($orderCol && $orderDirn) {
         $query->order($db->escape($orderCol . ' ' . $orderDirn));
      }

      return $query;
   }

   /**
    * @return mixed
    */
   public function getItems() {
      $items = parent::getItems();

      return $items;
   }
}