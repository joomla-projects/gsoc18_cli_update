<?php
/*
 *
 * @Version       $Id: emails.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.modellist');

/**
 * Methods supporting a list of Type records.
 */
class IssueTrackerModelEmails extends JModelList
{
   /**
    * @param array $config
    */
   public function __construct($config = array())
   {
      if (empty($config['filter_fields'])) {
         $config['filter_fields'] = array(
            'id', 'a.id',
            'type', 'a.type',
            'description','a.description',
            'ordering', 'a.ordering',
            'state', 'a.state',
            'checked_out', 'a.checked_out',
            'checked_out_time', 'a.checked_out_time'
         );
      }
      parent::__construct($config);
   }

   /**
    * @param null $ordering
    * @param null $direction
    */
   protected function populateState($ordering = null, $direction = null)
   {
      // Initialise variables.
      // $app     = JFactory::getApplication();
      $context = $this->context;

      $search = $this->getUserStateFromRequest($context.'.search', 'filter_search');
      $this->setState('filter.search', $search);

      $published = $this->getUserStateFromRequest($context.'.filter.state', 'filter_state', '');
      $this->setState('filter.state', $published);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.type', 'asc');
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
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);
      // $user    = JFactory::getUser();

      // Select the required fields from the table.
      $query->select(
         $this->getState(
            'list.select',
            'a.*'
         )
      );
      $query->from('`#__it_emails` AS a');

      // Join over the users for the checked out user.
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

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
            $query->where('( a.type LIKE '.$search.'  OR  a.description LIKE '.$search.' )');
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
