<?php
/*
 *
 * @Version       $Id: itpeoplelist.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

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
class IssueTrackerModelItpeoplelist extends JModelList
{
     /**
     * Constructor.
     *
     * @param    array $config   An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'ordering', 'a.ordering',
                'published', 'a.published',
                'person_name', 'a.person_name',
                'username', 'a.username',
                'person_email','a.person_email',
                'user_id','a.user_id',
                'person_role','a.person_role',
                'assigned_project','a.assigned_project',
                'registered','a.registered',
                'issues_admin','a.issues_admin',
                'email_notifications','a.email_notifications',
                'staff', 'a.staff'
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

      $projectId = $this->getUserStateFromRequest($this->context.'.filter.project', 'filter_project');
      $this->setState('filter.project', $projectId);

      $rolesId = $this->getUserStateFromRequest($this->context.'.filter.roles', 'filter_roles');
      $this->setState('filter.roles', $rolesId);

      $published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
      $this->setState('filter.state', $published);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.ordering', 'asc');
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
      $id.= ':' . $this->getState('filter.project');
      $id.= ':' . $this->getState('filter.roles');

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
      $query->from('`#__it_people` AS a');

      // Join over the users for the checked out user.
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

      // Outer join over the it_projects table since not every person has a project assignment.
      $query->select('t2.title AS project_name, t2.id AS project_id');
      $query->join('LEFT OUTER', '#__it_projects AS t2 ON t2.id = a.assigned_project');

      // Outer join over the it_roles table.
      $query->select('t3.role_name AS role_name, t3.id AS role_id');
      $query->join('LEFT OUTER', '#__it_roles AS t3 ON t3.id = a.person_role');

      // Filter by published state
      $published = $this->getState('filter.state');
      if (is_numeric($published)) {
         $query->where('a.published = '.(int) $published);
      } else if ($published === '') {
         $query->where('(a.published IN (0, 1))');
      }

      // Filter by roles
      $role = $this->getState('filter.roles');
      if (is_numeric($role)) {
         $query->where('a.person_role = '.(int) $role);
      }

      // Filter by project
      $proj = $this->getState('filter.project');
      if (is_numeric($proj)) {
         $query->where('a.assigned_project = '.(int) $proj);
      }

      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = '.(int) substr($search, 3));
         } else {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('( a.person_name LIKE '.$search.'  OR  a.person_email LIKE '.$search.' OR a.username LIKE '.$search.')');
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