<?php
/*
 *
 * @Version       $Id: customfields.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Issue tracker custom fields.
 */
class IssuetrackerModelcustomfields extends JModelList
{
   /**
    * Constructor.
    *
    * @param    array    $config An optional associative array of configuration settings.
    * @see      JController
    * @since    1.6
    */
   public function __construct($config = array())
   {
      if (empty($config['filter_fields'])) {
         $config['filter_fields'] = array(
               'id', 'a.id',
               'ordering', 'a.ordering',
               'state', 'a.state',
               'name', 'a.name',
               'type', 'a.type',
               'validation', 'a.validation',
               'access', 'a.access', 'access_level',
               'group', 'a.group',
               'group_name', 'cfgroup.name',
 //              'alias', 'a.alias',
               'value', 'a.value',
               'tooltip', 'a.tooltip',
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
      $query->from('`#__it_custom_field` AS a');

      // Join over the users for the checked out user.
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

      // Join over the custom group
      $query->select('cfgroup.name AS group_name');
      $query->select('cfgroup.id AS group_id');
      $query->join('LEFT', '#__it_custom_field_group AS cfgroup ON cfgroup.id = a.`group`');

      // Join over the created by field 'created_by'
//      $query->select('created_by.name AS created_by');
//      $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

      // Join over the asset groups.
      $query->select('ag.title AS access_level');
      $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

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
         } else {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
            $query->where('( a.validation LIKE '.$search.'  OR  a.tooltip LIKE '.$search.' )');
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


   /**
    * @return mixed
    */
   function getTotal()
   {
      $app          = JFactory::getApplication();
      $input        = $app->input;
      $option       = $input->get('option');
      $view         = $input->get('view');
      $db           = JFactory::getDBO();
      $filter_state = $app->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', 1, 'int');
      $search       = $app->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
      $search       = JString::strtolower($search);
      $filter_type  = $app->getUserStateFromRequest($option.$view.'filter_type', 'filter_type', '', 'string');
      $filter_group = $app->getUserStateFromRequest($option.$view.'filter_group', 'filter_group', '', 'string');

      $query = "SELECT COUNT(*) FROM #__it_custom_field WHERE id>0";

      if ($filter_state > -1) {
         $query .= " AND state={$filter_state}";
      }

      if ($search) {
         $escaped = $db->escape($search, true);
         $query .= " AND LOWER( name ) LIKE ".$db->Quote('%'.$escaped.'%', false);
      }

      if ($filter_type) {
         $query .= " AND `type`=".$db->Quote($filter_type);
      }

      if ($filter_group) {
         $query .= " AND `group`=".$db->Quote($filter_group);
      }

      $db->setQuery($query);
      $total = $db->loadresult();
      return $total;
   }

   function publish()
   {
      $app = JFactory::getApplication();
      $cid = JFactory::getApplication()->input->get('cid');
      foreach ($cid as $id) {
         $row = JTable::getInstance('IssuetrackerCustomField', 'Table');
         $row->load($id);
         $row->publish($id, 1);
      }
      $cache = JFactory::getCache('com_issuetracker');
      $cache->clean();
      $app->redirect('index.php?option=com_issuetracker&view=customfields');
   }

   function unpublish()
   {
      $app = JFactory::getApplication();
      $cid = JFactory::getApplication()->input->get('cid');
      foreach ($cid as $id) {
         $row = JTable::getInstance('IssuetrackerCustomField', 'Table');
         $row->load($id);
         $row->publish($id, 0);
      }
      $cache = JFactory::getCache('com_issuetracker');
      $cache->clean();
      $app->redirect('index.php?option=com_issuetracker&view=customfields');
   }

   /**
    * @return bool
    */
   function remove()
   {
      $app = JFactory::getApplication();
      // $db  = JFactory::getDBO();
      $cid = JFactory::getApplication()->input->get('cid');
      foreach ($cid as $id) {
         $row = JTable::getInstance('IssuetrackerCustomField', 'Table');
         $row->load($id);
         $row->delete($id);
      }
      $cache = JFactory::getCache('com_issuetracker');
      $cache->clean();
      $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_COMPLETED'));
      $app->redirect('index.php?option=com_issuetracker&view=customfields');
   }

   /**
    * @return mixed
    */
   function getCustomFieldGroup()
   {
      $cid = JFactory::getApplication()->input->get('cid');
      $row = JTable::getInstance('IssuetrackerCustomFieldGroup', 'Table');
      $row->load($cid);
      return $row;
   }

   /**
    * @param bool $filter
    * @return mixed
    */
   function getGroups($filter = false)
   {
      $app    = JFactory::getApplication();
      $input  = $app->input;
      $option = $input->get('option');
      $view   = $input->get('view');
      $limit  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
      $limitstart = $app->getUserStateFromRequest($option.$view.'.limitstart', 'limitstart', 0, 'int');
      $db = JFactory::getDBO();
      $query = "SELECT * FROM #__it_custom_field_group ORDER BY `name`";
      if ($filter) {
         $db->setQuery($query);
      } else {
         $db->setQuery($query, $limitstart,  $limit);
      }

      $rows = $db->loadObjectList();
      for ($i = 0; $i < sizeof($rows); $i++) {
          $query = "SELECT name FROM #__it_projects WHERE customFieldGroup=".(int)$rows[$i]->id;
          $db->setQuery($query);
          $jversion = new JVersion();
          if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
             $categories = $db->loadColumn();
          } else {
             $categories = $db->loadResultArray();
          }
          if (is_array($categories)) {
             $rows[$i]->categories = implode(', ', $categories);
          } else {
             $rows[$i]->categories = '';
          }
      }
      return $rows;
   }

   /**
    * @return mixed
    */
   function getTotalGroups()
   {
      $db = JFactory::getDBO();
      $query = "SELECT COUNT(*) FROM #__it_custom_field_group";
      $db->setQuery($query);
      $total = $db->loadResult();
      return $total;
   }

   function saveGroup()
   {
      $app = JFactory::getApplication();
      // $id  = JFactory::getApplication()->input->getInt('id');
      $row = JTable::getInstance('IssuetrackerCustomFieldsGroup', 'Table');
      if (!$row->bind(JFactory::getApplication()->input->get('post'))) {
         $app->enqueueMessage($row->getError(), 'error');
         $app->redirect('index.php?option=com_issuetracker&view=customfieldgroups');
      }

      if (!$row->check()) {
         $app->enqueueMessage($row->getError(), 'error');
         $app->redirect('index.php?option=com_issuetracker&view=customfieldgroup&cid='.$row->id);
      }

      if (!$row->store()) {
         $app->enqueueMessage($row->getError(), 'error');
         $app->redirect('index.php?option=com_issuetracker&view=customfieldgroup');
      }

      switch(JFactory::getApplication()->input->get('task'))
      {
         case 'apply' :
            $msg = JText::_('COM_ISSUETRACKER_CHANGES_TO_GROUP_SAVED');
            $link = 'index.php?option=com_issuetracker&view=customfieldgroup&cid='.$row->id;
            break;
         case 'save' :
         default :
            $msg = JText::_('COM_ISSUETRACKER_GROUP_SAVED');
            $link = 'index.php?option=com_issuetracker&view=customfieldgroups';
            break;
      }

      $cache = JFactory::getCache('com_issuetracker');
      $cache->clean();
      $app->enqueueMessage($msg);
      $app->redirect($link);
   }

   function removeGroups()
   {
      $app = JFactory::getApplication();
      $db =  JFactory::getDBO();
      $cid = JFactory::getApplication()->input->get('cid');
      JArrayHelper::toInteger($cid);
      foreach ($cid as $id) {
         $row = JTable::getInstance('IssuetrackerCustomFieldGroup', 'Table');
         $row->load($id);
         $query = "DELETE FROM #__it_custom_field WHERE `group`={$id}";
         $db->setQuery($query);
         $db->execute();
         $row->delete($id);
      }
      $cache = JFactory::getCache('com_issuetracker');
      $cache->clean();
      $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_COMPLETED'));
      $app->redirect('index.php?option=com_issuetracker&view=customfieldgroups');
   }
}