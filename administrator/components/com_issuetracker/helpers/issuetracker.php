<?php
/*
 *
 * @Version       $Id: issuetracker.php 2280 2016-04-24 15:54:22Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.11
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-04-24 16:54:22 +0100 (Sun, 24 Apr 2016) $
 *
 */
defined('_JEXEC') or die( 'Restricted access' );

# Import JMailHelper
JLoader::import('joomla.mail.helper');

// Load log helper
if (! class_exists('IssueTrackerHelperLog')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}
/*
 *
 * Issue Tracker helper.
 *
 */
/**
 * Class IssueTrackerHelper
 */
class IssueTrackerHelper
{
   /** @var array List of URLs to CSS files */
   public static $cssURLs = array();

   /**
    *
    * addSubmenu
    *
    * Configure the Linkbar.
    *
    * @param string $vName
    */
   public static function addSubmenu($vName = '')
   {
      $params = JComponentHelper::getParams('com_issuetracker');
      $attachments = $params->get('enable_attachments', '0');
      $imap_attachments = $params->get('imap_attachments', '0');
      $advaudit = $params->get('enable_adv_audit', '0');

      $jversion = new JVersion();
      if (version_compare($jversion->getShortVersion(), '3.1', 'ge')) {
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_CPANEL'),
            'index.php?option=com_issuetracker',
            $vName == 'cpanel'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_ISSUES'),
            'index.php?option=com_issuetracker&view=itissueslist',
            $vName == 'issues'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_PEOPLE'),
            'index.php?option=com_issuetracker&view=itpeoplelist',
            $vName == 'people'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_PROJECTS'),
            'index.php?option=com_issuetracker&view=itprojectslist',
            $vName == 'projects'
         );
         if ($attachments || $imap_attachments) {
            JHtmlSidebar::addEntry(
               JText::_('COM_ISSUETRACKER_MENU_ATTACHMENTS'),
               'index.php?option=com_issuetracker&view=attachments',
               $vName == 'attachments'
            );
         }
         if ($advaudit && self::check_db_priv('TRIGGER')) {
            JHtmlSidebar::addEntry(
               JText::_('COM_ISSUETRACKER_HISTORY'),
               'index.php?option=com_issuetracker&view=jchanges',
               $vName == 'chistory'
            );
            JHtmlSidebar::addEntry(
               JText::_('COM_ISSUETRACKER_TRIGGERS'),
               'index.php?option=com_issuetracker&view=jtriggers',
               $vName == 'chistory'
            );
         }
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_CUSTOMFIELDS'),
            'index.php?option=com_issuetracker&view=customfields',
            $vName == 'customfields'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_CUSTOMFIELDGROUPS'),
            'index.php?option=com_issuetracker&view=customfieldgroups',
            $vName == 'customfieldgroups'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_DOCUMENTATION'),
            'index.php?option=com_issuetracker&view=documentation',
            $vName == 'documentation'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_EMAILS'),
            'index.php?option=com_issuetracker&view=emails',
            $vName == 'emails'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_DISPLAY_LOG'),
            'index.php?option=com_issuetracker&view=itloglist',
            $vName == 'itloglist'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_PRIORITIES'),
            'index.php?option=com_issuetracker&view=itprioritylist',
            $vName == 'priorities'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_ROLES'),
            'index.php?option=com_issuetracker&view=itroleslist',
            $vName == 'roles'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_STATUSES'),
            'index.php?option=com_issuetracker&view=itstatuslist',
            $vName == 'statuses'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_SUPPORT'),
            'index.php?option=com_issuetracker&view=support',
            $vName == 'support'
         );
         JHtmlSidebar::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_TYPES'),
            'index.php?option=com_issuetracker&view=ittypeslist',
            $vName == 'types'
         );
      } else {
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_CPANEL'),
            'index.php?option=com_issuetracker',
            $vName == 'cpanel'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_ISSUES'),
            'index.php?option=com_issuetracker&view=itissueslist',
            $vName == 'issues'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_PEOPLE'),
            'index.php?option=com_issuetracker&view=itpeoplelist',
            $vName == 'people'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_PROJECTS'),
            'index.php?option=com_issuetracker&view=itprojectslist',
            $vName == 'projects'
         );
         if ($attachments || $imap_attachments) {
            JSubMenuHelper::addEntry(
               JText::_('COM_ISSUETRACKER_MENU_ATTACHMENTS'),
               'index.php?option=com_issuetracker&view=attachments',
               $vName == 'attachments'
            );
         }
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_PRIORITIES'),
            'index.php?option=com_issuetracker&view=itprioritylist',
            $vName == 'priorities'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_ROLES'),
            'index.php?option=com_issuetracker&view=itroleslist',
            $vName == 'roles'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_STATUSES'),
            'index.php?option=com_issuetracker&view=itstatuslist',
            $vName == 'statuses'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_TYPES'),
            'index.php?option=com_issuetracker&view=ittypeslist',
            $vName == 'types'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_EMAILS'),
            'index.php?option=com_issuetracker&view=emails',
            $vName == 'emails'
         );
         if ($advaudit && self::check_db_priv('TRIGGER')) {
            JSubMenuHelper::addEntry(
               JText::_('COM_ISSUETRACKER_HISTORY'),
               'index.php?option=com_issuetracker&view=jchanges',
               $vName == 'chistory'
            );
         }
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_CUSTOMFIELDS'),
            'index.php?option=com_issuetracker&view=customfields',
            $vName == 'customfields'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_CUSTOMFIELDGROUPS'),
            'index.php?option=com_issuetracker&view=customfieldgroups',
            $vName == 'customfieldgroups'
         );

         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_SUPPORT'),
            'index.php?option=com_issuetracker&view=support',
            $vName == 'support'
         );
         JSubMenuHelper::addEntry(
            JText::_('COM_ISSUETRACKER_MENU_DOCUMENTATION'),
            'index.php?option=com_issuetracker&view=documentation',
            $vName == 'documentation'
         );
      }
   }

   /**
    * Build up the full project name
    *
    * @param        $data
    * @param        $tree
    * @param int    $id
    * @param string $text
    * @param        $currentId
    * @return mixed
    */
   public static function ProjectTreeOption($data, $tree, $id = 0, $text = '', $currentId)
   {

      if ($id == 0) {
         $db = JFactory::getDBO();
         $query = "SELECT id FROM `#__it_projects` WHERE title= 'Root' ";
         $db->setQuery($query);
         $rid = $db->loadResult();
      } else {
         $rid = $id;
      }

      foreach ($data as $key) {
         $show_text = $text . $key->text;

         if ($key->parentid == $rid && $currentId != $rid && $currentId != $key->value) {
            $tree[$key->value] = new JObject();
            $tree[$key->value]->text = $show_text;
            $tree[$key->value]->value = $key->value;
            $tree = self::ProjectTreeOption($data, $tree, $key->value, $show_text . " - ", $currentId);
         }
      }
      return ($tree);
   }

   /**
    *
    * Update Project Name
    * Updates the input array so that the full Project name is display
    * which includes the parent project and sub project names.
    *
    * @param $rows
    * @return mixed
    */
   public static function updateprojectname($rows)
   {
      if (empty($rows)) return $rows;

      // This updates an array of arrays
      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
//      $query .= ' WHERE a.state = 1'
//              . ' ORDER BY a.lft';
      $db->setQuery($query);
      $rows2 = $db->loadObjectList();

      $catId = -1;
      $tree = array();
      $text = '';
      $tree = self::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($rows as $key) {
         foreach ($tree as $key2) {
            if ($key->project_id == $key2->value) {
               $key->project_name = $key2->text;
               break;    // Exit inner foreach since we have found our match.
            }
         }
      }
      return $rows;
   }

   /**
    * Update the project name so that it is infull.
    *
    * @param $row
    * @return mixed
    */
   public static function updatepname($row)
   {
      if (empty($row)) return $row;
      // This updates a single array entry
      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
//         . ' WHERE a.state = 1'
//         . ' ORDER BY a.lft';
      $db->setQuery($query);
      $rows2 = $db->loadObjectList();

      $catId = -1;
      $tree = array();
      $text = '';
      $tree = self::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($tree as $key2) {
         if ($row->id == $key2->value) {
            $row->title = $key2->text;
            break;    // Exit inner foreach since we have found our match.
         }
      }
      return $row;
   }

   /**
    * Given a real user->id  get the associated it_people id.
    *
    * @param $user_id
    * @return mixed
    */
   public static function get_itpeople_id($user_id)
   {

      $db = JFactory::getDBO();
      $query = 'SELECT a.id FROM #__it_people AS a WHERE user_id = ' . $user_id;
      $db->setQuery($query);
      $id = $db->loadResult();

      return $id;
   }

   /**
    * Given a person table id get the real user_id if present. Opposite of get_itpeople_id.
    *
    * @param $id
    * @return mixed
    * @internal param $user_id
    */
   public static function get_ituser_id($id)
   {

      $db = JFactory::getDBO();
      $query = 'SELECT a.user_id FROM #__it_people AS a WHERE a.id = ' . $id;
      $db->setQuery($query);
      $user_id = $db->loadResult();

      if ( empty($user_id) ) $user_id = 0;

      return $user_id;
   }

   /**
    * Method to return the project name
    * as a value given a project id.
    * A variation on the above methods.
    *
    * @param $pid
    * @return mixed
    */
   static function getprojname($pid)
   {
      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
      $db->setQuery($query);
      $rows2 = $db->loadObjectList();

      $catId = -1;
      $tree = array();
      $text = '';
      $tree = self::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($tree as $key2) {
         if ($pid == $key2->value) {
            return $key2->text;
            break;    // Exit inner foreach since we have found our match.
         }
      }
      return $pid;
   }

   /**
    *
    * @param $catId
    * @return mixed
    */
   public static function get_filtered_Project_name($catId)
   {
      $db = JFactory::getDBO();

      //build the list of categories
      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid';
      $query .= ' FROM #__it_projects AS a';
      $query .= " WHERE title != 'Root' ";
      $query .= ' ORDER BY a.lft';
      $db->setQuery($query);
      $data = $db->loadObjectList();

      $tree = array();
      $text = '';
      $tree = self::ProjectTreeOption($data, $tree, 0, $text, $catId);

      $query = "SELECT id FROM `#__it_projects` WHERE title = 'Root' ";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      array_unshift($tree, JHTML::_('select.option', $cnt, '- ' . JText::_('COM_ISSUETRACKER_SELECT_PROJECT') . ' -', 'value', 'text'));

      return $tree;
   }

   /**
    *
    * @param int $inchead
    * @return mixed
    */
   public static function getProject_name($inchead = 0)
   {
      $db = JFactory::getDBO();

      //build the list of categories
      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid';
      $query .= ' FROM #__it_projects AS a';
      $query .= ' WHERE state in (0, 1) ';
      $query .= ' ORDER BY a.lft';
      $db->setQuery($query);
      $data = $db->loadObjectList();

      $catId = -1;

      $tree = array();
      $text = '';
      $tree = self::ProjectTreeOption($data, $tree, 0, $text, $catId);

      if ($inchead == 0) {
         array_unshift($tree, JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_PROJECT') . ' -', 'value', 'text'));
      }

      return $tree;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getAssigned_name($inchead = 0)
   {
      $db = JFactory::getDBO();

      $db->setQuery('SELECT `user_id` AS value, `person_name` AS text FROM `#__it_people` ORDER BY user_id');
      $options = array();

      // Add a null value line for those users without assigned projects
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', JText::_('COM_ISSUETRACKER_NONE_ASSIGNED'));
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    * Method to check that the default assignee is a staff member.
    *
    * Input is the user id of the assignee to check.
    *
    * @param $aid
    * @return bool
    */
   public static function check_assignee($aid)
   {
      $db = JFactory::getDBO();
      $db->setQuery('SELECT count(user_id) FROM `#__it_people` WHERE staff = 1 and user_id = ' . $aid);
      $_count = $db->loadResult();

      if ($_count == 0) {
         return false;
      } else {
         return true;
      }
   }

   /**
    *
    * @return array
    */
   public static function getPerson_name()
   {
      $db = JFactory::getDBO();
      // Add email address on name since we may have a name more than once. Only the combination is unique.
      $db->setQuery("SELECT `id` AS value, CONCAT_WS(' - ', person_name, person_email) AS text FROM `#__it_people` ORDER BY person_name");
      $options = array();
      // Add a null value line for those users without assigned projects
      // $options[] = JHTML::_('select.option', '', JText::_('COM_ISSUETRACKER_NONE_ASSIGNED') );

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getAssignedPeople($inchead = 0)
   {
      $db = JFactory::getDBO();
      // $query   = $db->getQuery(true);

      $query = 'select distinct `assigned_to_person_id` AS value, `person_name` AS text ';
      $query .= 'from `#__it_issues` t1 ';
      $query .= 'left join `#__it_people` t2 on t2.user_id = t1.assigned_to_person_id ';
      $query .= ' where assigned_to_person_id IS NOT NULL ';

      $db->setQuery($query);
      $options = array();
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_ASSIGNED') . ' -');
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getIdentifyingPeople($inchead = 0)
   {
      $db = JFactory::getDBO();
      // $query   = $db->getQuery(true);

      $query = 'SELECT DISTINCT `identified_by_person_id` AS value, `person_name` AS text ';
      $query .= 'FROM `#__it_issues` AS t1 ';
      $query .= 'LEFT JOIN `#__it_people` AS t2 on t2.id = t1.identified_by_person_id ';
      $query .= 'WHERE identified_by_person_id IS NOT NULL ';
      $query .= 'ORDER BY person_name';

      $db->setQuery($query);
      $options = array();
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_IDENTIFIER') . ' -');
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }


   /**
    * getProjects
    *
    * Gets a list of projects filtered by the selected projects in the list.
    * Intended for front end project filter on issues list display.
    *
    * @param int $inchead
    * @return array
    */
   public static function getProjects($inchead = 0)
   {
      $db = JFactory::getDBO();
      $query = 'SELECT `title` AS text, `id` AS value, `parent_id` as parentid FROM `#__it_projects` WHERE state = 1 ';
      $query .= 'ORDER BY lft';
      $db->setQuery($query);
      $data = $db->loadObjectList();

      $catId = -1;

      $tree = array();
      $text = '';
      $tree = self::ProjectTreeOption($data, $tree, 0, $text, $catId);

      // Filter the projects based on the selected items in the parameters.
      // Done here since we would have had to add in the parent projects in to get the full name determined.
      $app = JFactory::getApplication();
      $params = $app->getParams();
      $projids = $params->get('project_ids', array());  // It is an array even if there is only one element!
      $out = array();

      // Special case if 'All' was selected.
      if (count($projids) == 1 && in_array('0', $projids, true)) {
         // Fetch all projids directly from db.
         $query = 'SELECT id FROM `#__it_projects` WHERE state = 1 ';
         $query .= 'ORDER BY lft';
         $db->setQuery($query);
         $jversion = new JVersion();
         if (version_compare($jversion->getShortVersion(), '3.1', 'ge')) {
            $projids = $db->loadColumn();
         } else {
            $projids = $db->loadResultArray();
         }
      }

      if (!empty($projids) && $projids[0] != "") {
         // Check if we have these ids in our $tree
         foreach ($projids as $key) {
            foreach ($tree as $key2) {
               if ($key == $key2->value) {
                  $out[] = $key2;
                  break;    // Exit inner foreach since we have found our match.
               }
            }
         }
      }

      if ($inchead == 0) {
         array_unshift($out, JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_PROJECT') . ' -', 'value', 'text'));
      }

      return $out;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getStatuses($inchead = 0)
   {
      $db = JFactory::getDBO();
      $db->setQuery('SELECT `id` AS value, `status_name` AS text FROM `#__it_status` WHERE state = 1 ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_STATUS') . ' -');
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getPriorities($inchead = 0)
   {
      $db = JFactory::getDBO();
      $db->setQuery('SELECT `id` AS value, `priority_name` AS text FROM `#__it_priority` WHERE state = 1 ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_PRIORITY') . ' -');
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getTypes($inchead = 0)
   {
      $db = JFactory::getDBO();

      /* New code for different types per project. */
      $params = JComponentHelper::getParams('com_issuetracker');
      $def_project = $params->get('def_project', '10');

      /*
            // Get value if pid was specified.
            $pid = JFactory::getApplication()->input->get('project_value');
            if (! empty($pid) ) {
               print("Found pid<p>");
               die("Test");
            }
      */

      $query = "SELECT itypes FROM #__it_projects WHERE id = " . $db->Quote($def_project);
      $db->setQuery($query);
      $types = $db->loadResult();
      $types = json_decode($types);  // Get as an array.

      // Get strings for type ids in display.
      $output = null;

      if (count($types) == 0 || $types[0] == 0) {
         $query = "SELECT `id` AS value, `type_name` AS text FROM `#__it_types` WHERE state = 1 ";
      } else {
         $query = "SELECT `id` AS value, `type_name` AS text FROM `#__it_types` WHERE state = 1 AND id IN (" . implode(',', $types) . ") ";
      }
      $query .= " ORDER BY ordering ASC ";

      $db->setQuery($query);
      $options = array();
      // Add a null value line for those users without assigned projects
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_TYPE') . ' -');
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getRoles($inchead = 0)
   {
      $db = JFactory::getDBO();
      $db->setQuery('SELECT `id` AS value, `role_name` AS text FROM `#__it_roles` ORDER BY id');
      $options = array();
      // Add a null value line for those users without assigned projects
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_ROLE') . ' -');
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getLogPriorities($inchead = 0)
   {

      $options = array();

      // Add a null value line
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_PRIORITY') . ' -');
      }
      $options[] = JHTML::_('select.option', 1, JText::_('COM_ISSUETRACKER_LOG_EMERGENCY_LABEL'));
      $options[] = JHTML::_('select.option', 2, JText::_('COM_ISSUETRACKER_LOG_ALERT_LABEL'));
      $options[] = JHTML::_('select.option', 4, JText::_('COM_ISSUETRACKER_LOG_CRITICAL_LABEL'));
      $options[] = JHTML::_('select.option', 8, JText::_('COM_ISSUETRACKER_LOG_ERROR_LABEL'));
      $options[] = JHTML::_('select.option', 16, JText::_('COM_ISSUETRACKER_LOG_WARNING_LABEL'));
      $options[] = JHTML::_('select.option', 32, JText::_('COM_ISSUETRACKER_LOG_NOTICE_LABEL'));
      $options[] = JHTML::_('select.option', 64, JText::_('COM_ISSUETRACKER_LOG_INFO_LABEL'));
      $options[] = JHTML::_('select.option', 128, JText::_('COM_ISSUETRACKER_LOG_DEBUG_LABEL'));

      return $options;
   }

   /**
    *
    * @param int $inchead
    * @return array
    */
   public static function getProjectLevels($inchead = 0)
   {
      $options = array();

      // Add a null value line
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('JOPTION_SELECT_MAX_LEVELS') . ' -');
      }

      $options[] = JHtml::_('select.option', '1', JText::_('J1'));
      $options[] = JHtml::_('select.option', '2', JText::_('J2'));
      $options[] = JHtml::_('select.option', '3', JText::_('J3'));
      $options[] = JHtml::_('select.option', '4', JText::_('J4'));
      $options[] = JHtml::_('select.option', '5', JText::_('J5'));
      $options[] = JHtml::_('select.option', '6', JText::_('J6'));
      $options[] = JHtml::_('select.option', '7', JText::_('J7'));
      $options[] = JHtml::_('select.option', '8', JText::_('J8'));
      $options[] = JHtml::_('select.option', '9', JText::_('J9'));
      $options[] = JHtml::_('select.option', '10', JText::_('J10'));
      return $options;
   }

   /**
    * show all projects
    *
    * @return mixed
    */
   public function showallprojects()
   {
      $db = JFactory::getDBO();
      $query = "SELECT Child.title, Child.lft, Child.rgt ";
      $query .= "FROM `#__it_projects` AS Parent, `#__it_projects` AS Child ";
      $query .= "WHERE Child.lft BETWEEN Parent.lft AND Parent.rgt ";
      $query .= "AND NOT EXISTS ( ";
      $query .= "  SELECT * ";
      $query .= "  FROM `#__it_projects` AS Mid ";
      $query .= "  WHERE Mid.lft BETWEEN Parent.lft AND Parent.rgt ";
      $query .= "  AND Child.lft BETWEEN Mid.lft AND Mid.rgt ";
      $query .= "  AND Mid.title NOT IN (Parent.title AND Child.title) ";
      $query .= ") ";
      $query .= "AND Parent.lft = 0 ";
      $query .= "ORDER by Child.lft";
      $db->setQuery($query);
      $data = $db->loadObjectList();
      return $data;
   }

   /**
    * show project children for a specified project.
    *
    * @param int $pid
    * @return mixed
    */
   public function showprojectchildren($pid = 0)
   {
      $db = JFactory::getDBO();
      $query = "SELECT CONCAT(Parent.title, ' - ', Child.title), Child.lft, Child.rgt ";
      $query .= "FROM `#__it_projects` AS Child, `#__it_projects` AS Parent ";
      $query .= "WHERE Child.level = Parent.level + 1 ";
      $query .= "AND Child.lft > Parent.lft ";
      $query .= "AND Child.rgt < Parent.rgt ";
      $query .= "AND Parent.id = " . (int)$pid;
      $query .= " ORDER by Child.lft";
      $db->setQuery($query);
      $data = $db->loadObjectList();
      return $data;
   }

   /**
    *
    * getActions
    *
    * Gets a list of the actions that can be performed.
    *
    * @since   1.0
    *
    * @param int $messageId
    * @return JObject
    */
   public static function getActions($messageId = 0)
   {
      JLoader::import('joomla.access.access');
      $user = JFactory::getUser();
      $result = new JObject;

      if (empty($messageId)) {
         $assetName = 'com_issuetracker';
      } else {
         $assetName = 'com_issuetracker.itissues.' . (int)$messageId;
      }

      $actions = JAccess::getActionsFromFile(
         JPATH_ADMINISTRATOR . '/components/' . 'com_issuetracker' . '/access.xml');

      foreach ($actions as $action) {
         $result->set($action->name, $user->authorise($action->name, $assetName));
      }

      return $result;
   }

   /**
    *
    * Get a list of filter options for the blocked state of a user.
    *
    * @return  array An array of JHtmlOption elements.
    * @since   1.6
    *
    */
   static function getStateOptions()
   {
      // Build the filter options.
      $options = array();
      $options[] = JHtml::_('select.option', '0', JText::_('JENABLED'));
      $options[] = JHtml::_('select.option', '1', JText::_('JDISABLED'));

      return $options;
   }

   /**
    *
    * @return string
    */
   public static function getComponentUrl()
   {
      return 'administrator/components/com_issuetracker';
   }

   /**
    *
    * A blank option for use in select.genericlist
    *
    * @access private
    * @param array $arr An array of objects
    * @param       $text
    * @param null  $defaultValue
    * @return array An array of objects prependes
    *  with a blank row with 'Select..' in text and 0 in value
    *
    */
   public function addBlankRow($arr, $text, $defaultValue = null)
   {
      if (!$defaultValue) $defaultValue = "0";
      $blank = new stdClass();
      $blank->value = $defaultValue;
      $blank->text = $text;
      $pre = array($blank);
      return array_merge($pre, $arr);
   }

   /**
    *
    * Obtains component version.
    *
    * @return mixed|string
    */
   public function getVersion()
   {
      $db = JFactory::getDBO();
      $sql = "SELECT version FROM " . $db->quoteName('#__it_meta') . " WHERE id='1'";

      $db->setQuery($sql);
      $version = $db->loadResult();

      if (!$version) {
         return "0";
      } else {
         return $version;
      }
   }

   /**
    *
    * Determines if user is on line.
    *
    * @param $id
    * @return bool
    */
   function isUserOnlineById($id)
   {
      $db = JFactory::getDBO();

      $sql = 'SELECT count(*) FROM #__session WHERE userid=' . $db->Quote($id);

      $db->setQuery($sql);
      $_count = $db->loadResult();

      if ($_count == 0) {
         return false;
      } else {
         return true;
      }
   }

   /**
    *
    * Method to determine which messages are to be sent when an issue is created, or changed.
    *
    *  Input:  $data   - array of input record.
    *          $newrec - Flag to indicate whether we are a new record.  Required since we saved record in db already.
    *
    * @param $data
    * @param $newrec
    * @return bool|int
    */
   public static function prepare_messages($data, $newrec)
   {
      // var_dump($data);
      // print ("<p>New record: $newrec   Source: $src ");
      // $app     = JFactory::getApplication();
      $user = JFactory::getUser();

      // get settings from com_issuetracker parameters
      $params = JComponentHelper::getParams('com_issuetracker');

      $notify = $params->get('email_notify', 0); // Should change this param name!
      if ($notify == 0) return 0;            // If we are not using notifications just return.

      // Find out who we are sending to.     Chnage so that we may have values of 0 to 3
      $ass_new_notify = $params->get('send_ass_msg_new', 0);
      $ass_upd_notify = $params->get('send_ass_msg_update', 0);
      // $ass_close_notify    = $params->get('send_ass_msg_close', 0);
      $usr_new_notify = $params->get('send_user_msg_new', 0);
      $usr_upd_notify = $params->get('send_user_msg_update', 0);
      $usr_close_notify = $params->get('send_user_msg_close', 0);
      $adm_new_notify = $params->get('send_adm_msg_new', 0);
      $adm_upd_notify = $params->get('send_adm_msg_update', 0);
      $adm_close_notify = $params->get('send_adm_msg_close', 0);
      // $open_status         = $params->get('open_status', '4');
      $closed_status = $params->get('closed_status', '1');

      $db = JFactory::getDBO();
      $status = $data['status'];   // get state of issue  1= closed, 4 = open  anything else is an update.

      // if a new case, get the real issue id
      if ($newrec) {
         $query = "SELECT id FROM #__it_issues WHERE alias = '" . $data['alias'] . "'";
         $db->setQuery($query);
         $nid = $db->loadResult();
         $data['id'] = $nid;            // Update array for send_mail method.
      } else {
         $nid = $data['id'];
      }
      // print ("<p>Real issue id = $nid ");
      // $def_assignee  = $this->_params->get('def_assignee', '');

      // Logic for issue messages follows

      // Notify user and assignee if the issue is new except where it is closed immediately
      // Notify admin unless admin actually opened the issue.
      if ($newrec) {
         if ($status != $closed_status) {
            if ($usr_new_notify) {
               if ($user->guest) {
                  // Not a registered user - Parse out the username, email and notify fields we require.
                  list($res, $matches) = self::fetch_and_parse_user_details($data['id']);
                  if ($res == 0 || empty($matches)) {
                     $notify = $data['notify'];

                     if ($notify == 1 || $notify == 3)  // Prepare for future possible SMS here as well.
                        self::send_email('user_new', $data['user_details']['email'], $data);   // Notify user
                  } else {
                     if ($res == 1 && $matches[3] == 'Y')  // TODO Change based on notify value.
                        self::send_email('user_new', $matches[2], $data);   // Notify user
                  }
               } else {
                  $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
                  $query .= " FROM #__it_people WHERE id = " . $data['identified_by_person_id'];
                  $db->setQuery($query);
                  $usr_email = $db->loadRow();

                  if ($usr_email[1] == 1 || $usr_email[3] == 1 || (array_key_exists('notify', $data) && $data['notify']))
                     self::send_message('user_new', $usr_email, $data);   // Notify user
                  // self::send_email('user_new', $usr_email[0], $data);   // Notify user
               }
            }

            if ($ass_new_notify && (!empty($data['assigned_to_person_id']))) {
               // Notify Assignee if we didn't open it and assign it to ourselves
               $ident_id = self::get_itpeople_id($data['assigned_to_person_id']);
               if ($data['identified_by_person_id'] != $ident_id) {
                  $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
                  $query .= " FROM #__it_people ";
                  $query .= " WHERE user_id = " . $data['assigned_to_person_id'];
                  $db->setQuery($query);
                  $ass_det = $db->loadRow();
                  if ($ass_det[1] == 1 || $ass_det[3] == 1)
                     self::send_message('ass_new', $ass_det, $data);
               }
            }

            if ($adm_new_notify) {
               self::send_adm_message('admin_new', $data);
            }
         } else if ($status == $closed_status) {
            // Issue being closed or is closed.  Treat this as a special case.
            // If admin is closing it do not notify them
            if ($adm_close_notify) {
               self::send_adm_message('admin_close', $data);
            }

            // If user has requested it notify them of closure
            if ($usr_close_notify) {
               if ($user->guest) {
                  // Not a registered user - Parse out the username, email and notify fields we require.
                  list($res, $matches) = self::fetch_and_parse_user_details($data['id']);

                  if ($res == 0 || empty($matches)) {
                     $notify = $data['notify'];

                     if ($notify == 1) {
                        self::send_email('user_new', $data['user_details']['email'], $data);   // Notify user
                     }
                  } else {
                     if ($res == 1 && $matches[3] == 1)
                        self::send_email('user_new', $matches[2], $data);   // Notify user
                  }
               } else {
                  // $query = "SELECT person_email, email_notifications, user_id FROM #__it_people WHERE id = ".$data['identified_by_person_id'];
                  $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
                  $query .= "FROM #__it_people WHERE id = " . $data['identified_by_person_id'];
                  $db->setQuery($query);
                  $usr_email = $db->loadRow();

                  if ($usr_email[1] == 1 || $usr_email[3] == 1 || (array_key_exists('notify', $data) && $data['notify']))
                     self::send_message('user_close', $usr_email, $data);   // Notify user

               }
            }

            // If assignee is closing it then do not notify them
            if ($ass_new_notify && (!empty($data['assigned_to_person_id']))) {
               if ($user->id != $data['assigned_to_person_id']) {
                  //get assignee details
                  $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
                  $query .= " FROM #__it_people ";
                  $query .= " WHERE user_id = " . $data['assigned_to_person_id'];
                  $db->setQuery($query);
                  $ass_det = $db->loadRow();
                  if ($ass_det[1] == 1 || $ass_det[3] == 1)
                     self::send_message('ass_close', $ass_det, $data);   // Notify user
               }
            }
         }
      } elseif ($status == $closed_status) {
         // Issue being closed or is closed
         // If admin is closing it do not notify them
         if ($adm_close_notify) {
            self::send_adm_message('admin_close', $data);
         }

         // If user has requested it notify them of closure
         if ($usr_close_notify) {
            if ($user->guest) {
               // Not a registered user - Parse out the username, email and notify fields we require.
               list($res, $matches) = self::fetch_and_parse_user_details($data['id']);

               if ($res == 0 || empty($matches)) {
                  $notify = $data['notify'];

                  if ($notify == 1)
                     self::send_email('user_new', $data['user_details']['email'], $data);   // Notify user
               } else {
                  if ($res == 1 && $matches[3] == 1) {
                     self::send_email('user_new', $matches[2], $data);   // Notify user
                  }
               }
            } else {
               $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
               $query .= "FROM #__it_people WHERE id = " . $data['identified_by_person_id'];
               $db->setQuery($query);
               $usr_email = $db->loadRow();

               // TODO Allow for future possibility to allow user to close from front end.
               if ($usr_email[1] == 1 || $usr_email[3] == 1 || (array_key_exists('notify', $data) && $data['notify']))
                  self::send_message('user_close', $usr_email, $data);   // Notify user
            }
         }

         // If assignee is closing it then do not notify them
         if ($ass_new_notify && (!empty($data['assigned_to_person_id']))) {
            if ($user->id != $data['assigned_to_person_id']) {
               //get assignee details
               $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
               $query .= " FROM #__it_people ";
               $query .= " WHERE user_id = " . $data['assigned_to_person_id'];
               $db->setQuery($query);
               $ass_det = $db->loadRow();
               if ($ass_det[1] == 1 || $ass_det[3] == 1)
                  self::send_message('ass_close', $ass_det, $data);   // Notify user
            }
         }
      } else {
         // On an update, notify all admin users, except if an admin user updated it.
         if ($adm_upd_notify) {
            self::send_adm_message('admin_update', $data);
         }

         // On an update notify the user if requested
         if ($usr_upd_notify) {
            if ($user->guest) {
               // Probably should never fall through here!
               // Check if the identifier is the Anonymous user.
               $aid = self::getAnonymousId();
               if ($data['identified_by_person_id'] != $aid && $aid != 0) {
                  $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
                  $query .= "FROM #__it_people WHERE id = " . $data['identified_by_person_id'];
                  $db->setQuery($query);
                  $usr_email = $db->loadRow();

                  if ($usr_email[1] == 1 || $usr_email[3] == 1)
                     self::send_message('user_update', $usr_email, $data);   // Notify user
               } else {
                  // A guest user is performing the update and it was marked as raised by Anonymous
                  // Parse out the username, email and notify fields we require.
                  list($res, $matches) = self::fetch_and_parse_user_details($data['id']);

                  if ($res == 0 || empty($matches)) {
                     // No details in the progress field so double check whether anonymous gets email.

                     $aid = self::getAnonymousId();
                     if ($data['identified_by_person_id'] != $aid && $aid != 0) {
                        $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
                        $query .= "FROM #__it_people WHERE id = " . $data['identified_by_person_id'];
                        $db->setQuery($query);
                        $usr_email = $db->loadRow();

                        if ($usr_email[1] == 1 || $usr_email[3] == 1)
                           self::send_message('user_update', $usr_email, $data);   // Notify user
                     }
                  } else {
                     if ($res == 1 && $matches[3] == 1)
                        self::send_email('user_update', $matches[2], $data);   // Notify user
                  }
               }
            } else {
               // A registered user is performing the update, which means it is either the identifying user or a staff/admin user.
               // Check if the identifier is the Anonymous (Default assignee) user.
               $aid = self::getAnonymousId();
               if ($data['identified_by_person_id'] != $aid && $aid != 0) {
                  $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
                  $query .= "FROM #__it_people WHERE id = " . $data['identified_by_person_id'];
                  $db->setQuery($query);
                  $usr_email = $db->loadRow();

                  if ($usr_upd_notify) {
                     // If raising user is assignee do not notify user.
                     if (!empty($data['assigned_to_person_id'])) {
                        // Notify User only if user and assignee are not the same.
                        $ident_id = self::get_itpeople_id($data['assigned_to_person_id']);

                        if ($data['identified_by_person_id'] != $ident_id && ($usr_email[1] == 1 || $usr_email[3] == 1))
                           self::send_message('user_update', $usr_email, $data);   // Notify user
                     } else {
                        self::send_message('user_update', $usr_email, $data);   // Notify user
                     }
                  }
               } else {
                  // Marked as anonymous user.
                  // Parse out the username, email and notify fields we require.
                  list($res, $matches) = self::fetch_and_parse_user_details($data['id']);

                  if ($res == 1 && $matches[3] == 1) {
                     self::send_email('user_update', $matches[2], $data);   // Notify user
                  } else {
                     // Check if anonymous (default identifier) gets email.
                     $query = "SELECT email_notifications, person_email, user_id ";
                     $query .= " FROM #__it_people WHERE id = " . $db->quote($aid);
                     $db->setQuery($query);
                     $usr_email = $db->loadRow();

                     if ($usr_email[0] == 1)
                        self::send_email('user_update', $usr_email[1], $data);   // Notify anonymous
                  }
               }
            }
         }

         // Notify assignee of updates except if assignee made the change
         if ($ass_upd_notify && !empty($data['assigned_to_person_id'])) {
            if ($user->id != $data['assigned_to_person_id']) {
               //get assignee details
               $query = "SELECT person_email, email_notifications, phone_number, sms_notify, user_id, username ";
               $query .= " FROM #__it_people ";
               $query .= " WHERE user_id = " . $data['assigned_to_person_id'];
               $db->setQuery($query);
               $ass_det = $db->loadRow();
               if ($ass_det[1] == 1 || $ass_det[3] == 1)
                  self::send_message('ass_update', $ass_det, $data);   // Notify user
            }
         }
      }  // end of newrec, update or closed record check.
      return true;
   }

   /**
    * Fetch first progress record from progress table and parse user details from the record.
    *
    * @param $id int The issue id.
    *
    * @return array
    */
   static function fetch_and_parse_user_details($id)
   {
      // Instead of parsing all of the progress records for the issue we only need to check the first.

      $db = JFactory::getDBO();
      $query = 'SELECT progress FROM `#__it_progress` WHERE issue_id = ' . $id . ' AND lineno = 1';
      $db->setQuery($query);
      $progrec = $db->loadResult();

      $res = preg_match("/Reported By:\s(.*)\<br \/\>.*Email:\s(.*)\<br \/\>.*Notify:\s([NY])\<br \/\>([A-Za-z:\s].*)/", $progrec, $matches);
      return array($res, $matches);
   }


   /**
    * Generic routine to add an entry to the progress table.
    *
    * @param $issue_id
    * @param $ntext
    * @param $uname
    * @return bool
    */
   public static function add_progress_change($issue_id, $ntext, $uname = 'admin')
   {
      if (empty($issue_id) || empty($ntext)) return false;

      $db = JFactory::getDBO();
      $query = "SELECT max(lineno)+1, alias, public, state, access FROM `#__it_progress` WHERE issue_id = '" . $issue_id . "'";
      $db->setQuery($query);
      $prow = $db->loadRow();
      $lineno = $prow[0];
      $alias = $prow[1];

      // If no earlier progress rows get issue alias.  TODO Change calling list to pass in alias.
      if (empty($alias) ) {
         $query = "SELECT alias FROM `#__it_issues` WHERE id = '" . $issue_id . "'";
         $db->setQuery($query);
         $alias = $db->loadResult();
      }

      if (empty($lineno) || $lineno == 1 ) {
         $lineno = 1;
         $public = 0;
         $state = 0;
         $access = 2;
      } else {
         $public = $prow[2];
         if (empty($public) || $public == 0) {             // If a private issue.
            $state = 0;
         } else {
            $state = $prow[3];
         }
         $access = $prow[4];
         if ( empty($access) ) $access = 2;
      }

      $query = 'INSERT INTO `#__it_progress` (issue_id, alias, progress, public, state, lineno, access, created_by, created_on) ';
      $query .= "VALUES(" . $issue_id . ",'" . $alias . "','" . $ntext . "'," . $public . "," . $state . "," . $lineno . "," . $access . ",'" . $uname . "', NOW() )";
      $db->setQuery($query);
      $res = $db->execute();
      if (!$res) {
         return false;
      }
      return true;
   }

   /**
    * Generic routine to add an entry to the resolution field of an issue.
    *
    * @param $issue_id
    * @param $ntext
    * @return bool
    */
   public static function add_resolution_change($issue_id, $ntext)
   {
      if (empty($issue_id) || empty($ntext)) return false;

      $db = JFactory::getDBO();
      $query = "SELECT resolution_summary FROM `#__it_issues` WHERE id = '" . $issue_id . "'";
      $db->setQuery($query);
      $restext = $db->loadResult();

      $restext .= '<br />'.$ntext;

      $query = "UPDATE `#__it_issues` SET resolution_summary = '".$restext."' WHERE id = '". $issue_id . "'";
      $db->setQuery($query);
      $res = $db->execute();
      if (!$res) {
         return false;
      }
      return true;
   }

   /**
    *
    * Method to update the strings with substituted values.
    *
    * Note that subject and body are passed by reference so that
    * changes we make in this method apply to the called arrays.
    *
    * The domain is passed in since we want to use this routine from the CLI.
    *
        * Note we now handle the custom fields in a separate routine, NOT in here. 1.6.9+
    *
    * @param $subject
    * @param $body
    * @param $data
    * @param $domain
    * @return bool
    */
   public static function update_strings(&$subject, &$body, $data, $domain)
   {
     // Load admin language so we handle case where we are called from the front end.
      $language = JFactory::getLanguage();
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR, 'en-GB', true);
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR, null, true);

      $db = JFactory::getDBO();

      //set up the front end URL
      $url = $domain;
      if (substr($url, -1) != '/') $url = $url . '/'; //first make sure base URL has '/' at the end
      // $urlb      = 'index.php?option=com_issuetracker&view=itissueslist';  //basic URL parms to get itemid
      $url .= 'index.php?option=com_issuetracker&view=itissues';
      $url .= '&id=' . $data['id']; //problem number
      // $menus      = JApplication::getMenu('site',array());
      // $menuItem   = $menus->getItems( 'link', $urlb, true );
      // $url       .= '&Itemid='.$menuItem->id;              // Add Menu item id
      $url = '<a href="' . $url . '">' . $url . '</a>';      // Add link tags for email

      $subject = str_replace('[url]', $url, $subject);
      $body = str_replace('[url]', $url, $body);

      $subject = str_replace('[issue_id]', $data['alias'], $subject);
      $body = str_replace('[issue_id]', $data['alias'], $body);

      $tags = array("<p>", "</p>");
      $title = str_replace($tags, "", $data['issue_summary']);
      $subject = str_replace('[title]', $title, $subject);
      $body = str_replace('[title]', $title, $body);

      $subject = str_replace('[description]', $data['issue_description'], $subject);
      $body = str_replace('[description]', $data['issue_description'], $body);

      $query = 'SELECT COUNT(*) FROM `#__it_progress` WHERE issue_id = ' . $data['id'];
      $db->setQuery($query);
      $nrows = $db->loadResult();

      $progress = NULL;
      if (!empty($nrows) && $nrows >= 0) {
         // Need to know who the recipient is to determine whether private or public should be send
         // on an update, IF we ever change the sending criteria. For the moment we only send to identifier and staff/admin
         $progress = self::getlastprogressrec($data['id']);
         // $progress = self::getallprogressrec($data['id']);
      } else {
         $progress = JText::_('COM_ISSUETRACKER_EMAIL_PROGRESS_MSG');
      }

      // Check if we have any user details in the progress field
      // This will contain the user details IF the issue was raised on the front end AND if
      // we are configured not to create users in the it_people table.
      //
      // Also applies if we created issues in the front end
      // in version 1.1.0 as well and it is an existing issue.

      // Parse out the username, email and notify fields we require.
      list($res, $matches) = self::fetch_and_parse_user_details($data['id']);

      if ($res == 1) {
         $username = $matches[1];
         $email = $matches[2];

         $subject = str_replace('[user_name]', $username, $subject);
         $body = str_replace('[user_name]', $username, $body);

         $subject = str_replace('[user_email]', $email, $subject);
         $body = str_replace('[user_email]', $email, $body);

         $subject = str_replace('[user_fullname]', $username, $subject);
         $body = str_replace('[user_fullname]', $username, $body);
      } else {
         // Otherwise we assume that the user is recorded and we can get their details from the it_people table.
         $query = "SELECT person_name, person_email, username FROM #__it_people WHERE id = '" . $data['identified_by_person_id'] . "'";
         $db->setQuery($query);
         $prow = $db->loadRow();

         $subject = str_replace('[user_name]', $prow[2], $subject);
         $body = str_replace('[user_name]', $prow[2], $body);

         $subject = str_replace('[user_email]', $prow[1], $subject);
         $body = str_replace('[user_email]', $prow[1], $body);

         $subject = str_replace('[user_fullname]', $prow[0], $subject);
         $body = str_replace('[user_fullname]', $prow[0], $body);
      }

      // Changed_by field.
      if ( strpos( $body, '[changed_by_') != 0 || strpos( $subject, '[changed_by_') != 0 ) {
         $query  = "SELECT IFNULL(i.modified_by,0), IFNULL(i.created_by,0), m.person_name, c.person_name ";
         $query .= "FROM #__it_issues AS i ";
         $query .= "LEFT JOIN #__it_people AS m ON m.username = i.modified_by ";
         $query .= "LEFT JOIN #__it_people AS c ON c.username = i.created_by ";
         $query .= "WHERE i.id = " . $data['id'];
         $db->setQuery($query);
         $names = $db->loadRow();

         if ( strpos( $body, '[changed_by_fullname]') != 0 || strpos( $subject, '[changed_by_fullname]') != 0 ) {
            if ( ! empty($names[2]) && $names[2] != '') {
              $fullname = $names[2];
            } else {
               // Use created_by field instead
               $fullname = $names[3];
            }
            $subject = str_replace('[changed_by_fullname]', $fullname, $subject);
            $body = str_replace('[changed_by_fullname]', $fullname, $body);
         }
         if ( strpos( $body, '[changed_by_username]') != 0 || strpos( $subject, '[changed_by_username]') != 0 ) {
            if ( ! empty($names[0]) && $names[0] != '') {
              $fullname = $names[0];
            } else {
               // Use created_by field instead
               $fullname = $names[1];
            }
            $subject = str_replace('[changed_by_username]', $fullname, $subject);
            $body = str_replace('[changed_by_username]', $fullname, $body);
         }
      }

      // Unlikely to put progress in email subject field.
      //      $subject    = str_replace('[progress]', $progress[0], $subject);
      $body = str_replace('[progress]', $progress, $body);

      // for the project, get the project id and expand out the full name
      $query = "SELECT title, id FROM #__it_projects WHERE id = " . $data['related_project_id'];
      $db->setQuery($query);
      $project = $db->loadRow();
      $pname = self::getprojname($project[1]);
      if ($pname != $project[1]) $project[0] = $pname;

      $subject = str_replace('[project]', $project[0], $subject);
      $body = str_replace('[project]', $project[0], $body);

      //for the priority, get the priority name
      $query = "SELECT priority_name FROM #__it_priority WHERE id = " . $data['priority'];
      $db->setQuery($query);
      $priority = $db->loadResult();

      $subject = str_replace('[priority]', $priority, $subject);
      $body = str_replace('[priority]', $priority, $body);

      //for the status, get the status name
      $query = "SELECT status_name FROM #__it_status WHERE id = " . $data['status'];
      $db->setQuery($query);
      $status = $db->loadResult();

      $subject = str_replace('[status]', $status, $subject);
      $body = str_replace('[status]', $status, $body);

      $ddddd = IssueTrackerHelperDate::dateWithOffSet($data['identified_date']);
      $subject = str_replace('[startdate]', $ddddd, $subject);
      $body = str_replace('[startdate]', $ddddd, $body);
      // $subject = str_replace('[startdate]', $data['identified_date'], $subject);
      // $body = str_replace('[startdate]', $data['identified_date'], $body);

      $ddddd = IssueTrackerHelperDate::dateWithOffSet($data['actual_resolution_date']);
      $subject = str_replace('[closedate]', $ddddd, $subject);
      $body = str_replace('[closedate]', $ddddd, $body);
      // $subject = str_replace('[closedate]', $data['actual_resolution_date'], $subject);
      // $body = str_replace('[closedate]', $data['actual_resolution_date'], $body);

      // get the assignee details
      $query = "SELECT person_name, person_email, username FROM #__it_people WHERE user_id = '" . $data['assigned_to_person_id'] . "'";
      $db->setQuery($query);
      $arow = $db->loadRow();

      if (empty($arow)) {
         $subject = str_replace('[assignee_fullname]', '', $subject);
         $body = str_replace('[assignee_fullname]', '', $body);

         $subject = str_replace('[assignee_email]', '', $subject);
         $body = str_replace('[assignee_email]', '', $body);

         $subject = str_replace('[assignee_uname]', '', $subject);
         $body = str_replace('[assignee_uname]', '', $body);
      } else {
         $subject = str_replace('[assignee_fullname]', $arow[0], $subject);
         $body = str_replace('[assignee_fullname]', $arow[0], $body);

         $subject = str_replace('[assignee_email]', $arow[1], $subject);
         $body = str_replace('[assignee_email]', $arow[1], $body);

         $subject = str_replace('[assignee_uname]', $arow[2], $subject);
         $body = str_replace('[assignee_uname]', $arow[2], $body);
      }

      if (array_key_exists('resolution_summary', $data)) {
         $subject = str_replace('[resolution]', $data['resolution_summary'], $subject);
         $body = str_replace('[resolution]', $data['resolution_summary'], $body);
      } else {
         $subject = str_replace('[resolution]', '', $subject);
         $body = str_replace('[resolution]', '', $body);
      }

      // Check and remove and REPORT tag that may be present, just in case.
      $body = str_replace('[REPORT]', ' ', $body);

      // Add conversion of relative img paths to absolute paths.
      // $domain = JURI::root(); //get site root from Joomla
      preg_match_all('/src\="(.*?)"/im', $body, $matches);
      // Need to ensure that all of the entries in the $matches array are unique otherwise
      // the str_replace function will replace it again on subsequent call.
      $matches = self::super_unique($matches);
      $nos = array_filter($matches);
      $srcstr = "src=\"";  // Add in the src=&#34; string itself otherwise the string without the src=&#34; is also changed.
      if (!empty($nos)) {
         foreach ($matches[1] as $n => $link) {
            if (substr($link, 0, 4) != 'http') {
               //$body = str_replace($matches[1][$n], $domain . $matches[1][$n], $body);
               $body = str_replace($srcstr . $matches[1][$n], $srcstr . $domain . $matches[1][$n], $body);
            }
         }
      }
   }


   /**
    *
    * Method to update the custom field strings with substituted values.
    *
    * Note that subject and body are passed by reference so that
    * changes we make in this method apply to the called arrays.
    *
    * The domain is passed in since we want to use this routine from the CLI.
    *
    * @param $subject
    * @param $body
    * @param $data
    * @param $recipient
    * @return bool
    */
   public static function update_cfields(&$subject, &$body, $data, $recipient)
   {
      // Load admin language so we handle case where we are called from the front end.
      $language = JFactory::getLanguage();
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR, 'en-GB', true);
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR, null, true);

      if (!empty($data['custom_fields'])) {

         $db = JFactory::getDBO();
         if ( $recipient > 0 ) {
            $access = JAccess::getAuthorisedViewLevels($recipient);
            // IssueTrackerHelperLog::log_array($access, JLog::DEBUG);
         }

         $cfields = json_decode($data['custom_fields']);

         // Extract custom fields
         // JLoader::import('customfield', 'JPATH_COMPONENT_ADMINISTRATOR' . '/' . 'models');
         // $cfmodel = JModelLegacy::getInstance('customfield', 'IssuetrackerModel');
         require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'models'.DS.'customfield.php');
         $cfmodel = new IssuetrackerModelCustomField;

         $dmode = 1;
         $pstate = 1;
         // IF we change the astate to 0 (which is what it should be) we get all the fields BUT we then need to filter them based upon the users access groups. Hmmm.
         // Possibly split the CF field substitution in a separately called method also passing in email recipients access groups
         // and then populate the email template?
         // Could create our own CF models just for the email. Probably not required if we do our own checking.
         $astate = 0;   // Get all Custom fields irrespective of access level.
         $customf = $cfmodel->check_customfields($data['related_project_id'], $data['id'], $pstate, $astate, $dmode);

         // Check if any custom field in email template
         $cfcnt = substr_count($body, '[CFIELD');
         if ($cfcnt == 0) {
            // IssueTrackerHelperLog::dblog('No custom field in template body.', JLog::INFO);
         } else {
            // IssueTrackerHelperLog::dblog('Number of custom field in template body '.$cfcnt, JLog::INFO);
            $pos = 0;
            for ($icnt = 1; $icnt <= $cfcnt; $icnt++) {
               // Get id of field
               $pos = strpos($body, '[CFIELD', $pos);
               $pose = strpos($body, ']', $pos + 5);
               $cf_len = $pose - ($pos + 7);
               $cf_id = substr($body, $pos + 7, $cf_len);
               // IssueTrackerHelperLog::dblog('Extracted custom field: '. $pos. ' '. $pose. ' '. $cf_len.' '.$cf_id, JLog::DEBUG);

               // Loop through each field.
               foreach ($customf as $extraField) :
                  // IssueTrackerHelperLog::log_array($extraField, JLog::DEBUG);

                  if ($cf_id == $extraField->id) {
                     // IssueTrackerHelperLog::dblog('Extra Field ['.$cf_id.'] '.$extraField->name.' '.$extraField->element, JLog::DEBUG);
                     if ( $recipient == 0 || ( $recipient > 0 && in_array($extraField->access, $access) ) ) {
                        switch ($extraField->type) {
                           case 'textfield' :
                           case 'date' :
                           case 'textarea' :
                              $sss = $extraField->name . ': ' . $extraField->element;
                              break;
                           case 'header' :
                              $sss = $extraField->name;
                              break;
                           case 'select':
                           case 'multipleSelect':
                             $sss = $extraField->name . ': ' . preg_replace('/<[^>]*>/', ', ', $extraField->element);
                              break;
                           case 'radio':
                           case 'multipleCheckbox':
                              $sss = $extraField->name . ': ' . preg_replace('/<[^>]*>/', ', ', $extraField->element);
                              break;
                           default:
                              $sss = '';
                              IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_CF_FIELDTYPE_NOT_SUPPORT_MSG', $extraField->type), JLog::INFO);
                              break;
                        }
                        $body = str_replace('[CFIELD' . $cf_id . ']', $sss, $body);
                     }
                  }
               endforeach;
               ++$pos;
            }
         }
      }

      // Remove any custom field tags in body that may still be lurking!
      $cfcnt = substr_count($body, '[CFIELD');
      if ($cfcnt > 0) {
         IssueTrackerHelperLog::dblog(JText::plural('COM_ISSUETRACKER_CF_FIELDTYPE_UNRESOLVED_FIELD_MSG', $cfcnt), JLog::INFO);
         $pos = 0;
         for ($icnt = 1; $icnt <= $cfcnt; $icnt++) {
            // Get id of field
            $pos = strpos($body, '[CFIELD', $pos);
            $pose = strpos($body, ']', $pos + 5);
            $cf_len = $pose - ($pos + 7);
            $cf_id = substr($body, $pos + 7, $cf_len);
            $body = str_replace('[CFIELD' . $cf_id . ']', ' ', $body);
         }
      }
   }

   /**
    * Handle multidimensional arrays for unique strings.
    *
    * See: http://stackoverflow.com/questions/3598298/php-remove-duplicate-values-from-multidimensional-array
    *
    * @param $array
    * @return array
    */
   public static function super_unique($array)
   {
      $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
      foreach ($result as $key => $value) {
         if (is_array($value)) {
            $result[$key] = self::super_unique($value);
         }
      }
      return $result;
   }

   /**
    *
    * Generic message sending routine for updating users and assignees
    * about issue status changes.
    * This effectively acts as a interface to both the former email routines and the newer sms routines.
    *
    * The details array contains four elements.
    * 0 = email address
    * 1 = email notifications flag zero or 1.
    * 2 = Phone number
    * 3 = SMS notifications flag zero or 1.
    *
    * @param string $what The type of notification.i.e. Insert, Update etc.
    * @param array  $details An array containing the recipients details.
    * @param array  $data The data to use to extract the message contents.
    * @return bool    Return success or failure.
    */
   public static function send_message($what, $details, $data)
   {
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');
      $notify = $params->get('email_notify', 0);
      $sid = $params->get('sms_senderid', 0);
//      if ( $sid == 0) {
//         if ( $logging )
//            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NOACYSMS_SENDERID_MSG'), JLog::WARNING);
//      }
      $ass_new_notify = $params->get('send_ass_msg_new', 0);
      $ass_upd_notify = $params->get('send_ass_msg_update', 0);
      $ass_close_notify = $params->get('send_ass_msg_close', 0);
      $usr_new_notify = $params->get('send_user_msg_new', 0);
      $usr_upd_notify = $params->get('send_user_msg_update', 0);
      $usr_close_notify = $params->get('send_user_msg_close', 0);
      $ret = true;
      $ret2 = true;
      $email = $details[0];
      $enotify = $details[1];
      $phone = $details[2];
      $snotify = $details[3];
      $email_user_id = $details[4];

      // Call email routine.
      switch ($notify) {
         case 0:
            // Nothing to do, just return!
            break;
         case 1:
            if (!$enotify) return true;
            if (($what == 'ass_update' && in_array($ass_upd_notify, array(1, 3))) ||
               ($what == 'ass_new' && in_array($ass_new_notify, array(1, 3))) ||
               ($what == 'ass_close' && in_array($ass_close_notify, array(1, 3))) ||
               ($what == 'user_update' && in_array($usr_upd_notify, array(1, 3))) ||
               ($what == 'user_new' && in_array($usr_new_notify, array(1, 3))) ||
               ($what == 'user_close' && in_array($usr_close_notify, array(1, 3)))
            ) {
               if (empty($email)) {
                  if ($logging)
                     IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_NO_EMAIL_ADDRESSES_MSG', $data['alias']), JLog::WARNING);
                  return false;
               } else {
                  $ret = self::send_email($what, $email, $data, $email_user_id);
               }
            }
            break;
         case 2:
            if (!$sid) return false;
            if (!$snotify) return true;
            // SMS message sent here.
            if (($what == 'ass_update' && in_array($ass_upd_notify, array(2, 3))) ||
               ($what == 'ass_new' && in_array($ass_new_notify, array(2, 3))) ||
               ($what == 'ass_close' && in_array($ass_close_notify, array(2, 3))) ||
               ($what == 'user_update' && in_array($usr_upd_notify, array(2, 3))) ||
               ($what == 'user_new' && in_array($usr_new_notify, array(2, 3))) ||
               ($what == 'user_close' && in_array($usr_close_notify, array(2, 3)))
            ) {
               if (empty($phone)) {
                  if ($logging)
                     IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_NO_PHONE_MSG', $details[5], $data['alias']), JLog::WARNING);
                  return false;
               } else {
                  $whats = 'sms' . substr($what, strpos($what, '_'));
                  $ret = self::prepare_sms_msg($whats, $phone, $data);
               }
            }
            break;
         case 3:
            if ($enotify) {
               if (($what == 'ass_update' && in_array($ass_upd_notify, array(1, 3))) ||
                  ($what == 'ass_new' && in_array($ass_new_notify, array(1, 3))) ||
                  ($what == 'ass_close' && in_array($ass_close_notify, array(1, 3))) ||
                  ($what == 'user_update' && in_array($usr_upd_notify, array(1, 3))) ||
                  ($what == 'user_new' && in_array($usr_new_notify, array(1, 3))) ||
                  ($what == 'user_close' && in_array($usr_close_notify, array(1, 3)))
               ) {
                  if (empty($email)) {
                     if ($logging)
                        IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_NO_EMAIL_ADDRESSES_MSG', $data['alias']), JLog::WARNING);
                     $ret = false;
                  } else {
                     $ret = self::send_email($what, $email, $data, $email_user_id);
                  }
               }
            }
            if (!$sid) return false;
            if ($snotify) {
               if (($what == 'ass_update' && in_array($ass_upd_notify, array(2, 3))) ||
                  ($what == 'ass_new' && in_array($ass_new_notify, array(2, 3))) ||
                  ($what == 'ass_close' && in_array($ass_close_notify, array(2, 3))) ||
                  ($what == 'user_update' && in_array($usr_upd_notify, array(2, 3))) ||
                  ($what == 'user_new' && in_array($usr_new_notify, array(2, 3))) ||
                  ($what == 'user_close' && in_array($usr_close_notify, array(2, 3)))
               ) {
                  if (empty($phone)) {
                     if ($logging)
                        IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_NO_PHONE_MSG', $details[5], $data['alias']), JLog::WARNING);
                     $ret2 = false;
                  } else {
                     $whats = 'sms' . substr($what, strpos($what, '_'));
                     $ret2 = self::prepare_sms_msg($whats, $phone, $data);
                  }
               }
            }
            if ($ret && $ret2) {
               $ret = true;
            } else {
               $ret = false;
            }
            break;
      }
      return $ret;
   }

   /**
    *
    * Generic message sending routine for updating users and assignees
    * about issue status changes.
    * This effectively acts as a interface to both the former email routines and the newer sms routines.
    *
    * @param string $what The type of notification.i.e. Insert, Update etc.
    * @param array  $data The data to use to extract the message contents.
    * @return bool    Return success or failure.
    */
   public static function send_adm_message($what, $data)
   {
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');
      $notify = $params->get('email_notify', 0);
      $sid = $params->get('sms_senderid', 0);
//      if ( $sid == 0) {
//         if ( $logging )
//            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NOACYSMS_SENDERID_MSG'), JLog::ERROR);
//      }

      $adm_new_notify = $params->get('send_adm_msg_new', 0);
      $adm_upd_notify = $params->get('send_adm_msg_update', 0);
      $adm_close_notify = $params->get('send_adm_msg_close', 0);
      $ret = true;
      $ret2 = true;

      // Call email routine.
      switch ($notify) {
         case 0:
            break;
         case 1:
            if (($what == 'admin_update' && in_array($adm_upd_notify, array(1, 3))) ||
               ($what == 'admin_new' && in_array($adm_new_notify, array(1, 3))) ||
               ($what == 'admin_close' && in_array($adm_close_notify, array(1, 3)))
            )
               $ret = self::send_adm_email($what, $data);
            break;
         case 2:
            if (!$sid) return false;
            if (($what == 'admin_update' && in_array($adm_upd_notify, array(2, 3))) ||
               ($what == 'admin_new' && in_array($adm_new_notify, array(2, 3))) ||
               ($what == 'admin_close' && in_array($adm_close_notify, array(2, 3)))
            ) {
               $whats = 'sms' . substr($what, strpos($what, '_'));
               $ret = self::prepare_adm_sms_msg($what, $data);
            }
            break;
         case 3:
            if (($what == 'admin_update' && in_array($adm_upd_notify, array(1, 3))) ||
               ($what == 'admin_new' && in_array($adm_new_notify, array(1, 3))) ||
               ($what == 'admin_close' && in_array($adm_close_notify, array(1, 3)))
            )
               $ret = self::send_adm_email($what, $data);
            if (!$sid) return false;
            if (($what == 'admin_update' && in_array($adm_upd_notify, array(2, 3))) ||
               ($what == 'admin_new' && in_array($adm_new_notify, array(2, 3))) ||
               ($what == 'admin_close' && in_array($adm_close_notify, array(2, 3)))
            ) {
               $whats = 'sms' . substr($what, strpos($what, '_'));
               $ret2 = self::prepare_adm_sms_msg($whats, $data);
            }
            if ($ret && $ret2) {
               $ret = true;
            } else {
               $ret = false;
            }
            break;
      }
      return $ret;
   }

   /**
    *
    * Generic email sending routine for updating users and assignees
    * about issue status changes.
    * Changed to avoid use of JFactoryApplication since we want to call it from the CLI
    *
    * @param $what
    * @param $to
    * @param $data
    * $param $eid            User id of email recipient.
    * @param int $eid
    * @return bool
    */
   public static function send_email($what, $to, $data, $eid = 0 )
   {
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');

      if (empty($to)) {
         // print ("Input to send_email: $what $to <p>");
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NO_EMAIL_ADDRESS_PROVIDED_MSG'), JLog::WARNING);
         return false;
      }

      // check email address
      if (!JMailHelper::isEmailAddress($to)) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_INVALID_EMAIL_ADDR_MSG', $to), JLog::ERROR);
         return false;
      }

      //get the message subject and body
      $query = "SELECT subject, body FROM #__it_emails WHERE type = '" . $what . "' AND state = 1";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      if (empty($mdetails) || !array_filter($mdetails)) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_TEMPLATE_NOT_FOUND_MSG', $what), JLog::ERROR);
         return false;
      }

      $fromadr = $params->get('emailFrom', '');
      if (!JMailHelper::isEmailAddress($fromadr)) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_INVALID_FROM_ADDR_MSG', $fromadr), JLog::ERROR);
         return false;
      }
      $sender = $params->get('emailSender', '');
      $link = $params->get('emailLink', '');
      $replyto = $params->get('emailReplyto', '');
      $replyname = $params->get('emailReplyname', '');

      $subprefix = $params->get('emailMSGSubject', '');
      $msgprefix = $params->get('emailMSGMessagePrefix', '');
      $msgpostfix = $params->get('emailMSGMessagePostfix', '');

      // set up base for message
      $subject = $mdetails[0];
      $body = $mdetails[1];
      $domain = $params->get('imap_site_base', '');
      if (empty($domain)) $domain = JURI::root();
      // Update the strings
      self::update_strings($subject, $body, $data, $domain);

      // Handle Custom fields here rather than in update strings.
      if ( $eid > 0 ) {
         self::update_cfields($subject, $body, $data, $eid);
      } else {
         // Remove any custom field tags in body that may still be lurking!
         $cfcnt = substr_count($body, '[CFIELD');
         if ($cfcnt > 0) {
            IssueTrackerHelperLog::dblog(JText::plural('COM_ISSUETRACKER_CF_FIELDTYPE_UNRESOLVED_FIELD_MSG', $cfcnt), JLog::INFO);
            $pos = 0;
            for ($icnt = 1; $icnt <= $cfcnt; $icnt++) {
               // Get id of field
               $pos = strpos($body, '[CFIELD', $pos);
               $pose = strpos($body, ']', $pos + 5);
               $cf_len = $pose - ($pos + 7);
               $cf_id = substr($body, $pos + 7, $cf_len);
               $body = str_replace('[CFIELD' . $cf_id . ']', ' ', $body);
            }
         }
      }

      if ($subprefix != "")
         $subject = $subprefix . ' ' . $subject;

      // Add check in here for an update message with no progress details.
      if ($what == 'user_update') {
         // Only need to check if progress is one of the fields in the template.
         $mbody = $mdetails[1];
         $mstr = '[progress]';
         $pos = strpos($mbody, $mstr);
         if ($pos !== false) {
            // Check the last record in the progress table.
            $iid = $data['id'];
            $pok = self::checklastprogressrec($iid);

            if (!$pok) {
               if ($logging)
                  IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_SKIP_PROGRESS_MSG', $data['alias']), JLog::INFO);
               return false;
            }
         }
      }

      $nbody = $msgprefix . $body . $msgpostfix . '<br /><br />' . $sender . '<br />' . $fromadr . '<br />' . $link;

      // Clean the email data
      $subject = JMailHelper::cleanSubject($subject);
      $body = JMailHelper::cleanBody($nbody);
      $fromadr = JMailHelper::cleanAddress($fromadr);

      //setup the mailer & create message
      $mail = JFactory::getMailer();
      $mail->isHTML(true);
      $mail->Encoding = 'base64';
      $mail->addRecipient($to);
      //$mail->setSender($sender);
      $mail->setSender(array($fromadr, $sender));
      // $mail->setFrom($fromadr,$sender,false);
      // Add custom headers
      $mail->AddCustomHeader('X-IT-IssueId: ' . $data['id']);
      $mail->AddCustomHeader('X-IT-IssueAlias: ' . $data['alias']);

      // if (!empty($replyto)) $mail->addReplyTo(array($replyto, $replyname));
      if (!empty($replyto)) $mail->addReplyTo($replyto, $replyname);
      $mail->setSubject($subject);
      $mail->setBody($body);

      if (!$mail->Send()) {
         // echo "<pre>"; var_dump ($mail); echo "</pre>";
         return false;   // if there was trouble, return false for error checking in the caller
      } else {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_EMAIL_SEND_MSG', $what, $to, $data['alias']));
      }
      unset($mail);
      return true;
   }


   /**
    *
    * Method to send email to issue administrators
    * Similar to send_email method only we may have multiple issue administrators to inform.
    *
    * @param string $what
    * @param        $data
    * @return bool
    */
   public static function send_adm_email($what, $data)
   {
      $app = JFactory::getApplication();
      // get settings from com_issuetracker parameters
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');

      // Get number of issue admins.  If only one and we are the user working on the issue just return,
      // otherwise send emails to all other admins.
      $user = JFactory::getUser();
      $is_admin = self::isIssueAdmin($user->id);

      if ($is_admin == 1) {
         $query  = "SELECT count(*) FROM #__it_people WHERE issues_admin = 1 ";
         $query .= " AND email_notifications = 1";
         $query .= " AND user_id != ".$user->id;
         $db = JFactory::getDBO();
         $db->setQuery($query);
         $admincnt = $db->loadResult();
         if ( $admincnt == 0 ) return;
      }

      //get the message subject and body
      $query = "SELECT subject, body FROM #__it_emails WHERE type = '" . $what . "' AND state = 1 ";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      // $SiteName   = $params->get('emailSiteName', '');
      $fromadr = $params->get('emailFrom', '');
      $sender = $params->get('emailSender', '');
      // $link       = $params->get('emailLink', '');
      $replyto = $params->get('emailReplyto', '');
      $replyname = $params->get('emailReplyname', '');
      $subprefix = $params->get('emailADMSubject', '');

      // set up base for message
      $subject = $mdetails[0];
      $body = $mdetails[1];
      $domain = $params->get('imap_site_base', '');
      if (empty($domain)) $domain = JURI::root();

      // Update the strings
      self::update_strings($subject, $body, $data, $domain);

      if ($subprefix != "")
         $subject = $subprefix . ' ' . $subject;

      // Clean the email data
      $subject = JMailHelper::cleanSubject($subject);
      $body = JMailHelper::cleanBody($body);
      $fromadr = JMailHelper::cleanAddress($fromadr);

      // var_dump($subject);
      // var_dump($body);

      // get all administrators with email notifications set
      $query = "SELECT p.username, p.person_email, p.user_id FROM " . $db->quoteName('#__it_people') . " p " .
         " WHERE p.issues_admin = 1 AND p.email_notifications = 1";
      if ($is_admin == 1) {
         $query .= " AND p.user_id != ".$user->id;
      }

      $db->setQuery($query);
      $_administrator_list = $db->loadAssocList();

      if (empty($_administrator_list)) {
         if ($app->isAdmin() && $logging)
            IssueTrackerHelperLog::dblog(JText::_("COM_ISSUETRACKER_WARNING_NO_ISSUE_ADMINISTRATORS"), JLog::INFO);
         return true;
      }

      // For efficiency we build up the administrator recipient list so we only send one email.
      // However we cannot do this if we are using custom fields with specific access rule.

      // Look for any custom fields we may have.
      $cfcnt = substr_count($body, '[CFIELD');
      if ($cfcnt > 0) {
         $kbody = $body;
         while (list($key, $val) = each($_administrator_list)) {
            $eid   = $_administrator_list[$key]['user_id'];
            $email = $_administrator_list[$key]['person_email'];

            $body = $kbody;  // Get original template body
            $recipient = array();  // Reset array

            // Handle Custom fields here rather than in update strings.
            if ( $eid > 0 ) {
               self::update_cfields($subject, $body, $data, $eid);
            }
            // Remove any custom field tags in body that may still be lurking!
            $cfcnt = substr_count($body, '[CFIELD');
            if ($cfcnt > 0) {
               IssueTrackerHelperLog::dblog(JText::plural('COM_ISSUETRACKER_CF_FIELDTYPE_UNRESOLVED_FIELD_MSG', $cfcnt), JLog::INFO);
               $pos = 0;
               for ($icnt = 1; $icnt <= $cfcnt; $icnt++) {
                  // Get id of field
                  $pos = strpos($body, '[CFIELD', $pos);
                  $pose = strpos($body, ']', $pos + 5);
                  $cf_len = $pose - ($pos + 7);
                  $cf_id = substr($body, $pos + 7, $cf_len);
                  $body = str_replace('[CFIELD' . $cf_id . ']', ' ', $body);
               }
            }

            if (JMailHelper::isEmailAddress($email)) {
               $recipient[] = $email;
            }

            if ( empty($recipient) ) {
               IssueTrackerHelperLog::dblog('Admin Mail Address ' . $email . ' is determined to be invalid. Email sending skipped.',JLog::ERROR);
            } else {
               // Now send email to recipient.
               $mail = JFactory::getMailer();
               $mail->isHTML(true);
               $mail->Encoding = 'base64';
               $mail->ClearAllRecipients();       //clean the to array
               $mail->addRecipient($recipient);
               // if (!empty($replyto)) $mail->addReplyTo(array($replyto, $replyname));
               if (!empty($replyto)) $mail->addReplyTo($replyto, $replyname);
               // $mail->setSender($sender);
               $mail->setSender(array($fromadr, $sender));
               // $mail->setFrom($fromadr,$sender,false);
               // Add custom headers
               $mail->AddCustomHeader('X-IT-IssueId: ' . $data['id']);
               $mail->AddCustomHeader('X-IT-IssueAlias: ' . $data['alias']);

               $mail->setSubject($subject);
               $mail->setBody($body);

               if (!$mail->Send()) {
                  // echo "<pre>"; var_dump ($mail); echo "</pre>";
                  // die ("In send email ");
                  // return false;   // if there was trouble, return false for error checking in the caller.
                  IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_CANNOT_SEND_ADM_MAIL_MSG', $email), JLog::INFO);
               } else {
                  if ($logging)
                     IssueTrackerHelperLog::dblog('Sending Admin Mail: ' . $what . ' Issue: ' . $data['alias'].' to '. $email);
               }
            }
         }
      } else {
         // No custom fields found

         if ($logging)
            IssueTrackerHelperLog::dblog('Sending Admin Mail: ' . $what . ' Issue: ' . $data['alias']);

         $recipient = array();
         reset($_administrator_list);
         while (list($key, $val) = each($_administrator_list)) {
            // $username = $_administrator_list[$key]['username'];
            $email = $_administrator_list[$key]['person_email'];
            if (JMailHelper::isEmailAddress($email)) {
               $recipient[] = $email;
            }
         }

         $mail = JFactory::getMailer();
         $mail->isHTML(true);
         $mail->Encoding = 'base64';
         $mail->addRecipient($recipient);
         // if (!empty($replyto)) $mail->addReplyTo(array($replyto, $replyname));
         if (!empty($replyto)) $mail->addReplyTo($replyto, $replyname);
         // $mail->setSender($sender);
         $mail->setSender(array($fromadr, $sender));
         // $mail->setFrom($fromadr,$sender,false);
         // Add custom headers
         $mail->AddCustomHeader('X-IT-IssueId: ' . $data['id']);
         $mail->AddCustomHeader('X-IT-IssueAlias: ' . $data['alias']);

         $mail->setSubject($subject);
         $mail->setBody($body);

         if (!$mail->Send()) {
            return false;   // if there was trouble, return false for error checking in the caller
         }
      }
      return true;
   }


   /**
    *
    * Method to send email to other interested parties
    * Similar to send_email method only we may be sending to multiple interested parties.
    *
    * @param $what
    * @param $data
    * @return bool
    */
   public static function send_int_email($what, $data)
   {
      // $app  = JFactory::getApplication();
      // get settings from com_issuetracker parameters
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');

      //get the message subject and body
      $query = "SELECT subject, body FROM #__it_emails WHERE type = '" . $what . "' AND state = 1 ";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      // $SiteName   = $params->get('emailSiteName', '');
      $fromadr = $params->get('emailFrom', '');
      $sender = $params->get('emailSender', '');
      // $link       = $params->get('emailLink', '');
      $replyto = $params->get('emailReplyto', '');
      $replyname = $params->get('emailReplyname', '');
      $subprefix = $params->get('emailADMSubject', '');

      // set up base for message
      $subject = $mdetails[0];
      $body = $mdetails[1];
      $domain = $params->get('imap_site_base', '');
      if (empty($domain)) $domain = JURI::root();

      // Update the strings
      self::update_strings($subject, $body, $data, $domain);

      if ($subprefix != "")
         $subject = $subprefix . ' ' . $subject;

      // Clean the email data
      $subject = JMailHelper::cleanSubject($subject);
      $body = JMailHelper::cleanBody($body);
      $fromadr = JMailHelper::cleanAddress($fromadr);

      // var_dump($subject);
      // var_dump($body);

      // TODO define how we are to get the interested parties.
      // We need a method so that the interested parties may be associated with a specific issue
      // or a specific project.

      // get all interested parties with email notifications set
      $query = "SELECT p.username, p.person_email FROM " . $db->quoteName('#__it_people') . " p " .
         " WHERE p.issues_admin = 1 AND p.email_notifications = 1";

      $db->setQuery($query);
      $_interested_list = $db->loadAssocList();

      if (empty($_interested_list)) {
         return true;
      }

      if ($logging)
         IssueTrackerHelperLog::dblog('Sending Interested Parties Mail: ' . $what . ' Issue: ' . $data['alias']);

      // For efficiency build up the recipient list so we only send one email.
      $recipient = array();
      reset($_interested_list);
      while (list($key, $val) = each($_interested_list)) {
         // $username = $_interested_list[$key]['username'];
         $email = $_interested_list[$key]['person_email'];
         if (JMailHelper::isEmailAddress($email)) {
            $recipient[] = $email;
         }
      }

      $mail = JFactory::getMailer();
      $mail->isHTML(true);
      $mail->Encoding = 'base64';
      $mail->addRecipient($recipient);
      // if (!empty($replyto)) $mail->addReplyTo(array($replyto, $replyname));
      if (!empty($replyto)) $mail->addReplyTo($replyto, $replyname);
      // $mail->setSender($sender);
      $mail->setSender(array($fromadr, $sender));
      // $mail->setFrom($fromadr,$sender,false);
      // Add custom headers
      $mail->AddCustomHeader('X-IT-IssueId: ' . $data['id']);
      $mail->AddCustomHeader('X-IT-IssueAlias: ' . $data['alias']);

      $mail->setSubject($subject);
      $mail->setBody($body);

      if (!$mail->Send()) {
         // echo "<pre>"; var_dump ($mail); echo "</pre>";
         // die ("In send email ");
         return false;   // if there was trouble, return false for error checking in the caller
      }
      return true;
   }

   /**
    * @param $id
    * @return mixed|string
    */
   function getUsernameById($id)
   {
      $db = JFactory::getDBO();
      $sql = "SELECT username FROM " . $db->quoteName('#__users') . " WHERE id=" . $db->Quote($id);
      $db->setQuery($sql);
      $username = $db->loadResult();

      if (!$username) {
         return "-";
      } else {
         return $username;
      }
   }

   /**
    *
    * Method to determine whether the user is an issue administrator
    *
    * @param $id
    * @return mixed
    */
   public static function isIssueAdmin($id)
   {
      $db = JFactory::getDBO();
      $sql = "SELECT issues_admin FROM " . $db->quoteName('#__it_people') . " WHERE user_id=" . $db->Quote($id);
      $db->setQuery($sql);
      $isadmin = $db->loadResult();

      return $isadmin;
   }

   /**
    *
    * Method to determine whether the user is an member of Staff
    *
    * @param $id
    * @return mixed
    */
   public static function isIssueStaff($id)
   {
      $db = JFactory::getDBO();
      $sql = "SELECT staff FROM " . $db->quoteName('#__it_people') . " WHERE user_id=" . $db->Quote($id);
      $db->setQuery($sql);
      $isadmin = $db->loadResult();

      return $isadmin;
   }

   /**
    *
    * Method to determine whether email notifications are required
    *
    * @param $id
    * @return mixed
    */
   function EmailNotify($id)
   {
      $db = JFactory::getDBO();
      $sql = "SELECT email_notifications FROM " . $db->quoteName('#__it_people') . " WHERE user_id=" . $db->Quote($id);
      $db->setQuery($sql);
      $notify = $db->loadResult();

      return $notify;
   }


   /**
    *
    * Applies the content tag filters to arbitrary text as per settings for current user group
    *
    * @param $text
    * @internal param $text The string to filter
    * @return string The filtered string
    */
   public static function filterText($text)
   {
      // Filter settings
      JLoader::import('joomla.application.component.helper');
      $config = JComponentHelper::getParams('com_issuetracker');
      $user = JFactory::getUser();
      $userGroups = JAccess::getGroupsByUser($user->get('id'));

      $filters = $config->get('filters');
      $blackListTags = array();
      $blackListAttributes = array();
      $whiteListTags = array();
      $whiteListAttributes = array();

      // $noHtml     = false;
      $whiteList = false;
      $blackList = false;
      $unfiltered = false;

      // Cycle through each of the user groups the user is in.
      // Remember they are include in the Public group as well.
      foreach ($userGroups AS $groupId) {
         // May have added a group by not saved the filters.
         if (!isset($filters->$groupId)) {
            continue;
         }

         // Each group the user is in could have different filtering properties.
         $filterData = $filters->$groupId;
         $filterType = strtoupper($filterData->filter_type);

         if ($filterType == 'NONE') {
            // No HTML filtering.
            $unfiltered = true;
         } else {
            // Black or white list.
            // Preprocess the tags and attributes.
            $tags = explode(',', $filterData->filter_tags);
            $attributes = explode(',', $filterData->filter_attributes);
            $tempTags = array();
            $tempAttributes = array();

            foreach ($tags AS $tag) {
               $tag = trim($tag);
               if ($tag) {
                  $tempTags[] = $tag;
               }
            }

            foreach ($attributes AS $attribute) {
               $attribute = trim($attribute);
               if ($attribute) {
                  $tempAttributes[] = $attribute;
               }
            }

            // Collect the black or white list tags and attributes.
            // Each list is cumulative.
            if ($filterType == 'BL') {
               $blackList = true;
               $blackListTags = array_merge($blackListTags, $tempTags);
               $blackListAttributes = array_merge($blackListAttributes, $tempAttributes);
            } else if ($filterType == 'WL') {
               $whiteList = true;
               $whiteListTags = array_merge($whiteListTags, $tempTags);
               $whiteListAttributes = array_merge($whiteListAttributes, $tempAttributes);
            }
         }
      }

      // Remove duplicates before processing (because the black list uses both sets of arrays).
      $blackListTags = array_unique($blackListTags);
      $blackListAttributes = array_unique($blackListAttributes);
      $whiteListTags = array_unique($whiteListTags);
      $whiteListAttributes = array_unique($whiteListAttributes);

      // Unfiltered assumes first priority.
      if ($unfiltered) {
         // Dont apply filtering.
      } else {
         // Black lists take second precedence.
         if ($blackList) {
            // Remove the white-listed attributes from the black-list.
            $filter = JFilterInput::getInstance(
               array_diff($blackListTags, $whiteListTags),        // blacklisted tags
               array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
               1,                                        // blacklist tags
               1                                         // blacklist attributes
            );
         } else if ($whiteList) {
            // White lists take third precedence.
            $filter = JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
         } else {
            // No HTML takes last place.
            $filter = JFilterInput::getInstance();
         }
         $text = $filter->clean($text, 'html');
      }
      return $text;
   }

   /**
    * Adds an arbitrary CSS file.
    *
    * @param $path string The path to the file, in the format media://path/to/file
    */
   public static function addCSSfile($path)
   {
      self::$cssURLs[] = self::parsePath($path);
   }

   /**
    * Method to add a css file.
    *
    * @param $path
    */
   public static function addCSS($path)
   {
      $url = self::parsePath($path);
      JFactory::getDocument()->addStyleSheet($url);
   }

   /**
    * Parse a fancy path definition into a path relative to the site's root,
    * respecting template overrides, suitable for inclusion of media files.
    * For example, media://com_foobar/css/test.css is parsed into
    * media/com_foobar/css/test.css if no override is found, or
    * templates/mytemplate/media/com_foobar/css/test.css if the current
    * template is called mytemplate and there's a media override for it.
    *
    * The valid protocols are:
    * media://    The media directory or a media override
    * admin://    Path relative to administrator directory (no overrides)
    * site://     Path relative to site's root (no overrides)
    *
    * @param string $path Fancy path
    * @return string Parsed path
    */
   public static function parsePath($path)
   {
      $protoAndPath = explode('://', $path, 2);
      if (count($protoAndPath) < 2) {
         $protocol = 'media';
      } else {
         $protocol = $protoAndPath[0];
         $path = $protoAndPath[1];
      }

      $url = JURI::root();

      switch ($protocol) {
         case 'media':
            // Do we have a media override in the template?
            $pathAndParams = explode('?', $path, 2);
            $altPath = JPATH_BASE . '/templates/' . JFactory::getApplication()->getTemplate() . '/media/' . $pathAndParams[0];
            if (file_exists($altPath)) {
               $isAdmin = version_compare(JVERSION, '1.6.0', 'ge') ? (!JFactory::$application ? false : JFactory::getApplication()->isAdmin()) : JFactory::getApplication()->isAdmin();
               $url .= $isAdmin ? 'administrator/' : '';
               $url .= 'templates/' . JFactory::getApplication()->getTemplate() . '/media/';
            } else {
               $url .= 'media/';
            }
            break;
         case 'admin':
            $url .= 'administrator/';
            break;
         default:
         case 'site':
            break;
      }

      $url .= $path;

      return $url;
   }

   /**
    * method to check database privileges
    * @param $priv
    * @return mixed
    */
   public static function check_db_priv($priv)
   {
      $db = JFactory::getDbo();

      $config = JFactory::getConfig();
      // $dbname = $config->get('db');
      $dbuser = $config->get('user');
      // $host    = $config->get('host');   // Ignore host for now.
      $cstring = "'" . $dbuser . "'@'";
      // GRANTEE is stored a single quotes around the dbuser and host and the @ sign in between.

      $query = " SELECT MAX(CNT) FROM ( ";
      $query .= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.USER_PRIVILEGES";
      $query .= " WHERE PRIVILEGE_TYPE ='" . $priv . "'";
      $query .= ' AND GRANTEE LIKE "' . $cstring . '%"';
      $query .= "UNION ALL ";
      $query .= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.SCHEMA_PRIVILEGES";
      $query .= " WHERE PRIVILEGE_TYPE ='" . $priv . "'";
      $query .= ' AND GRANTEE LIKE "' . $cstring . '%"';
      $query .= ") AS A ";
      $db->setQuery($query);
      $res = $db->loadResult();

      // Extra checks for log_bin setting turned on, SUPER privilege and log_bin_trust_function_creators setting.
      if ($priv != 'CREATE VIEW') {
         // Only applies to TRIGGER and ROUTINE get log_bin setting.
         $query = "SELECT variable_value from INFORMATION_SCHEMA.GLOBAL_VARIABLES WHERE VARIABLE_NAME ='LOG_BIN'";
         $db->setQuery($query);
         $res = $db->loadResult();

         // If binary logging off we do not need any further checks.
         if ($res != 'OFF') {
            // Check if trust setting used.
            $query = "SELECT variable_value from INFORMATION_SCHEMA.GLOBAL_VARIABLES WHERE VARIABLE_NAME = 'LOG_BIN_TRUST_FUNCTION_CREATORS'";
            $db->setQuery($query);
            $trust = $db->loadResult();

            // Check if SUPER privilege granted.
            $query = " SELECT MAX(CNT) FROM ( ";
            $query .= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.USER_PRIVILEGES";
            $query .= " WHERE PRIVILEGE_TYPE ='SUPER'";
            $query .= ' AND GRANTEE LIKE "' . $cstring . '%"';
            $query .= " UNION ALL ";
            $query .= "SELECT COUNT(DISTINCT PRIVILEGE_TYPE) AS CNT FROM INFORMATION_SCHEMA.SCHEMA_PRIVILEGES";
            $query .= " WHERE PRIVILEGE_TYPE ='SUPER'";
            $query .= ' AND GRANTEE LIKE "' . $cstring . '%"';
            $query .= ") AS A ";
            $db->setQuery($query);
            $res = $db->loadResult();

            if ($res || $trust != 'OFF') {
               $res = true;
            } else {
               $res = false;
            }
         }
      }

      return $res;
   }

   /**
    * method to check existance of database procedure
    * @param null $proc
    * @return mixed
    */
   public static function check_proc_exists($proc = null)
   {
      $db = JFactory::getDbo();

      // Check if view exists and if it does use it.
      $prefix = $db->getPrefix();
      $config = JFactory::getConfig();
      $tschema = $config->get('db');

      if (empty($proc))
         $proc = 'add_it_sample_data';

      if (substr($proc, 0, 3) == '#__')
         $proc = substr($proc, 3);

      $query1 = "SELECT 1 from INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = '" . $tschema . "' AND ROUTINE_NAME = '" . $prefix . $proc . "'";
      $db->setQuery($query1);
      $res = $db->loadResult();
      return $res;
   }

   /**
    * Method to get details of Anonymous user id. (default identifier)
    * @return integer $aid if found 0 otherwise
    */
   public static function getAnonymousId()
   {
      // Set up access to parameters
      $params = JComponentHelper::getParams('com_issuetracker');
      $def_identby = $params->get('def_identifiedby', '0');
      return $def_identby;
   }


   /**
    * Method to get details of the default Assignee id for a given project.
    *
    * If a project id is specified, then get the default assignee from the projects table
    * for the specified project, otherwise just return the component default.
    *
    * @param  int $pid Project Id.
    * @return integer $aid if found 0 otherwise
    */
   public static function getDefassignee($pid)
   {
      if (!empty($pid) && $pid != 0) {
         $db = JFactory::getDbo();
         // Load the data
         $query = "SELECT assignee FROM `#__it_projects` WHERE `id` = " . $pid;
         $db->setQuery($query);
         $aid = $db->loadResult();
      }

      if (empty($aid) || $aid == 0) {
         $params = JComponentHelper::getParams('com_issuetracker');
         $aid = $params->get('def_assignee', 0);
      }

      return $aid;
   }

   /**
    * generateNewAlias  Generates a ten character issue alias used for issues.
    *
    * @param int    $len The length of the generated string.
    * @param string $fchar The leading character for the generated string.
    * @param int    $method Method to use for string generation.
    *
    * @return  string
    *
    */
   public static function generateNewAlias($len = 10, $fchar = 'Z', $method = 0)
   {
      $str = null;
      $db = JFactory::getDBO();

      switch ($method) {
         case 0:
            // Possible seeds
            $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';

            list($usec, $sec) = explode(' ', microtime());
            $seed = (float)$sec + ((float)$usec * 100000);
            mt_srand($seed);

            // Start all front end issues with the letter Z
            $str = $fchar;
            $seeds_count = strlen($seeds);
            $length = $len - 1;
            for ($i = 0; $length > $i; $i++) {
               $str .= $seeds{mt_rand(0, $seeds_count - 1)};
            }
            break;
         case 1:
            // Build an alias of a leading character followed by zeros and then the number.
            $query = "SELECT max(id)+1 FROM `#__it_issues` ";
            $db->setQuery($query);
            $iid = $db->loadResult();

            if (empty($iid)) $iid = 1;

            $str = $fchar;
            $str .= str_pad($iid, 9, '0', STR_PAD_LEFT);
            break;
         case 2:
            // Numeric string padded to right with blanks.
            $query = "SELECT max(id)+1 FROM `#__it_issues` ";
            $db->setQuery($query);
            $iid = $db->loadResult();

            if (empty($iid)) $iid = 1;

            $str = str_pad($iid, 10, ' ', STR_PAD_RIGHT);
            break;
      }
      return $str;
   }

   /**
    * checkAlias  Checks and if required updates the ten character issue alias used for issues.
    *
    * @param int    $rid The unique id for the issue.
    * @param string $alias The current alias for the issue.
    * @param int    $method Method to use for string generation.
    * @param int    $len The length of the generated string.
    *
    * @return  string
    *
    */
   public static function checkAlias($rid = 0, $alias = '', $len = 10, $method = 0)
   {
      if ($rid == 0 || empty($alias)) return null;

      if ($method == 0) return $alias;
      $db = JFactory::getDBO();
      $nalias = $alias;

      // Commit to avoid a cache error on some MySQL versions.
      $db->setQuery('COMMIT');
      $db->execute();

      switch ($method) {
         case 1:
            $nlen = $len - 1;
            $aid = (int)substr($alias, 1, $len);
            if ($rid != $aid) {
               $nalias = substr($alias, 0, 1) . str_pad($rid, $nlen, '0', STR_PAD_LEFT);
               $query = "UPDATE `#__it_issues` SET alias = '" . $nalias . "' WHERE id = " . $rid;
               $db->setQuery($query);
               $db->execute();
            }
            break;
         case 2:
            $aid = (int)$alias;
            if ($rid != $aid) {
               $nalias = str_pad($rid, $len, ' ', STR_PAD_RIGHT);
               $query = "UPDATE `#__it_issues` SET alias = '" . $nalias . "' WHERE id = " . $rid;
               $db->setQuery($query);
               $db->execute();
            }
            break;
      }
      return $nalias;
   }


   /**
    * Gets a list of the tables being monitored.
    *
    * @param int $inchead
    * @return  JObject
    * @since   1.6
    */
   public static function getTablename($inchead = 0)
   {
      $db = JFactory::getDBO();
      $db->setQuery('SELECT distinct `table_name` AS value, `table_name` AS text FROM `#__it_chistory` ORDER BY table_name');
      $options = array();
      // Add an optional null value line if requested
      if ($inchead == 0) {
         $options[] = JHTML::_('select.option', '', '- ' . JText::_('COM_ISSUETRACKER_SELECT_TABLE') . ' -');
      }

      foreach ($db->loadObjectList() as $r) {
         $options[] = JHTML::_('select.option', $r->value, $r->text);
      }
      return $options;
   }

   /**
    * Get the last progress record from the table for the specified issue.
    * Called from update_strings routine.
    *
    * @param int $issue_id
    * @return  JObject
    * @since   1.6
    */
   public static function getlastprogressrec($issue_id)
   {
      // TODO add check for the users access groups instead of hard coding.
      // Get the last line no and its details
      // Get published or unpublished record, access levels for public and registered only.
      $db = JFactory::getDBO();
      $query = "SELECT lineno, progress, created_by, created_on ";
      $query .= "FROM `#__it_progress` ";
      $query .= " WHERE issue_id = '" . $issue_id . "'";
      $query .= " AND state IN (0,1) ";
      $query .= " AND access IN (1, 2) ";
      $query .= " ORDER BY lineno DESC ";
      $query .= " LIMIT 1";
      $db->setQuery($query);
      $prow = $db->loadObjectList();

      if (empty($prow)) {
         return null;
      } else {
         $ddddd = IssueTrackerHelperDate::dateWithOffSet($prow[0]->created_on);
         // $progress = $prow[0]->created_by . ': ' . $prow[0]->created_on . '<br />' . $prow[0]->progress . '<br />';
         $progress = $prow[0]->created_by . ': ' . $ddddd . '<br />' . $prow[0]->progress . '<br />';
      }
      return $progress;
   }


   /**
    * Get all of the progress records from the table for the specified issue.
    * Was called from update_strings routine.
    *
    * @param int $issue_id
    * @return  JObject
    * @since   1.6
    */
   public static function getallprogressrec($issue_id)
   {
      // TODO add check for the users access groups instead of hard coding.
      // Need to fetch records from the progress table.
      // Get published and unpublished records, access levels for public and registered only.
      $db = JFactory::getDBO();
      $query = 'SELECT lineno, progress, created_by, created_on FROM `#__it_progress`';
      $query .= " WHERE issue_id = '" . $issue_id . "'";
      $query .= " AND state IN (0,1) ";
//         $query .= " AND public = 1 ";
      $query .= " AND access IN (1, 2) ";
      $query .= " ORDER BY lineno ASC";
      $db->setQuery($query);
      $prows = $db->loadObjectList();

      $progress = '';

      foreach ($prows as $prow) {
         // Format each row for the message.
         $progress .= $prow->created_by . ': ' . $prow->created_on . '<br />' . $prow->progress . '<hr>';
      }
      return $progress;
   }


   /**
    * Check last record in progress table to see if it a 'restricted' record.
    * i.e Not in an access group that is Public or Registered.
    *
    * Called from send_email routine.
    *
    * @param int $issue_id
    * @return  JObject
    * @since   1.6
    */
   public static function checklastprogressrec($issue_id)
   {
      // Get the last line no and its details
      // Get published or unpublished record, access levels for public and registered only.
      $db = JFactory::getDBO();
      $query = "SELECT lineno, progress, access ";
      $query .= "FROM `#__it_progress` ";
      $query .= " WHERE issue_id = '" . $issue_id . "'";
      $query .= " ORDER BY lineno DESC ";
      $query .= " LIMIT 1";
      $db->setQuery($query);
      $prow = $db->loadObjectList();

      if ($prow[0]->access != 1 && $prow[0]->access != 2) {
         return false;
      }
      return true;
   }

   /**
    * Routine to return the access groups for a given user email address.
    *
    * @param string $email_addr
    * @return  JObject
    * @since   1.6
    */
   public static function getaccessgroups($email_addr)
   {
      // Get the last line no and its details
      // Get published or unpublished record, access levels for public and registered only.
      $db = JFactory::getDBO();
      $query = "SELECT group_id ";
      $query .= "FROM `#__it_people` AS p ";
      $query .= "LEFT JOIN `#_user_usergroup_map AS g ON g.user_id = p.user_id ";
      $query .= "WHERE person_email = '" . $email_addr . "'";
      $db->setQuery($query);
      $pgroups = $db->loadColumn();

      if (empty($pgroups)) {
         // Return public and registered groups only if none specified.
         $pgroups = array(1, 2);
      }
      return $pgroups;
   }


   /**
    * Routine to send an SMS message.
    *
    * @param string $msg The message to send
    * @param string $pno The phone number to send the message to.
    * @return  JObject
    * @since   1.6
    */
   public static function send_sms($msg, $pno)
   {
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');

      if (!include_once(rtrim(JPATH_ADMINISTRATOR, DS) . DS . 'components' . DS . 'com_acysms' . DS . 'helpers' . DS . 'helper.php')) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NOACYSMS_MSG'), JLog::ERROR);
         return false;
      }

      $senderProfileClass = ACYSMS::get('class.senderprofile');
      $sid = $params->get('sms_senderid', 0);
      if ($sid == 0) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NOACYSMS_SENDERID_MSG'), JLog::ERROR);
         return false;
      }
      $gateway = $senderProfileClass->getGateway($sid);

      if (!$gateway->open()) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_ACYSMS_GATEWAY_MSG'), JLog::ERROR);
         return false;
      }

      // TODO can remove this when Interface is complete.
      $phoneHelper = ACYSMS::get('helper.phone');
      $phone = $phoneHelper->getValidNum($pno);
      if (!$phone) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_INVALID_PHONE_NO_MSG', $phone), JLog::ERROR);
         echo 'Invalid phone number';
         return false;
      }

      $status = $gateway->send($msg, $phone);
      if (!$status) {
         // echo implode('<br />', $gateway->errors);
         if ($logging) {
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_INVALID_SMS_CODE_MSG', $phone), JLog::ERROR);
            IssueTrackerHelperLog::log_array($gateway->errors, JLog::INFO);
         }
      }
      $gateway->close();

      return true;
   }

   /**
    *
    * Generic SMS sending routine for updating users and assignees
    * about issue status changes.
    * Changed to avoid use of JFactoryApplication since we want to call it from the CLI
    *
    * @param $what
    * @param $phone
    * @param $data
    * @return bool
    */
   public static function prepare_sms_msg($what, $phone, $data)
   {
      // get settings from com_issuetracker parameters
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');
      $sms_maxsize = $params->get('sms_maxsize', '160');

      if (!include_once(rtrim(JPATH_ADMINISTRATOR, DS) . DS . 'components' . DS . 'com_acysms' . DS . 'helpers' . DS . 'helper.php')) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NOACYSMS_MSG'), JLog::ERROR);
         return false;
      }

      if (empty($phone)) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NO_PHONE_PROVIDED_MSG'), JLog::WARNING);
         return false;
      }

      $phoneHelper = ACYSMS::get('helper.phone');
      $phone = $phoneHelper->getValidNum($phone);
      if (!$phone) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_INVALID_PHONE_NO_MSG', $phone), JLog::ERROR);
         return false;
      }

      //get the message subject and body
      $query = "SELECT subject, body FROM #__it_emails WHERE type = '" . $what . "' AND state = 1";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      if (empty($mdetails) || !array_filter($mdetails)) {
         if ($logging)
            IssueTrackerHelperLog::dblog('Message template ' . $what . ' not found in database.', JLog::ERROR);
         return false;
      }

      // set up base for message
      $subject = $mdetails[0];
      $body = $mdetails[1];
      $domain = $params->get('imap_site_base', '');
      if (empty($domain)) $domain = JURI::root();
      // Update the strings
      self::update_strings($subject, $body, $data, $domain);

      // Unlikely to place Progress records in SMS message because of size limits.
      // Add check in here for an update message with no progress details.
      /*
      if ( $what == 'user_update' ) {
         $mbody = $mdetails[1];
         $mstr  = '[progress]';
         $pos   = strpos($mbody, $mstr);
         if ( $pos !== false ) {
            $iid = $data['id'];
            $pok = self::checklastprogressrec($iid);
            if ( ! $pok ) {
               if ( $logging )
                  IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_SKIP_PROGRESS_MSG',$data['alias']),JLog::INFO);
               return false;
            }
         }
      }
      */

      // Append body onto subject and Clean the message data
      $body = JMailHelper::cleanBody($subject . $body);
      $body = trim($body);
      $blen = strlen($body);
      if ($blen > $sms_maxsize) {
         $body = substr($body, 0, $sms_maxsize);
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf("COM_ISSUETRACKER_SMS_MESSAGE_SIZE_MSG", $blen, $sms_maxsize, $body), JLog::WARNING);
      }

      $status = self::send_sms($body, $phone);
      if (!$status) {
         // Error while sending
         return false;   // if there was trouble, return false for error checking in the caller
      } else {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_SMS_MSG_LOG_MSG', $what, $phone, $data['alias']));
      }
      return true;
   }

   /**
    *
    * Method to send email to issue administrators
    * Similar to send_email method only we may have multiple issue administrators to inform.
    *
    * @param $what
    * @param $data
    * @return bool
    */
   public static function prepare_adm_sms_msg($what, $data)
   {
      $app = JFactory::getApplication();
      $params = JComponentHelper::getParams('com_issuetracker');
      $logging = $params->get('enablelogging', '0');
      $sms_maxsize = $params->get('sms_maxsize', '160');

      if (!include_once(rtrim(JPATH_ADMINISTRATOR, DS) . DS . 'components' . DS . 'com_acysms' . DS . 'helpers' . DS . 'helper.php')) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NOACYSMS_MSG'), JLog::ERROR);
         return false;
      }
      $phoneHelper = ACYSMS::get('helper.phone');

      //get the message subject and body
      $query = "SELECT subject, body FROM #__it_emails WHERE type = '" . $what . "' AND state = 1 ";
      $db = JFactory::getDBO();
      $db->setQuery($query);
      $mdetails = $db->loadRow();

      // set up base for message
      $subject = $mdetails[0];
      $body = $mdetails[1];
      $domain = $params->get('imap_site_base', '');
      if (empty($domain)) $domain = JURI::root();

      // Update the strings
      self::update_strings($subject, $body, $data, $domain);

      // Clean the message data
      $body = JMailHelper::cleanBody($subject . $body);
      $body = trim($body);
      $blen = strlen($body);
      if ($blen > $sms_maxsize) {
         $body = substr($body, 0, $sms_maxsize);
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_SMS_MESSAGE_SIZE_MSG', $blen, $sms_maxsize, $body), JLog::WARNING);
      }

      // get all administrators with sms notifications set
      $query = "SELECT username, phone_number FROM " . $db->quoteName('#__it_people');
      $query .= " WHERE issues_admin = 1 AND sms_notify = 1";
      $db->setQuery($query);
      $_administrator_list = $db->loadAssocList();

      if (empty($_administrator_list)) {
         if ($app->isAdmin() && $logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_WARNING_NO_ISSUE_ADMINISTRATORS'), JLog::INFO);
         return true;
      }

      if ($logging)
         IssueTrackerHelperLog::dblog('Sending Admin SMS Message: ' . $what . ' Issue: ' . $data['alias']);

      // For each administrator send one message.
      // $recipient = array();
      reset($_administrator_list);
      while (list($key, $val) = each($_administrator_list)) {
         $phone = $_administrator_list[$key]['phone_number'];
         $phone = $phoneHelper->getValidNum($phone);
         if (!$phone) {
            if ($logging)
               IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_INVALID_PHONE_NO_MSG', $phone), JLog::ERROR);
         } else {
            $status = self::send_sms($body, $phone);
            if (!$status) {
               // Error while sending
               return false;   // if there was trouble, return false for error checking in the caller
            } else {
               if ($logging)
                  IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_SMS_MSG_LOG_MSG', $what, $phone, $data['alias']));
            }
         }
      }
      return true;
   }

   /**
    *
    * Method to determine whether SMS notifications are required
    *
    * @param int $id The unique id of the person in the it_people table.  NOT the user_id!
    * @return mixed
    */
   function SMSNotify($id)
   {
      $db = JFactory::getDBO();
      $sql = "SELECT sms_notify FROM " . $db->quoteName('#__it_people') . " WHERE id=" . $db->quote($id);
      $db->setQuery($sql);
      $notify = $db->loadResult();

      return $notify;
   }

   /**
    * Get the list of SMS sender ids from the AcySMS table.
    *
    * @param int $inchead
    * @return array
    */
   public static function getSMS_senderid($inchead = 0)
   {
      $db = JFactory::getDBO();
      $prefix = $db->getPrefix();

      // Check whether AcySMS is installed.
      $query = "SELECT count(*) FROM information_schema.tables WHERE table_name = '" . $prefix . "acysms_senderprofile' LIMIT 1";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      $db->setQuery('SELECT `senderprofile_id` AS value, `senderprofile_name` AS text FROM `#__acysms_senderprofile` ORDER BY senderprofile_id');
      $options = array();

      if ($cnt == 1) {
         if ($inchead == 0) {
            $options[] = JHTML::_('select.option', '', JText::_('COM_ISSUETRACKER_NONE_ASSIGNED'));
         }

         foreach ($db->loadObjectList() as $r) {
            $options[] = JHTML::_('select.option', $r->value, $r->text);
         }
      } else {
         $options[] = JHTML::_('select.option', '', JText::_('COM_ISSUETRACKER_NO_SMS_CONFIGURED'));
      }
      return $options;
   }

   /**
    * Check if a component is installed.
    *
    * @param string $comp
    * @return array
    */
   public static function comp_installed($comp)
   {
      $db = JFactory::getDbo();
      $db->setQuery("SELECT enabled FROM #__extensions WHERE element = '" . $comp . "'");
      $is_enabled = $db->loadResult();

      return $is_enabled;
   }

   /**
    *
    * Method to prepare an email for an autoclosed record.
    * Caled from the auto_close cron task.
    *
    * @param int $id The unique id of the issue being autoclosed.
    *
    * @return bool|int
    */
   public static function send_auto_close_msg($id)
   {
      if ($id == 0) return;  // Nothing to do.

      $db = JFactory::getDBO();

      // get settings from com_issuetracker parameters
      $params = JComponentHelper::getParams('com_issuetracker');
      $closed_status = $params->get('closed_status', '1');
      $logging = $params->get('enablelogging', '0');
      $def_identby = $params->get('def_identifiedby', '0');

      $query = "SELECT * ";
      $query .= "FROM `#__it_issues` ";
      $query .= "WHERE id = " . $db->q($id);
      $db->setQuery($query);
      $data = $db->loadAssoc();

      // Check that the issue is closed. If not return.
      if ($data['status'] != $closed_status) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::sprintf('COM_ISSUETRACKER_ATTEMPT_AUTO_MSG', $data['alias']), JLog::WARNING);
         return;
      }

      // Ensure we have an auto_close message, return if not or it is not published.
      $query = "SELECT count(*) FROM #__it_emails WHERE type = 'auto_close' AND state = 1 ";
      $db->setQuery($query);
      $mok = $db->loadResult();

      if (!$mok) {
         if ($logging)
            IssueTrackerHelperLog::dblog(JText::_('COM_ISSUETRACKER_NO_AUTO_EXISTS_MSG'), JLog::WARNING);
         return;
      }

      // Check if user is registered by querying the people table registered flag.
      $query = "SELECT registered, person_email, email_notifications, phone_number, sms_notify, user_id, username ";
      $query .= " FROM `#__it_people` WHERE id = " . $data['identified_by_person_id'];
      $db->setQuery($query);
      $usr_email = $db->loadRow();

      if (!$usr_email[0]) {
         // Not a registered user - Parse out the username, email and notify fields we require.
         list($res, $matches) = self::fetch_and_parse_user_details($data['id']);
         if ($res == 0 || empty($matches)) {
            $notify = $data['notify'];
            if ($notify == 1 || $notify == 3)  // Prepare for future possible SMS here as well.
               self::send_email('auto_close', $data['user_details']['email'], $data);   // Notify user
         }
      } else {
         if ($usr_email[2] == 1 || (array_key_exists('notify', $data) && $data['notify']))
            self::send_email('auto_close', $usr_email[1], $data);   // Notify user
      }
   }

}