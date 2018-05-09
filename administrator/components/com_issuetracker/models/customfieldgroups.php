<?php
/*
 *
 * @Version       $Id: customfieldgroups.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Custom field groups.
 */
class IssuetrackerModelcustomfieldgroups extends JModelList
{
   /**
    * Constructor.
    *
    * @param    array    $config An optional associative array of configuration settings.
    * @see        JController
    * @since    1.6
    */
   public function __construct($config = array())
   {
      if (empty($config['filter_fields'])) {
         $config['filter_fields'] = array(
                'id', 'a.id',
                'name','a.name',
                'project_name','pr.title',
//                'ordering', 'a.ordering',
//                'state', 'a.state',
//                'issue_id', 'a.issue_id',
                'created_on', 'a.created_on',
                'created_by', 'a.created_by',
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
   protected function populateState($ordering = null, $direction = null)
   {
      // Initialise variables.
      $app = JFactory::getApplication('administrator');

      // Load the filter state.
      $search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
      $this->setState('filter.search', $search);

      $published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
      $this->setState('filter.state', $published);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.id', 'asc');
   }

   /**
    * Method to get a store id based on model configuration state.
    *
    * This is necessary because the model is used by the component and
    * different modules that might need different sets of data or different
    * ordering requirements.
    *
    * @param   string      $id   A prefix for the store id.
    * @return  string      A store id.
    * @since   1.6
    */
   protected function getStoreId($id = '')
   {
      // Compile the store id.
      $id.= ':' . $this->getState('filter.search');
      $id.= ':' . $this->getState('filter.state');

      return parent::getStoreId($id);
   }

   /**
    * Build an SQL query to load the list data.
    *
    * @return  JDatabaseQuery
    * @since   1.6
    */
   protected function getListQuery()
   {
      // Create a new query object.
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);

      // Select the required fields from the table.
      $query->select(
         $this->getState(
            'list.select',
            'a.*'
         )
      );
      $query->from('`#__it_custom_field_group` AS a');

      // Join over projects table
      $query->select('pr.title AS project_name, pr.id AS project_id');
      $query->join('LEFT','#__it_projects AS pr ON pr.customFieldsGroup = a.id');


      // Join over the users for the checked out user.
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

      // Join over the created by field 'created_by'
//      $query->select('created_by.name AS created_by');
//      $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');


      // Filter by published state
      $published = $this->getState('filter.state');
      if (is_numeric($published)) {
         $query->where('a.state = '.(int) $published);
      } else if ($published === '') {
         $query->where('(a.state IN (0, 1))');
      }

      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = '.(int) substr($search, 3));
         }
      }

      // Add the list ordering clause.
      $orderCol   = $this->state->get('list.ordering');
      $orderDirn  = $this->state->get('list.direction');
      if ($orderCol && $orderDirn) {
         $query->order($db->escape($orderCol.' '.$orderDirn));
      }

      return $query;
   }
}