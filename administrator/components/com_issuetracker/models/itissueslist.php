<?php
/*
 *
 * @Version       $Id: itissueslist.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
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
class IssueTrackerModelItissueslist extends JModelList
{
     /**
     * Constructor.
     *
     * @param    array  $config  An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'issue_summary', 'a.issue_summary',
                'issue_description', 'a.issue_description',
                'alias', 'a.alias',
                'project_name', 't2.title',
                'ppath', 't2.path',
                'person_name', 't3.person_name',
                'identifying_name', 't7.person_name',
                'status', 'a.status',
                'public', 'a.public',
                'issue_type', 'a.issue_type',
                'priority', 'a.priority',
                'identified_date','a.identified_date',
                'actual_resolution_date','a.actual_resolution_date',
                'created_by','a.created_by',
                'created_on','a.created_on',
                'modified_by','a.modified_by',
                'modified_on','a.modified_on'
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

      $assigned = $this->getUserStateFromRequest($this->context.'.filter.assigned', 'filter_assigned', '');
      $this->setState('filter.assigned', $assigned);

      $identifier = $this->getUserStateFromRequest($this->context.'.filter.identifier', 'filter_identifier', '');
      $this->setState('filter.identifier', $identifier);

      $published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
      $this->setState('filter.state', $published);

      $projectId = $this->getUserStateFromRequest($this->context.'.filter.project_id', 'filter_project_id');
      $this->setState('filter.project_id', $projectId);

      $statusId = $this->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
      $this->setState('filter.status_id', $statusId);

      $typeId = $this->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id');
      $this->setState('filter.type_id', $typeId);

      $priorityId = $this->getUserStateFromRequest($this->context.'.filter.priority_id', 'filter_priority_id');
      $this->setState('filter.priority_id', $priorityId);

      $createdbyId = $this->getUserStateFromRequest($this->context.'.filter.created_by_id', 'filter_created_by');
      $this->setState('filter.created_by', $createdbyId);
      $createdonId = $this->getUserStateFromRequest($this->context.'.filter.created_on_id', 'filter_created_on');
      $this->setState('filter.created_on', $createdonId);
      $modifiedbyId = $this->getUserStateFromRequest($this->context.'.filter.modified_id', 'filter_modified_by');
      $this->setState('filter.modified_by', $modifiedbyId);
      $modifiedonId = $this->getUserStateFromRequest($this->context.'.filter.modified_on', 'filter_modified_on');
      $this->setState('filter.modified_on', $modifiedonId);

      $tag = $this->getUserStateFromRequest($this->context.'.filter.tag', 'filter_tag', '');
      $this->setState('filter.tag', $tag);

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

      // List state information.
      parent::populateState('a.ordering', 'desc');

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
      $id.= ':' . $this->getState('filter.assigned');
      $id.= ':' . $this->getState('filter.identifier');
      $id.= ':' . $this->getState('filter.project_id');
      $id.= ':' . $this->getState('filter.type_id');
      $id.= ':' . $this->getState('filter.status_id');
      $id.= ':' . $this->getState('filter.priority_id');
      $id.= ':' . $this->getState('filter.created_by');
      $id.= ':' . $this->getState('filter.created_on');
      $id.= ':' . $this->getState('filter.modified_by');
      $id.= ':' . $this->getState('filter.modified_on');

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

      $query->from('`#__it_issues` AS a');

      // Join over the it_projects table.
      $query->select('t2.title AS project_name, t2.id AS project_id, t2.path AS ppath');
      $query->join('LEFT', '#__it_projects AS t2 ON t2.id = a.related_project_id');

      // Join over the it_people table.
      $query->select('t3.person_name AS person_name');
      $query->join('LEFT', '#__it_people AS t3 ON t3.user_id = a.assigned_to_person_id');

      // Join over the it_people table.
      $query->select('t7.person_name AS identifying_name');
      $query->join('LEFT', '#__it_people AS t7 ON t7.id = a.identified_by_person_id');

      // Join over the it_status table.
      $query->select('t4.status_name AS status_name');
      $query->join('LEFT', '#__it_status AS t4 ON t4.id = a.status');

      // Join over the it_priority table.
      $query->select('t5.priority_name AS priority_name, t5.ranking as ranking');
      $query->join('LEFT', '#__it_priority AS t5 ON t5.id = a.priority');

      // Join over the it_types table.
      $query->select('t6.type_name AS type_name');
      $query->join('LEFT', '#__it_types AS t6 ON t6.id = a.issue_type');

      // Join over the users for the checked out user.
      $query->select('uc.name AS editor');
      $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

      // Filter by published state
      $published = $this->getState('filter.state');
      if (is_numeric($published)) {
         $query->where('a.state = '.(int) $published);
      } else if ($published === '' || empty($published) ) {
         $query->where('(a.state IN (0, 1))');
      }

      // Filter by project_id
      $pid = $this->getState('filter.project_id');
      if (is_numeric($pid)) {
         $query->where('t2.id = ' . (int) $pid );
      }

      // Filter by assigned person
      $pid = $this->getState('filter.assigned');
      if (is_numeric($pid)) {
         $query->where('a.assigned_to_person_id = ' . (int) $pid);
      }

      // Filter by identifying person
      $pid = $this->getState('filter.identifier');
      if (is_numeric($pid)) {
         $query->where('a.identified_by_person_id = ' . (int) $pid);
      }

      // Filter by status_id
      $sid = $this->getState('filter.status_id');
      if (is_numeric($sid)) {
         $query->where('t4.id = ' . (int) $sid);
      }

      // Filter by priority_id
      $pid = $this->getState('filter.priority_id');
      if (is_numeric($pid)) {
         $query->where('t5.id = ' . (int) $pid);
      }

      // Filter by type_id
      $tid = $this->getState('filter.type_id');
      if (is_numeric($tid)) {
         $query->where('t6.id = ' . (int) $tid);
      }

      // Filter by created_by
      $tid = $this->getState('filter.created_by');
      if (is_numeric($tid)) {
         $query->where('a.created_by = ' . (int) $tid);
      }

      // Filter by created_on
      $tid = $this->getState('filter.created_on');
      if (is_numeric($tid)) {
         $query->where('a.created_on = ' . (int) $tid);
      }

      // Filter by modified_by
      $tid = $this->getState('filter.modified_by');
      if (is_numeric($tid)) {
         $query->where('a.modified_by = ' . (int) $tid);
      }

      // Filter by modified_on
      $tid = $this->getState('filter.modified_on');
      if (is_numeric($tid)) {
         $query->where('a.modified_on = ' . (int) $tid);
      }

      // Filter by search in title
      $search = $this->getState('filter.search');
      if (!empty($search)) {
         if (stripos($search, 'id:') === 0) {
            $query->where('a.id = '.(int) substr($search, 3));
         } else {
            $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('( a.issue_summary LIKE '.$search.'  OR  a.issue_description LIKE '.$search.' OR a.progress LIKE '.$search.' OR a.resolution_summary LIKE '.$search.' OR a.alias LIKE '.$search.')');
         }
      }

      // Filter by a single tag.
      $tagId = $this->getState('filter.tag');
      if (is_numeric($tagId)) {
         $query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId);
         $query->join(
            'LEFT',
            $db->quoteName('#__contentitem_tag_map', 'tagmap') . ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' .  $db->quoteName('a.id')
               . ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_issuetracker.itissue')
         );
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
     * Methods to get the latest opened issues
     * @param int $count
     * @return object with data
     * 20/8/12 Remove time element from output. Was %k:%i
     */
   function latestIssues( $count = 10)
   {
      $db = JFactory::getDBO();

      $query  = "SELECT t1.id, t1.issue_summary, t2.title as project_name, t1.state, DATE_FORMAT( t1.identified_date, \"%d.%m.%Y\") AS issuedate ";
      $query .= " ,t2.id AS project_id ";
      $query .= "FROM #__it_issues t1 ";
      $query .= "LEFT JOIN #__it_projects AS t2 ON t2.id = t1.related_project_id ";
      $query .= "WHERE t1.state IN (0,1) ";
      $query .= "ORDER BY t1.created_on DESC LIMIT " . $count;

      $db->setQuery($query);
      $rows = $db->loadObjectList();
      if ( ! empty($rows) ) $rows = IssueTrackerHelper::updateprojectname($rows);

      return $rows;
   }

    /**
     * Methods to get the Overdue issues
     * @param int $count
     * @return object with data
     */
   function overdueIssues( $count = 10)
   {
      $db = JFactory::getDBO();

      $query  = "SELECT i.id, pr.priority_name AS priority, i.issue_summary, ";
      $query .= "       p.person_name assignee, DATE_FORMAT(i.target_resolution_date, \"%d.%m.%Y\") AS target_resolution_date, r.title AS project_name ";
      $query .= " ,r.id as project_id ";
      $query .= "FROM `#__it_issues` i ";
      $query .= "RIGHT OUTER JOIN `#__it_people` p ";
      $query .= "  ON i.assigned_to_person_id = p.id ";
      $query .= "LEFT JOIN `#__it_projects` r ";
      $query .= " ON i.related_project_id = r.id ";
      $query .= "LEFT JOIN `#__it_priority` pr ";
      $query .= " ON i.priority = pr.id ";
      $query .= "WHERE i.target_resolution_date < sysdate() ";
      $query .= "      AND i.target_resolution_date IS NOT NULL ";
      $query .= "      AND i.target_resolution_date != '0000-00-00 00:00:00' ";
      $query .= "  AND i.status != '1' ";
      $query .= " AND i.state IN (0,1) ";
      $query .= "ORDER BY i.priority, i.target_resolution_date ASC LIMIT " . $count;

      $db->setQuery($query);
      $rows = $db->loadObjectList();
      if (! empty($rows) ) $rows = IssueTrackerHelper::updateprojectname($rows);
      return $rows;
   }

   /**
    * Methods to get the Issue Summary
    * @return object with data
    */
   function issueSummary ()
   {
      $db = JFactory::getDBO();

      $query  = "SELECT title as project_name, t2.id as project_id, ";
      $query .= "   DATE_FORMAT( MIN(identified_date), \"%d.%m.%Y\") AS first_identified, ";
      $query .= "   DATE_FORMAT( MAX(actual_resolution_date), \"%d.%m.%Y\") AS last_closed, ";
      $query .= "   COUNT(t1.id) AS total_issues, ";
      $query .= "   SUM(IF(status='4',1,0)) AS open_issues, ";              // Open = 4
      $query .= "   SUM(IF(status='3',1,0)) AS onhold_issues, ";            // On-Hold = 3
      $query .= "   SUM(IF(status='2',1,0)) AS inprogress_issues, ";        // In-Progress = 2
      $query .= "   SUM(IF(status='1',1,0)) AS closed_issues, ";       // Closed = 1
      $query .= "   SUM(IF(status='4',IF(priority IS NULL,1,0),0)) AS open_no_prior, ";
      $query .= "   SUM(IF(status='4',IF(priority='1',1,0),0))  AS open_high_prior, ";   // High = 1
      $query .= "   SUM(IF(status='4',IF(priority='3',1,0),0)) AS open_medium_prior, ";  // Medium = 2
      $query .= "   SUM(IF(status='4',IF(priority='2',1,0),0)) AS open_low_prior ";      // Low = 3
      $query .= "FROM #__it_issues t1 ";
      $query .= "RIGHT OUTER JOIN #__it_projects t2 ";
      $query .= " ON t1.related_project_id = t2.id ";
      $query .= " WHERE t2.state IN (0,1) ";
      $query .= " AND t1.state IN (0,1) ";
//      $query .= "WHERE t2.title != 'Root' ";
      $query .= "GROUP BY related_project_id ";
      $query .= "HAVING COUNT(related_project_id) > 0 ";
      $query .= "ORDER BY t2.lft ";

      $db->setQuery($query);
      $rows = $db->loadObjectList();
      if ( !empty($rows) ) $rows = IssueTrackerHelper::updateprojectname($rows);

      return $rows;
   }

   /**
    * Methods to get the Unassigned Issue Report
    * @return object with data
    */
   function unassignedissues ()
   {
      // Get default assignee from parameters.
      $db = JFactory::getDBO();

      $query  = "SELECT i.id, ";
      $query .= "    pr.priority_name AS priority, ";
      $query .= "    i.issue_summary, ";
      $query .= "    DATE_FORMAT(i.target_resolution_date, \"%d.%m.%Y\") AS target_resolution_date, ";
      $query .= "    r.title AS project_name, ";
      $query .= "    r.id AS project_id, ";
      $query .= "    p.person_name AS identifiee ";
      $query .= "FROM #__it_issues i, ";
      $query .= "     #__it_people p, ";
      $query .= "     #__it_projects r, ";
      $query .= "     #__it_priority pr ";
      $query .= "WHERE (i.assigned_to_person_id IS NULL ";
      $query .= "      OR i.assigned_to_person_id = 1 ) ";
      $query .= "  AND i.status != '1' ";
      $query .= "  AND i.related_project_id = r.id ";
      $query .= "  AND i.identified_by_person_id = p.id ";
      $query .= "  AND i.priority = pr.id ";
      $query .= "  AND i.state IN (0,1) ";

      $db->setQuery($query);
      $rows = $db->loadObjectList();
      if ( ! empty($rows) ) $rows = IssueTrackerHelper::updateprojectname($rows);
      return $rows;
   }

   /* Following methods all relate to the export functionality. */

   /**
    * Get file name
    *
    * @return  string    The file name
    *
    * @since   1.6
    */
   public function getBaseName()
   {
      if (!isset($this->basename)) {
         $app = JFactory::getApplication();
         $basename = $this->getState('basename');
         // $basen    = $app->getCfg('sitename');
         $basen    = $app->get('sitename');

         $basename = str_replace('__SITE__', $basen, $basename);

         $projectId = $this->getState('filter.project_id');
         if (is_numeric($projectId)) {
            if ($projectId > 0) {
               $projectId = JText::_('COM_ISSUETRACKER_PROJECT') . '_' . $projectId;
               $basename = str_replace('__PROJECTID__', $projectId, $basename);
            } else {
               $basename = str_replace('__PROJECTID__', '', $basename);
            }

            $projectName = IssueTrackerHelper::getprojName($projectId);
            $projectName = JText::_('COM_ISSUETRACKER_PROJECT') . '_' . $projectName;
            $basename = str_replace('__PROJECTNAME__', $projectName, $basename);
         } else {
            $basename = str_replace('__PROJECTID__', '', $basename);
            $basename = str_replace('__PROJECTNAME__', '', $basename);
         }

         $search = $this->getState('filter.search');
         if ( ! empty($search) ) {
           $search = JText::_('COM_ISSUETRACKER_SEARCH') . '_' . $search;
           $basename = str_replace('__SEARCH__', $search, $basename);
         } else {
            $basename = str_replace('__SEARCH__', '', $basename);
         }

         $stateid = $this->getState('filter.state');
         $stateName = JText::_('JALL');
         if (is_numeric($stateid) && $stateid > 0) {
            $stateid = JText::_('JSTATUS') . '_' . $stateid;
            $stateid = JText::_('COM_ISSUETRACKER_PSTATE') . '_' . $stateid;
            $basename = str_replace('__STATEID__', $stateid, $basename);
            switch ($stateid) {
               case 0:
                  $stateName = JText::_('JUNPUBLISHED');
                  break;
               case 1:
                  $stateName = JText::_('JPUBLISHED');
                  break;
               case 2:
                  $stateName = JText::_('JARCHIVED');
                  break;
               case -2:
                  $stateName = JText::_('JTRASHED');
                  break;
               case '*':
                  $stateName = JText::_('JALL');
                  break;
            }
            $stateName = JText::_('COM_ISSUETRACKER_PSTATE') . '_' . $stateName;
            $basename = str_replace('__STATENAME__', $stateName, $basename);
         } else {
            $basename = str_replace('__STATEID__', '', $basename);
            $basename = str_replace('__STATENAME__', '', $basename);
         }

         $assigned = $this->getState('filter.assigned');
         if (is_numeric($assigned)) {
            if (!empty($assigned)) {
               $assigned = JText::_('COM_ISSUETRACKER_ASSIGNED') . '_' . $assigned;
               $basename = str_replace('__ASSIGNEDID__', $assigned, $basename);
            } else  {
               $basename = str_replace('__ASSIGNEDID__', '', $basename);
            }
            $assignedName = $this->getAssignedName();
            $assignedName = JText::_('COM_ISSUETRACKER_ASSIGNED') . '_' . $assignedName;
            $basename = str_replace('__ASSIGNEDNAME__', $assignedName, $basename);
         } else {
            $basename = str_replace('__ASSIGNEDID__', '', $basename);
            $basename = str_replace('__ASSIGNEDNAME__', '', $basename);
         }

         $identifier = $this->getState('filter.identifier');
         if (is_numeric($identifier)) {
            if (!empty($identifier)) {
               $identifier = JText::_('COM_ISSUETRACKER_IDENTIFIEE') . '_' . $identifier;
               $basename = str_replace('__IDENTIFIERID__', $identifier, $basename);
            } else  {
               $basename = str_replace('__IDENTIFIERID__', '', $basename);
            }
            $identifierName = $this->getIdentifierName();
            $identifierName = JText::_('COM_ISSUETRACKER_IDENTIFIEE') . '_' . $identifierName;
            $basename = str_replace('__IDENTIFIERNAME__', $identifierName, $basename);
         } else {
            $basename = str_replace('__IDENTIFIERID__', '', $basename);
            $basename = str_replace('__IDENTIFIERNAME__', '', $basename);
         }

         $createdon = $this->getState('filter.created_on');
         if (!empty($createdon)) {
            $createdon = JText::_('COM_ISSUETRACKER_CREATEDON') . '_' . $createdon;
            $basename = str_replace('__CREATEDON__', $createdon, $basename);
         } else {
            $basename = str_replace('__CREATEDON__', '', $basename);
         }

         $createdby = $this->getState('filter.created_by');
         if (!empty($createdby)) {
            $createdby = JText::_('COM_ISSUETRACKER_CREATEDBY') . '_' . $createdby;
            $basename = str_replace('__CREATEDBY__', $createdby, $basename);
         } else {
            $basename = str_replace('__CREATEDBY__', '', $basename);
         }

         $modifiedon = $this->getState('filter.modified_on');
         if (!empty($modifiedon)) {
            $modifiedon = JText::_('COM_ISSUETRACKER_MODIFIEDON') . '_' . $modifiedon;
            $basename = str_replace('__MODIFIEDON__', $modifiedon, $basename);
         } else {
            $basename = str_replace('__MODIFIEDON__', '', $basename);
         }

         $modifiedby = $this->getState('filter.modified_by');
         if (!empty($modifiedby)) {
            $modifiedby = JText::_('COM_ISSUETRACKER_MODIFIEDBY') . '_' . $modifiedby;
            $basename = str_replace('__MODIFIEDBY__', $modifiedby, $basename);
         } else {
            $basename = str_replace('__MODIFIEDBY__', '', $basename);
         }

         $statusid = $this->getState('filter.status_id');
         if (is_numeric($statusid)) {
            if ( $statusid > 0 ) {
               $statusid = JText::_('COM_ISSUETRACKER_ISTATUS') . '_' . $statusid;
               $basename = str_replace('__STATUSID__', $statusid, $basename);
            } else {
               $basename = str_replace('__STATUSID__', '', $basename);
            }
            $statusName = $this->getStatusName();
            $statusName = JText::_('COM_ISSUETRACKER_ISTATUS') . '_' . $statusName;
            $basename = str_replace('__STATUSNAME__', $statusName, $basename);
         } else {
            $basename = str_replace('__STATUSID__', '', $basename);
            $basename = str_replace('__STATUSNAME__', '', $basename);
         }

         $typeid = $this->getState('filter.type_id');
         if (is_numeric($typeid)) {
            if ( $typeid > 0) {
               $typeid = JText::_('COM_ISSUETRACKER_TYPE') . '_' . $typeid;
               $basename = str_replace('__TYPEID__', $typeid, $basename);
            } else {
               $basename = str_replace('__TYPEID__', '', $basename);
            }
            $typeName = $this->getTypeName();
            $typeName = JText::_('COM_ISSUETRACKER_TYPE') . '_' . $typeName;
            $basename = str_replace('__TYPENAME__', $typeName, $basename);
         } else {
            $basename = str_replace('__TYPEID__', '', $basename);
            $basename = str_replace('__TYPENAME__', '', $basename);
         }

         $priorityid = $this->getState('filter.priority_id');
         if (is_numeric($priorityid)) {
            if ( $priorityid > 0 ) {
               $priorityid = JText::_('COM_ISSUETRACKER_PRIORITY') . '_' . $priorityid;
               $basename = str_replace('__PRIORITYID__', $priorityid, $basename);
            } else {
               $basename = str_replace('__PRIORITYID__', '', $basename);
            }
            $priorityName = $this->getPriorityName();
            $priorityName = JText::_('COM_ISSUETRACKER_PRIORITY') . '_' . $priorityName;
            $basename = str_replace('__PRIORITYNAME__', $priorityName, $basename);
         } else {
            $basename = str_replace('__PRIORITYID__', '', $basename);
            $basename = str_replace('__PRIORITYNAME__', '', $basename);
         }

         $tag = $this->getState('filter.tag');
         if (!empty($tag)) {
            $tag = JText::_('COM_ISSUETRACKER_TAG') . '_' . $tag;
            $basename = str_replace('__TAG__', $tag, $basename);
         } else {
            $basename = str_replace('__TAG__', '', $basename);
         }

         if ( empty($basename) || substr($basename, 1, 2) == '__') $basename = $basen;
         $this->basename = $basename;
      }

      return $this->basename;
   }

   /**
    * Get the Progress data as a JSON string for the specified issue
    *
    * @param $id
    * @return  string    The person name.
    *
    * @since   1.6
    */

   protected function getProgressData($id)
   {
      if ($id) {
         $db = $this->getDbo();
         $query = $db->getQuery(true)
            ->select($db->quoteName(array('lineno', 'progress', 'public', 'state', 'alias')))
            ->from($db->quoteName('#__it_progress'))
            ->where($db->quoteName('issue_id') . '=' . $db->quote($id))
            ->order('lineno ASC');
         $db->setQuery($query);

         try
         {
            $row = $db->loadObjectList();
            if (! empty($row) ) {
               $data = json_encode(array($row));
            } else {
               $data = '';
            }
         }
         catch (RuntimeException $e)
         {
            $data = JText::_('COM_ISSUETRACKER_NOPROGRESSDATA');
            // Should really comment the following out.
            $this->setError($e->getMessage());
            return false;
         }
      } else {
         $data = JText::_('COM_ISSUETRACKER_NOPROGRESSDATA');
      }

      return $data;
   }

   /**
    * Get the status name
    *
    * @return  string    The status name.
    *
    * @since   1.6
    */

   protected function getStatusName()
   {
      $statusid = $this->getState('filter.status_id');

      if ($statusid) {
         $db = $this->getDbo();
         $query = $db->getQuery(true)
            ->select('status_name')
            ->from($db->quoteName('#__it_status'))
            ->where($db->quoteName('id') . '=' . $db->quote($statusid));
         $db->setQuery($query);

         try
         {
            $name = $db->loadResult();
         }
         catch (RuntimeException $e)
         {
            $this->setError($e->getMessage());

            return false;
         }
      } else {
         $name = JText::_('COM_ISSUETRACKER_NOSTATUSNAME');
      }

      return $name;
   }

   /**
    * Get the priority name
    *
    * @return  string    The priority name.
    *
    * @since   1.6
    */

   protected function getPriorityName()
   {
      $priorityid = $this->getState('filter.priority_id');

      if ($priorityid) {
         $db = $this->getDbo();
         $query = $db->getQuery(true)
            ->select('priority_name')
            ->from($db->quoteName('#__it_priority'))
            ->where($db->quoteName('id') . '=' . $db->quote($priorityid));
         $db->setQuery($query);

         try
         {
            $name = $db->loadResult();
         }
         catch (RuntimeException $e)
         {
            $this->setError($e->getMessage());

            return false;
         }
      } else {
         $name = JText::_('COM_ISSUETRACKER_NOPRIORITYNAME');
      }

      return $name;
   }

   /**
    * Get the type name
    *
    * @return  string    The type name.
    *
    * @since   1.6
    */

   protected function getTypeName()
   {
      $typeid = $this->getState('filter.type_id');

      if ($typeid) {
         $db = $this->getDbo();
         $query = $db->getQuery(true)
            ->select('type_name')
            ->from($db->quoteName('#__it_types'))
            ->where($db->quoteName('id') . '=' . $db->quote($typeid));
         $db->setQuery($query);

         try
         {
            $name = $db->loadResult();
         }
         catch (RuntimeException $e)
         {
            $this->setError($e->getMessage());

            return false;
         }
      } else {
         $name = JText::_('COM_ISSUETRACKER_NOTYPENAME');
      }

      return $name;
   }

    /**
     * Get the assigned person name
     *
     * @return  string    The person name.
     *
     * @since   1.6
     */

    protected function getAssignedName()
    {
       $assigned = $this->getState('filter.assigned');

       if ($assigned) {
          $db = $this->getDbo();
          $query = $db->getQuery(true)
             ->select('person_name')
             ->from($db->quoteName('#__it_people'))
             ->where($db->quoteName('user_id') . '=' . $db->quote($assigned));
          $db->setQuery($query);

          try
          {
             $name = $db->loadResult();
          }
          catch (RuntimeException $e)
          {
             $this->setError($e->getMessage());

             return false;
          }
       } else {
          $name = JText::_('COM_ISSUETRACKER_NOPERSONNAME');
       }

       return $name;
    }

    /**
     * Get the identifier person name
     *
     * @return  string    The person name.
     *
     * @since   1.6
     */

    protected function getIdentifierName()
    {
       $identifier = $this->getState('filter.identifier');

       if ($identifier) {
          $db = $this->getDbo();
          $query = $db->getQuery(true)
             ->select('person_name')
             ->from($db->quoteName('#__it_people'))
             ->where($db->quoteName('id') . '=' . $db->quote($identifier));
          $db->setQuery($query);

          try
          {
             $name = $db->loadResult();
          }
          catch (RuntimeException $e)
          {
             $this->setError($e->getMessage());

             return false;
          }
       } else {
          $name = JText::_('COM_ISSUETRACKER_NOPERSONNAME');
       }

       return $name;
    }

   /**
    * Get the file type.
    *
    * @return  string    The file type
    *
    * @since   1.6
    */
   public function getFileType()
   {
      return $this->getState('compressed') ? 'zip' : 'csv';
   }

   /**
    * Get the mime type.
    *
    * @return  string    The mime type.
    *
    * @since   1.6
    */
   public function getMimeType()
   {
      return $this->getState('compressed') ? 'application/zip' : 'text/csv';
   }

   /**
    * Get the content
    *
    * @return  string    The content.
    *
    * @since   1.6
    */
   public function getContent()
   {
      if (!isset($this->content)) {
         $this->content = '';

         $this->content .=
            '"' . str_replace('"', '""', JText::_('JGRID_HEADING_ID')) . '","' .
//            str_replace('"', '""', JText::_('COM_ISSUETRACKER_ASSET_ID')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_ISSUE_NUMBER')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_ISSUE_DESCRIPTION_LABEL')) . '","' .
//             str_replace('"', '""', JText::_('COM_ISSUETRACKER_IDENTIFIED_BY_ID')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_IDENTIFIED_DATE')) . '","' .
//             str_replace('"', '""', JText::_('COM_ISSUETRACKER_PROJECT_ID')) . '","' .
//             str_replace('"', '""', JText::_('COM_ISSUETRACKER_ASSIGNED_PERSON_ID')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_ASSIGNED_TO_PERSON_ID_LABEL')) . '","' .
//             str_replace('"', '""', JText::_('COM_ISSUETRACKER_ISSUE_TYPE_ID')) . '","' .
             str_replace('"', '""', JText::_('COM_ISSUETRACKER_TYPE')) . '","' .
//            str_replace('"', '""', JText::_('JSTATUS')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_STATUS_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_PUBLIC')) . '","' .
            str_replace('"', '""', JText::_('JPUBLISHED')) . '","' .
//            str_replace('"', '""', JText::_('JGRID_HEADING_ORDERING')) . '","' .
//            str_replace('"', '""', JText::_('CHECKED_OUT')) . '","' .
//            str_replace('"', '""', JText::_('COM_ISSUETRACKER_PRIORITY')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_PRIORITY_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_TARGET_RESOLUTION_DATE_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_PROGRESS_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_CLOSE_DATE')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_RESOLUTION_SUMMARY_LABEL')) . '","' .
//            str_replace('"', '""', JText::_('JFIELD_ACCESS_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_CUSTOMFIELDS')) . '","' .
            str_replace('"', '""', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_CREATED_ON_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_CREATED_BY_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_MODIFIED_ON_LABEL')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_FIELD_MODIFIED_BY_LABEL')) . '","' .
//            str_replace('"', '""', JText::_('CHECKED OUT TIME')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_PROJECT_NAME')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_PROJECT_ID')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_IDENTIFYING_PERSON')) . '","' .
            str_replace('"', '""', JText::_('COM_ISSUETRACKER_RANKING')) . '"' . "\n";


         $this->items = IssueTrackerHelper::updateprojectname($this->getItems());

         // foreach ($this->getItems() as $item)
         foreach ($this->items as $item)
         {
            // echo "<pre)";var_dump($item);echo "</pre>";
            $text = 'Unknown';

            switch ($item->state) {
               case 0:
                  $text = JText::_('JUNPUBLISHED');
                  break;
               case 1:
                  $text = JText::_('JPUBLISHED');
                  break;
               case 2:
                  $text = JText::_('JARCHIVED');
                  break;
               case -2:
                  $text = JText::_('JTRASHED');
                  break;
                case '*':
                  $text = JText::_('JALL');
                  break;
           }

            $this->content .=
               '"' . str_replace('"', '""', $item->id) . '","' .
//               str_replace('"', '""', $item->asset_id) . '","' .
               str_replace('"', '""', $item->alias) . '","' .
               str_replace('"', '""', $item->issue_summary) . '","' .
               str_replace('"', '""', $item->issue_description) . '","' .
//               str_replace('"', '""', $item->identified_by_person_id) . '","' .
               str_replace('"', '""', $item->identified_date) . '","' .
//               str_replace('"', '""', $item->related_project_id) . '","' .
//               str_replace('"', '""', $item->assigned_to_person_id) . '","' .
               str_replace('"', '""', $item->person_name) . '","' .
//               str_replace('"', '""', $item->issue_type) . '","' .
               str_replace('"', '""', $item->type_name) . '","' .
//               str_replace('"', '""', $item->status) . '","' .
               str_replace('"', '""', $item->status_name) . '","' .
               str_replace('"', '""', ($item->public == 1 ? JText::_('COM_ISSUETRACKER_PUBLIC_OPTION') : JText::_('COM_ISSUETRACKER_PRIVATE_OPTION'))) . '","' .
//               str_replace('"', '""', $item->state) . '","' .
               str_replace('"', '""', $text) . '","' .
//               str_replace('"', '""', $item->ordering) . '","' .
//               str_replace('"', '""', $item->checked_out) . '","' .
//               str_replace('"', '""', $item->priority) . '","' .
               str_replace('"', '""', $item->priority_name) . '","' .
               str_replace('"', '""', $item->target_resolution_date) . '","' .
//               str_replace('"', '""', $item->progress) . '","' .
               str_replace('"', '""', $this->getProgressData($item->id)) . '","' .
               str_replace('"', '""', $item->actual_resolution_date) . '","' .
               str_replace('"', '""', $item->resolution_summary) . '","' .
//               str_replace('"', '""', $item->access) . '","' .
               str_replace('"', '""', $item->custom_fields) . '","' .
               str_replace('"', '""', $item->metadata) . '","' .
               str_replace('"', '""', $item->created_on) . '","' .
               str_replace('"', '""', $item->created_by) . '","' .
               str_replace('"', '""', $item->modified_on) . '","' .
               str_replace('"', '""', $item->modified_by) . '","' .
//               str_replace('"', '""', $item->checked_out_time) . '","' .
               str_replace('"', '""', $item->project_name) . '","' .
               str_replace('"', '""', $item->project_id) . '","' .
               str_replace('"', '""', $item->identifying_name) . '","' .
               str_replace('"', '""', $item->ranking) . '"' . "\n";
         }

         if ($this->getState('compressed'))
         {
            $app = JFactory::getApplication('administrator');

            $files = array();
            $files['track'] = array();
            $files['track']['name'] = $this->getBasename() . '.csv';
            $files['track']['data'] = $this->content;
            $files['track']['time'] = time();
            // $ziproot = $app->getCfg('tmp_path') . '/' . uniqid('issuetracker_issues_') . '.zip';
            $ziproot = $app->get('tmp_path'). '/' . uniqid('issuetracker_issues_') . '.zip';

            // Run the packager
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');
            // $delete = JFolder::files($app->getCfg('tmp_path') . '/', uniqid('issuetracker_issues_'), false, true);
            $delete = JFolder::files($app->get('tmp_path'). '/' . uniqid('issuetracker_issues_'), false, true);

            if (!empty($delete))
            {
               if (!JFile::delete($delete))
               {
                  // JFile::delete throws an error
                  $this->setError(JText::_('COM_ISSUETRACKER_ERR_ZIP_DELETE_FAILURE'));

                  return false;
               }
            }

            if (!$packager = JArchive::getAdapter('zip'))
            {
               $this->setError(JText::_('COM_ISSUETRACKER_ERR_ZIP_ADAPTER_FAILURE'));

               return false;
            }
            elseif (!$packager->create($ziproot, $files))
            {
               $this->setError(JText::_('COM_ISSUETRACKER_ERR_ZIP_CREATE_FAILURE'));

               return false;
            }

            $this->content = file_get_contents($ziproot);
         }
      }

      return $this->content;
   }
}
