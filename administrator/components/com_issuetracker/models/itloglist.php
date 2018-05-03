<?php
/*
 *
 * @Version       $Id: itloglist.php 2167 2016-01-01 16:41:39Z geoffc $
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

JLoader::import('joomla.application.component.modellist');

/**
 * Methods supporting a list of Log records.
 */
class IssuetrackerModelItloglist extends JModelList
{
     /**
     * Constructor.
     *
     * @param    array $config   An optional associative array of configuration settings.
     * @see      JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'priority', 'a.priority',
                'date', 'a.date',
                'category', 'a.category',
                'message', 'a.message',
                'etype','a.etype'
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

      $priorityId = $this->getUserStateFromRequest($this->context.'.filter.priority', 'filter_priority');
      $this->setState('filter.priority', $priorityId);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.id', 'desc');
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
      $id.= ':' . $this->getState('filter.priority');

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
      // Use specific query to enable case statement to work.
      $query->select("a.id, a.priority, a.message, a.date, a.category");

      $query->select("case priority when '1' then 'Emergency' when '2' then 'Alert' when '4' then 'Critical' when '8' then 'Error' when '16' then 'Warning' when '32' then 'Notice' when '64' then 'Info' when '128' then 'Debug'
 end as etype");

      $query->from('`#__it_issues_log` AS a');

      // Filter by log priority
      $pid = $this->getState('filter.priority');
      if (is_numeric($pid)) {
         $query->where('a.priority = ' . (int) $pid);
      }

      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = '.(int) substr($search, 3));
         } else {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('( a.message LIKE '.$search.' )');
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
    * Method to delete one or more records.
    *
    * @param   array  &$pks  An array of record primary keys.
    *
    * @return  boolean  True if successful, false if an error occurs.
    *
    * @since   11.1
    */
   public function delete(&$pks)
   {
      $pks = (array) $pks;
      $row = $this->getTable('itlog','IssueTrackerTable');

      // Iterate the items to delete each one.
      foreach ($pks as $pk)
      {
        if (!$row->delete( $pk )) {
           $this->setError( $row->getErrorMsg() );
           return false;
        }
      }
      return true;
   }

   /**
    * Method to purge the table.
    *
    * @param   None
    *
    * @return  True.
    *
    */
   public function purge()
   {
      $db      = $this->getDbo();
      $query   = "TRUNCATE TABLE `#__it_issues_log`";
      $db->setQuery($query);
      $db->execute();

      return true;
   }
}