<?php
/*
 * @Version       $Id: itissues.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.10
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.modelitem');
JLoader::import('joomla.application.component.helper'); // Import component helper library

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

if (! class_exists('IssueTrackerHelperLog')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

if (! class_exists('Akismet')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'classes'.DS.'Akismet.php');
}

/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssuetrackerModelItissues extends JModelItem
{
   /**
    * Model context string.
    *
    * @var     string
    */
   protected $_context = 'com_issuetracker.itissues';
   protected $_params;

   /**
    * Itissues data array for tmp store
    *
    * @var array
    */

    /**
     * Constructor
     *
     */
   function __construct()
   {
       parent::__construct();
   }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return  void
     * @since   1.6
     */
   protected function populateState()
   {
      $app = JFactory::getApplication('site');

      // Load state from the request.
      $pk = JFactory::getApplication()->input->get('id');
      $this->setState('itissues.id', $pk);

      $offset = JFactory::getApplication()->input->get('limitstart');
      $this->setState('list.offset', $offset);

      // Get value if pid was specified.
      $pid = JFactory::getApplication()->input->get('project_value');
      $this->setState('project_value', $pid);

      // Load the parameters.
      $params = $app->getParams();
      $this->setState('params', $params);

      $this->setState('filter.access', true);
   }

   /**
     * Method to build the query.
     *
     * Returns the query
     * @param $id
     * @return string The query to be used to retrieve the rows from the database
     */
   private function _buildQuery($id)
   {
      // Create a new query object.
      $db      = $this->getDbo();
      $query   = $db->getQuery(true);
      $query->select(
         $this->getState(
         'list.select',
         't1.*'
         )
      );

      $query->from('#__it_issues AS t1');

      // Join over the it_projects table.
      $query->select('t2.title AS project_name, t2.id AS project_id, t2.access AS project_access');
      $query->join('LEFT', '#__it_projects AS t2 ON t2.id = t1.related_project_id');

      // Join over the it_people table.
      // $query->select('t3.person_name AS assigned_person_name');
      $query->select("IFNULL(t3.person_name,'".JText::_('JNONE')."') AS assigned_person_name");
      $query->join('LEFT', '#__it_people AS t3 ON t3.user_id = t1.assigned_to_person_id');

      // Join over the it_people table.
      $query->select('t4.person_name AS identified_person_name');
      $query->join('LEFT', '#__it_people AS t4 ON t4.id = t1.identified_by_person_id');

      // Join over the it_status table.
      $query->select('t5.status_name AS status_name');
      $query->join('LEFT', '#__it_status AS t5 ON t5.id = t1.status');

      // Join over the it_priority table.
      $query->select('t6.priority_name AS priority_name');
      $query->join('LEFT', '#__it_priority AS t6 ON t6.id = t1.priority');

      // Join over the it_priority table.
      $query->select('t7.type_name AS type_name');
      $query->join('LEFT', '#__it_types AS t7 ON t7.id = t1.issue_type');

      $query = $query . $this->_buildQueryWhere($id);

      return $query;
   }


   /**
    * Method to build the where clause of the query.
    *
    * @param $id
    * @return string
    */
   private function _buildQueryWhere($id)
   {
      // $app      = JFactory::getApplication();
      $sess    = JFactory::getSession();
      $chk_new = $sess->get( 'display_issue', 0 );
      $user    = JFactory::getUser();

      // We use the session variable to control the display of the issue after a guest has
      // created an issue.
      // If we are an issue admin or staff also permit access.
      if ( ! $user->guest ) {
         $isadmin = IssueTrackerHelper::isIssueAdmin($user->id);
         $isstaff = IssueTrackerHelper::isIssueStaff($user->id);
      } else {
         $isadmin = 0;
         $isstaff = 0;
      }

      if ( ( $isadmin || $isstaff ) || $chk_new == 1 || ! $user->guest ) {
         $where = " WHERE ( t1.`id` = {$id} ) ";
         // And reset variable so that subsequent calls do not find an issue.
         $sess->set('display_issue',0);
         return $where;
      } else {
         $where = " WHERE (( t1.`state`=1 ) AND ( t1.`id` = {$id} )) ";
      }

      if ( $user->guest) {
         // If guest and chk_new set do not check published state.
         if ( $chk_new == 0 )
            $where .= " AND public = 1 ";
      } else  {
         // If Registered user we can see public or their own private issues.
         if ( ! (($isadmin || $isstaff) || $chk_new == 1) ) {
            // Refined to check the it_person id not the user_id.
            $person_id = IssueTrackerHelper::get_itpeople_id($user->id);
            $where .= " AND (public = 1 OR ( public = 0 AND t1.identified_by_person_id = ".$person_id.")) ";
         }
      }

      // Filter by access level.
      if ($access = $this->getState('filter.access')) {
         // $user = JFactory::getUser();
         $groups  = implode(',', $user->getAuthorisedViewLevels());
         $where .= ' AND t1.access IN ('.$groups.')';
         $where .= ' AND t2.access IN ('.$groups.')';
      }

      return $where;
   }

   /**
    * Retrieves the data
    * @return array Array of objects containing the data from the database
    *
    * @param null $pk
    * @return object
    * @throws Exception
    */
   public function getItem ($pk = null)
   {
      // Initialise variables.
      $pk = (!empty($pk)) ? $pk : (int) $this->getState('itissues.id');

      if ($this->_item === null) {
        $this->_item = array();
      }

      if (!isset($this->_item[$pk])) {
         try {
            $db      = $this->getDbo();
            $query   = $this->_buildQuery($pk);
            $db->setQuery($query);

            $data = $db->loadObject();

            if ($error = $db->getErrorMsg()) {
               throw new Exception($error);
            }

            if (empty($data)) {
               //Let view handle issue not found.
               return null;
               // return JError::raiseError(404,JText::_('COM_ISSUETRACKER_ISSUE_NOT_FOUND'));
            }

            $this->updatepname($data);

            // Convert parameter fields to objects.
            $registry = new JRegistry;
 //           $registry->loadString($data->attribs);

            $data->params = clone $this->getState('params');
            $data->params->merge($registry);

            $registry = new JRegistry;
            $registry->loadString($data->metadata);
            $data->metadata = $registry;

            // Compute selected asset permissions.
            $user = JFactory::getUser();

            // Technically guest could edit an issue, but lets not check that to improve performance a little.
            if (!$user->get('guest')) {
               $userId  = $user->get('id');
               $asset   = 'com_issuetracker.itissues.'.$data->id;

               // Check general edit permission first.
               if ($user->authorise('core.edit', $asset)) {
                  $data->params->set('access-edit', true);
               }
               // Now check if edit.own is available.
               elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                  // Check for a valid user and that they are the owner.
                  // Note that the issue created by is a string not an id.
                  // if ($userId == $data->created_by) {
                  if ($user->username == $data->created_by ) {
                     $data->params->set('access-edit', true);
                  }
                  $person_id = IssueTrackerHelper::get_itpeople_id($user->id);
                  if ($person_id == $data->identified_by_person_id ) {
                     $data->params->set('access-edit', true);
                  }
               // Now add check if issue admin
               elseif ( IssueTrackerHelper::isIssueAdmin($userId)  || IssueTrackerHelper::isIssueStaff($userId) ) {
                  $data->params->set('access-edit', true);
                  }
               }

               // Check for closed issues being reopened.  Issue admin are exempt.
               if ( !IssueTrackerHelper::isIssueAdmin($userId) ) {
                  $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
                  $allow_reopen  = $this->_params->get('allow_reopen', 0);
                  $closed_status = $this->_params->get('closed_status', '1');
                  if ( $allow_reopen == 0 && $closed_status == $data->status ) {
                     $data->params->set('access-edit',false);
                  }
               }
            }

            // Compute view access permissions.
            if ($access = $this->getState('filter.access')) {
               // If the access filter has been set, we already know this user can view.
               $data->params->set('access-view', true);
            } else {
               // If no access filter is set, the layout takes some responsibility for display of limited information.
               $user = JFactory::getUser();
               $groups = $user->getAuthorisedViewLevels();

               // if ($data->project_id == 0 || $this->_data->project_access === null) {
               if ($data->project_id == 0 || $data->project_access === null) {
                  $data->params->set('access-view', in_array($data->access, $groups));
               } else {
                  $data->params->set('access-view', in_array($data->access, $groups) && in_array($data->project_access, $groups));
               }
            }
            $this->_item[$pk] = $data;
         }

         catch (JException $e)
         {
            if ($e->getCode() == 404) {
               // Need to go thru the error handler to allow Redirect to work.
               JError::raiseError(404, $e->getMessage());
            } else {
               $this->setError($e);
               $this->_item[$pk] = false;
            }
         }

         $jversion = new JVersion();
         if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
            $data->tags = new JHelperTags;
            $data->tags->getTagIds($data->id, 'com_issuetracker.itissue');
         }
      }

      return $this->_item[$pk];
   }

   // Variation on helper file to expand out project name for an issue.
   /**
    * Method to update the full project name.
    *
    * @param $row
    * @return mixed
    */
   function updatepname( $row )
   {
      // This updates a single array entry
      $db = JFactory::getDBO();
      // Now need to merge in to get the full project name.

      $query = 'SELECT a.title AS text, a.id AS value, a.parent_id as parentid'
         . ' FROM #__it_projects AS a';
      $db->setQuery( $query );
      $rows2 = $db->loadObjectList();

      $catId   = -1;
      $tree    = array();
      $text    = '';
      $tree    = IssueTrackerHelper::ProjectTreeOption($rows2, $tree, 0, $text, $catId);

      foreach ($tree as $key2) {
         if ($row->project_id == $key2->value) {
            $row->project_name = $key2->text;
            break;    // Exit inner foreach since we have found out match.
         }
      }
      return $row;
   }

   /**
    * Method to set up the record defaults.
    *
    * @return int The modified title
    *
    * @param $data
    */
   private function _setdefaults( & $data )
   {
      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $def_published = $this->_params->get('def_published', 0);
      $def_assignee  = $this->_params->get('def_assignee', 1);
      $def_project   = $this->_params->get('def_project', 10);
      $def_type      = $this->_params->get('def_type', 1);
      $privacy       = $this->_params->get('allow_private_issues');
      $def_privacy   = $this->_params->get('def_privacy', 1);
      $def_priority  = $this->_params->get('def_priority', 2);  // Low
      $def_status    = $this->_params->get('def_status', 4);   // Open
      $notify        = $this->_params->get('email_notify', 0);
      $fchar         = $this->_params->get('initial_site', 'Z');
      $open_status   = $this->_params->get('open_status', '4');
      $closed_status = $this->_params->get('closed_status', '1');
      $def_identby   = $this->_params->get('def_identifiedby','0');
      $iformat       = $this->_params->get('iformat', '0');

      // Check default assignee.  Set to null if not a staff member.
      if ( ! IssueTrackerHelper::check_assignee($def_assignee) )
         $def_assignee = NULL;

      // Set up our audit fields.   Created audit fields are set up in the it_issues view.
      $user = JFactory::getUser();
      $date = JFactory::getDate();

      // Determine whether insert or an update
      if ($data['id'] ==  0 ) {
         $len = 10;
         $data['alias'] = IssueTrackerHelper::generateNewAlias($len, $fchar, $iformat);
         // $data['alias'] = $this->_generateNewAlias($len, $fchar);

         // set privacy field
         if ( $privacy ) {
            // if ( empty($data['public']) || ! array_key_exists('public',$data)) {
            if ( ! array_key_exists('public',$data)) {
               $data['public'] = $def_privacy;
            }
         }
      }

      // Audit fields are set in the table definition store procedure.
      // Ensure FK relationships are set as a minimum: assigned_to, identified_by and related_project_id
      // Ensure default published state is set:
      if (! array_key_exists('state',$data) ) { $data['state'] = $def_published; }
      if ($data['id'] == 0 && $data['state'] == '') { $data['state'] = $def_published; }
      if (! array_key_exists('issue_type',$data) || empty($data['issue_type']))  { $data['issue_type'] = $def_type; }

      // If status is closed and actual resolution date is not set, then set it.
      if ($data['status'] == $closed_status ) {
         // Check time elements on important date fields
         if ( empty($data['actual_resolution_date'] ) || $data['actual_resolution_date'] == '0000-00-00 00:00:00' ) {
            $data['actual_resolution_date'] = "$date";
         } else {
            $ddd = $data['actual_resolution_date'];
            $d4 = IssueTrackerHelperDate::datetoUTC($ddd);
            $data['actual_resolution_date'] = $d4;
            $this->checktime($data['actual_resolution_date']);
         }
      } else {
         // If status is not closed set actual_resoltion_date to null
         $data['actual_resolution_date'] = "";
      }

      // If identified date is empty set it to today.
      if (empty($data['identified_date']) || $data['identified_date'] == '0000-00-00 00:00:00' ) {
         $data['identified_date'] = "$date";
      } else {
         $ddd = $data['identified_date'];
         $d4 = IssueTrackerHelperDate::datetoUTC($ddd);
         $data['identified_date'] = $d4;
      }

      // If identified by field is empty set it to current user.  At this stage we do not know the guest user so set to default.
      $aid = $this->getAnonymousId();
      if ( $aid > 0 && $def_identby == 0) $def_identby = $aid;
      if (empty($data['identified_by_person_id']) || ( $aid > 0 && $data['identified_by_person_id'] == $aid) ) {
         if ( $user->guest ) {
            $data['identified_by_person_id'] = $def_identby;
         } else {
          if (! (IssueTrackerHelper::isIssueAdmin($user->id) || IssueTrackerHelper::isIssueStaff($user->id)) )
            $data['identified_by_person_id'] = IssueTrackerHelper::get_itpeople_id($user->id);
         }
      }

      // Should check if we displayed the project name before we check the assigned, otherwise we
      // cannot base the assignee on the project.
      if ( ! array_key_exists('related_project_id',$data) || $data['related_project_id'] == 10 ) {
         // Double check - get project for the specified user.
         $proj_id = $this->getDefProject($user->id);
         if ($proj_id == 0) $proj_id = $def_project;
         $data['related_project_id'] = $proj_id;
      }

      // If assigned_to field is empty set it to default assignee if it is valid, NULL otherwise.
      // If the user was updating in the front end than we will not have the assigned_to_person_id field in our data
      // array and thus it was getting changed.
      // Check user details. If user then we do not need the following. Only if staff or admin do we need to check.
      if ( (IssueTrackerHelper::isIssueAdmin($user->id) || IssueTrackerHelper::isIssueStaff($user->id))
             || empty($data['id']) ) {
         if ( ! array_key_exists('assigned_to_person_id',$data) || empty($data['assigned_to_person_id']) || $data['assigned_to_person_id'] == 0) {
            $asnid = IssueTrackerHelper::getDefassignee($data['related_project_id']);

            if ( $asnid != 0 ) {
               $data['assigned_to_person_id'] = $asnid;
            } else {
               $data['assigned_to_person_id'] = $def_assignee;
            }
         }
      } else if ( $data['id'] != 0 ) {
         $data['assigned_to_person_id'] = $this->getoldassignee($data['id']);
      }

      // If target_resolution_date is specified use that else
      // if target_resolution_date greater than that for the associated project
      // set the target to be the project_target_date.
      // However if current date is greater than project target date then we leave it alone. Assumption is that it is a default.
      if ( !empty($data['target_resolution_date']) ) {
         // Form has already applied convertion to UTC. We convert it back to the user timezone.
         // Then when the issue is saved the timezone is extracted automatically.
         $ddd = $data['target_resolution_date'];
         $dd2 = IssueTrackerHelperDate::getDate($ddd);
         $data['target_resolution_date'] = $dd2->format('Y-m-d H:i:s', false, false);
      } else {
         $tdate      = $this->getProjectTargetDate($data['related_project_id']);
         if (! ( empty($tdate) || $tdate == '0000-00-00 00:00:00' ) ) {
            $tdatetime  = strtotime($tdate);
            $cdate      = JFactory::getDate();
            if ( strtotime($cdate) < $tdatetime) {
               if ( empty($data['target_resolution_date']) || ( strtotime($data['target_resolution_date']) > $tdatetime) ) {
                  $data['target_resolution_date'] = $tdate;
               }
            }
         }
      }

      // If empty status or Undefined set it to the defined default.
      if ( ! array_key_exists('status',$data) || empty($data['status']) )     { $data['status'] = $def_status; }
      // If priority not set set it to Low
      if ( ! array_key_exists('priority',$data) || empty($data['priority']) )   { $data['priority'] = $def_priority; }

      // Check time elements on important date fields
      if ( $data['status'] == $open_status)
         $this->checktime($data['identified_date']);

      return;
   }

   /**
    * Method to cludge the time element on a date where the time element is missing.
    * typically this is the situation where the 'calendar JForm drop down has been used.
    *
    * Note that often the hour has actually been set with an offset for the time zone applied
    * so it is only the minutes and seconds being zero that we can check.
    * There is a small chance that the time was exactly on the hour but that is hopefully rare.
    *
    * @param $idate
    *
    */
   private function checktime( & $idate)
   {
      if (empty($idate) || $idate == '0000-00-00 00:00:00' ) return;
      $cdate = JFactory::getDate();
      if ( strlen($idate) == 10 || substr($idate, 14, 5) == '00:00') {
         $string = $cdate->Format('H:i:s');
         $idate = substr($idate,0,10)." ".$string;
      }
      return;
   }

   /**
    * Method to store a record
    *
    * @access  public
    * @return  boolean  True on success
    */
   public function store()
   {
      $app = JFactory::getApplication();
      // Get the input or changed data
      $input   = $app->input;
      $data    = $input->get('jform','','array');        // We mainly want jform fields.

      // Get user details
      $user = JFactory::getUser();

      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Add check for captcha.  Guests cannot edit so this must be a create save.
      if ( $user->guest ) {
         // Save the data in the session.
         $app->setUserState('com_issuetracker.itissues.data', $data);

         if ( $this->_params->get('captcha')  == "recaptcha" ) {
            $dispatcher = JEventDispatcher::getInstance();
            JPluginHelper::importPlugin('captcha');
            $res = $dispatcher->trigger('onCheckAnswer','code');
            if(!$res[0]){
               $app->enqueueMessage( JText::_('COM_ISSUETRACKER_INVALID_CAPTCHA_DETECTED'), 'error' );
               $return = $input->get('return_edit', null, 'default', 'base64');
               if ( empty($return) ) {
                  $return = $return = JRoute::_('index.php?option=com_issuetracker&view=form&layout=edit');
               }

               $input->set('return', base64_encode($return));
               return false;
            }
         }
      }

      // Run spam checker on the description.  Mainly a check for guest users.
      $isSpam  = intval($this->_isSpam());
      if ($isSpam) return false;

      // Get parameters for new user creation.
      $def_notify    = $this->_params->get('def_notify', 0);
      $logging       = $this->_params->get('enableloggings', '0');

      // Find out if we are an issue administrator or staff.
      if ( ! $user->guest ) {
         $isadmin = IssueTrackerHelper::isIssueAdmin($user->id);
         $isstaff = IssueTrackerHelper::isIssueStaff($user->id);
      } else {
         $isadmin = 0;
         $isstaff = 0;
      }

      // Run check against Akismet if configured unless we are an issue administrator or staff.
      if ( ! ( $isadmin || $isstaff )) {
         $use_akismet   = $this->_params->get('akismet_api_key','');
         if ( ! empty($use_akismet) ) {
            if ( $this->_check_akismet($input) ) {
               $app->enqueueMessage( JText::_('COM_ISSUETRACKER_AKISMET_DETECTED_SPAM'), 'error' );
               return false;
            }
         }
      }

      // Check if this is a new record. Ensure we capture id field which is outside the jform sub array.
      // Or the values that were not changable in the editor.  Get these from the hidden fields.
      $data['id']    = JFactory::getApplication()->input->get('id', '', 'post', 'double');
      $db = JFactory::getDBO();
      if ( ! $data['id'] == 0 ) {
         $t2 = $data['id'];

         // Get original record.
         $query  = "SELECT issue_summary, issue_description, status, priority, ";
         $query .= "identified_by_person_id, related_project_id, custom_fields ";
         $query .= " FROM `#__it_issues` WHERE id = ".$db->q($t2);
         $db->setQuery( $query );
         $origrec = $db->loadRow();

         if ( ! isset($data['status']) )
            $data['status'] = $origrec[2];
         if (! isset($data['issue_summary']) )
            $data['issue_summary'] = $origrec[0];
         if ( ! isset($data['issue_description']) )
            $data['issue_description'] = $origrec[1];
         if ( ! isset($data['priority']) )
            $data['priority'] = $origrec[3];
         if ( ! isset($data['identified_by_person_id']) )
            $data['identified_by_person_id'] = $origrec[4];
         if ( empty($data['related_project_id']) ) {
            $rproject_id   = JFactory::getApplication()->input->get('project_id', '', 'post', 'double');
            $data['related_project_id'] = $rproject_id;
            // $data['related_project_id'] = $origrec[5];
            // $data['custom_fields'] = $origrec[6];  // Not sure we need this since the CFs depend upon the Project anyway.
         }
      } else {
         if ( ! isset($data['status']) )     $data['status'] = '';
         if ( ! isset($data['priority']) )   $data['priority'] = '';
         if ( ! isset($data['identified_by_person_id']) )   $data['identified_by_person_id'] = '';
      }

      $def_project = $this->_params->get('def_project', 10);

      // Must be a new record check if called from a menu and if only one project use that otherwise use default project.
      // If the project is not displayed then the array valuable will be empty, so set it to the desired value.
      // If the project is in the array then either they have specifically set it and it has a value, or it is null.
      if ( !array_key_exists ('related_project_id', $data) || empty($data['related_project_id'] )) {
         // If the form was called with a Pid value (as from the projects list view, the following will return a value otherwise it will be empty.
         $pid = $this->getState('project_value', '');
         if ( !empty($pid) ) {
            $data['related_project_id'] = $pid;
         } else {
            // Get the Menu parameters to determine which projects have been selected.
            // Unless we are a Issue Administrator since we may be editing the issue.
            $minput = JFactory::getApplication()->input;
            $menuitemid = $minput->getInt( 'Itemid' ); // this returns the menu id number so we can reference parameters
            $menu = JFactory::getApplication()->getMenu();  // $menu = JSite::getMenu();
            if ($menuitemid) {
               $menuparams = $menu->getParams( $menuitemid );
               $projects = $menuparams->get('projects');

               if ( $menuparams->get('show_project_name',0) == 0 ) {
                  if ( count($projects) == 1 && $projects[0] != 0 ) {
                     $data['related_project_id'] = $projects[0];
                  } else {
                     $data['related_project_id'] = $def_project;
                  }
               } else {
                  $data['related_project_id'] = $def_project;
               }
            } else {    // Just use the default project!
               $data['related_project_id'] = $def_project;
            }
         }
      }

      // If a private issue ensure published is not set.
      if ( array_key_exists ('public', $data) && $data['public'] == 0 && $data['state'] == 1 ) {
         $data['state'] = 0;
      }

      // Ensure defaults are all set.
      $this->_setdefaults($data);

      // Get date.
      $date = JFactory::getDate();
      $Name = NULL;

      // Populate the progress field with user details if a guest.   A guest cannot edit existing issues.
      if ($user->guest) {
         // Get details for email.
         $Name = $data['user_details']['name'];
         $Uname = NULL;
         $Email = $data['user_details']['email'];
         $gnotify = $data['notify'];
         if ( $gnotify == 2 ) {
            $gnotify = $def_notify;
            $data['notify'] = $def_notify;
         }

         // Get parameters for new user creation.
         $cnewperson    = $this->_params->get('create_new_person','0');
         $autogenuname  = $this->_params->get('auto_generate_username','0');
         $def_identby   = $this->_params->get('def_identifiedby','0');
         $def_role = $this->_params->get('def_role', '2');

         if ( ! array_key_exists ('progress', $data) ) $data['progress'] = null;
         if ( $cnewperson == '0' ) {
            $data['progress'] .= 'Reported By: ' . $data['user_details']['name'] . "<br />";
            $data['progress'] .= 'Email: ' .  $data['user_details']['email'] . "<br />";
            $data['progress'] .= 'Notify: ' . ($gnotify == 0 ? 'N' : 'Y') . "<br />";
            $data['identified_by_person_id'] = $def_identby;
         } else {
            // If generate username use email as a base.
            // TODO Need extra check here on empty UName. Should autogenerate option be the default anyway?  Was an and, changed to or. 12/5/2014
            if (empty($Uname) || $autogenuname) $Uname = ucwords(str_replace(array('.','_','-','@'),'_',substr($Email,0)));
            $identby = $this->create_new_person ( $Name, $Uname, $Email, $gnotify, $def_role, $def_project);
            if ( $identby == '' || $identby == 0 ) {
               if ( $logging )
                  IssueTrackerHelperLog::dblog('Error saving user: '.$Name.' Email: '.$Email.' Username: '.$Uname, JLog::ERROR);
                  $identby = $this->getAnonymousId();
               // Add details to progress field since we could not create the user.
               if ( ! array_key_exists ('progress', $data) ) $data['progress'] = null;
               $data['progress'] .= 'Reported By: ' . $data['user_details']['name'] . "<br />";
               $data['progress'] .= 'Email: ' .  $data['user_details']['email'] . "<br />";
               $data['progress'] .= 'Notify: ' . ($gnotify == 0 ? 'N' : 'Y') . "<br />";
            }
            $data['identified_by_person_id'] = $identby;
         }

         $dumm = $data['user_details']['website'];
         if ( ! empty($dumm) ) {
            $data['progress'] .= 'Web Site: ' .  $dumm . "<br />";
         }
      } else {
         // Just in case!
         if ( empty($data['identified_by_person_id'] ) )
            $data['identified_by_person_id'] =  IssueTrackerHelper::get_itpeople_id($user->id);

         // If a registered user is editing then capture the additional information.
         if ( array_key_exists ('additional_info', $data) ) {
            $additional_data = $data['additional_info'];
            // $additional_data = $input->get('jform','additional_info');
            $additional_data = JFilterOutput::cleanText($additional_data);
            if ( ! empty($additional_data)) {
               // Store in progress table.
               $query = "SELECT max(lineno)+1 FROM `#__it_progress` WHERE `alias` = '".$data['alias']."'";
               $db->setQuery( $query );
               $lineno = $db->loadResult();

               if (empty($lineno)) $lineno = 1;

               $public = 1;
               // Check whether this is a private issue.
               if ( array_key_exists('public',$data) && $data['public'] == 0 ) {
                  $public = 0;
               }

               // If private issue set state to unpublished else to published.
               $pstate = 0;
               if ($public) $pstate = 1;

               // Use the registered user group, make published and set to private.
               $query = "SELECT id from `#__usergroups` WHERE title = 'Registered'";
               $db->setQuery( $query );
               $rgroup = $db->loadResult();

               if ( empty($rgroup) ) $rgroup = 2;

               // Handle all quotes correctly since earlier cleaning seems not to have done so!
               $additional_data = str_replace(array("'", '"'), array("\\'", '\\"'), $additional_data);

               // Save record in the table.
               $query = 'INSERT INTO `#__it_progress` (issue_id, alias, progress, public, state, lineno, access, created_by, created_on) ';
               $query .= "VALUES(".$data['id'] .",'". $data['alias'] ."','". $additional_data ."',".$public.",".$pstate.",". $lineno .",". $rgroup .",'". $user->username ."', UTC_TIMESTAMP() )";
               $db->setQuery( $query );
               $db->execute();
            }
         }

         // Check if the notification request has changed.  Need to review this logic and make it more robust.
         // key will be blank if it is an issue update where field is not displayed.
         if ( array_key_exists ('notify', $data) && $data['notify'] != '' ) {
            $notify = $data['notify'];
            if ( $notify != 2 )
               $this->_upd_user_notify($user->id, $notify );
         }
      }

      // Determine whether insert or an update
      if ($data['id'] > 0 ) {
         $new = 0;
         $cur_issue_no = $data['alias'];
      } else {
         $new = 1;

         if ( ! array_key_exists ('progress', $data )) {
            $data['progress'] = '';
         }
      }

      // Handle the custom field entries and store then here. This is true whether we are creating a new issue
      // or whether this is an update.
      $objects = array();
      // $variables = JRequest::get('post', 2);
      $variables = $input->getArray($_POST);
      // echo "<pre>";var_dump($variables);echo "</pre>";
      foreach ($variables as $key => $value) {
         if (( bool )JString::stristr($key, 'CustomField_')) {
            $object = new JObject;
            $object->set('id', JString::substr($key, 14));
            if (is_string($value)) {
               $value = trim($value);
            }
            $object->set('value', $value);
            unset($object->_errors);
            $objects[] = $object;
         }
      }

      // Need to merge the input custom field values with any existing fields if this is an existing issue.
      if ( $new == 1 ) {
         $data['custom_fields'] = json_encode($objects);
      } else {
         // Fetch original custom field values since not all may have ben visible to the user.
         $sql = "SELECT custom_fields from #__it_issues where id = ".$data['id'];
         $db->setQuery($sql);
         $xx = $db->loadResult();

         // Order is important! New object first.
         $rrrr = $objects + json_decode($xx);
         $data['custom_fields'] = json_encode($rrrr);
      }

      // $cur_issue_no = $data['alias'];

      // Changed to move progress data into progress table.
      if ( ! $new && ! empty($data['progress']) ) {
         $this->update_progress_table($data);
         $ndata = NULL;
      } else {
         $ndata = $data;
      }
      $data['progress'] = ''; // Empty out our issue progress field.

      JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');
      $row  = $this->getTable('itissues','IssueTrackerTable');  //was &

      // Bind the form fields to the table
      if (!$row->bind($data)) {
         $this->setError($this->_db->getErrorMsg());
         // Need to remove any saved attachments
         return false;
      }

      // Make sure the record is valid
      if (!$row->check()) {
         $this->setError($this->_db->getErrorMsg());
         // Need to remove any saved attachments
         return false;
      }


      if ((!empty($data['tags']) && $data['tags'][0] != '')) {
         $row->newTags = $data['tags'];
      }

      // Store record in the database
      if (!$row->store()) {
         $this->setError( $row->getError() );
         return false;
      }

      // Flush the data from the session
      $app->setUserState('com_issuetracker.itissues.data', null);

      if ( !empty ($data['id'])) {
         $row->checkIn($data['id']);
      }

      // If this is a new issue we need to check that the issue alias matches the id if we are using format 1 or 2.
      if ( $new ) {
         $iformat = $this->_params->get('iformat', '0');
         $oalias  = $row->alias;
         if ( empty($data['id']) ) {
            $data['id'] = $row->id;
         }
         if ( $iformat > 0 ) {
            $rid     = $row->id;
            $len     = 10;
            $nalias = IssueTrackerHelper::checkAlias ($rid, $oalias, $len, $iformat );

            //Update data array with new alias value
            $data['alias'] = $nalias;
         } else {
            $nalias = $oalias;
         }

         // Update progress details if this was a new record.
         if ( !empty($ndata) ) {
            if ( $nalias != $oalias ) $ndata['alias'] = $nalias;
            $this->update_progress_table($ndata);
         }
      }

      // Ensure it is checked in.  Check this. Suspect should be $row->id
      $pk   = $data['id'];
      $this->checkin($pk);

      // $rid  = $data['id'];
      $files       = JFactory::getApplication()->input->files->get('attachedfile', '', 'files', 'array');

      // Check if we have any files specified in our array.
      $emptyFile   = true;
      if ( !empty($files) ) {
         foreach ($files as $file) {
            // $emptyFile   = true;
            if ( !empty($file['name'] ))
               $emptyFile = false;
         }
      }

      //  if ( !$emptyFile && $perms['attachment'] ) {
      if ( !$emptyFile ) {
         // Perform file load in model. We just populate the required details for the extra information.
         require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'models'.DS.'attachment.php');
         $attmodel = new IssuetrackerModelAttachment();
         $attachment = $attmodel->getItem();

         $ndata = array();

         JLoader::import('joomla.utilities.date');
         $jdate = new JDate();

         $ndata['created_on']  = $jdate->toSql();
         //  $ndata['enabled']     = 1;
         $ndata['issue_id']    = $data['alias'];
         $ndata['uid']         = $user->id;
         $ndata['title']       = $data['issue_summary'];
         $ndata['state']       = 1;

         if ( $user->guest) {
            $ndata['created_by']  = $Name;
         } else {
            $ndata['created_by']  = $user->name;
         }

         // Pick up provided file titles if any.
         $Filedesc = $variables['Filedesc'];

         // We do the save of all the specified files in the model.
         $result = $attmodel->save($ndata, $Filedesc);

         if ( !$result) {
            $app->enqueueMessage( $this->getError(), 'error' );
         } else {
            $app->enqueueMessage( JText::plural('COM_ISSUETRACKER_N_FILE_ATTACHMENT_SAVED_MSG', $result));
         }
      }

      IssueTrackerHelper::prepare_messages( $data, $new);
      $app->enqueueMessage( JText::_('COM_ISSUETRACKER_MESSAGES_ISSUE_SAVED') . $data['alias'] );

      // If this is a new issue set a variable which will control the WHERE clause for the display selection.
      $sess =  JFactory::getSession();
      $sess->set('display_issue', $new);

      // If we don't then we need an additional check in here to see if this is a guest user and that the default state (which this just
      // saved issue will assume) is published.
      if ( !$user->guest || ($user->guest && $row->state == 1) || $new == 1 ) {
         $return = $input->get('return', null, 'default', 'base64');
         if ( empty($return) ) {
            $iid = $row->id;
            $return = JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.(int) $iid);
            $input->set('return', base64_encode($return));
         }
      }

      return true;
   }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array $config Configuration array for model. Optional.
     * @internal param \The $type table type to instantiate
     * @return  JTable   A database object
     * @since   1.6
     */
   public function getTable($type = 'Itissues', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    *
    * Method to remove a row
    *
    * @param $pk   int    The id of the row to remove
    * @return bool
    */
   public function delete($pk)
   {
      $row = $this->getTable();

      // Get reference to parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $app = JFactory::getApplication();
      $delmode = $this->_params->get('delete', 0);

      if ($delmode == 0 ) {
         // Delete mode disabled.  Should give a message as well.
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_DISABLED_MSG'));
         return false;
      } else if ( $delmode == 1 || $delmode == 2 ) {
         // Check that record is not locked out, i.e. being edited.
         $query  = "SELECT checked_out FROM `#__it_issues` WHERE id = ".$pk;
         $this->_db->setQuery( $query );
         $checkedout = $this->_db->loadResult();
         $user = JFactory::getUser();

         if ($checkedout > 0 && $checkedout != $user->id ) {
            $app = JFactory::getApplication();
            $checkoutUser  = JFactory::getUser($checkedout);
            $app->enqueueMessage(JText::sprintf('COM_ISSUETRACKER_ISSUE_BEING_EDITED_MSG',$checkoutUser->name),'info');
            $return = JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.(int) $pk);
            $app->redirect($return, false);
         }

         // Remove attachments if any.
         $this->delete_attachments($pk);

         // Delete progress records.
         $this->delete_progress_recs($pk);

         if (!$row->delete( $pk )) {
            // Special case where asset_id is 0 which may be the case for old issues created
            // prior to the introduction of the table field.
            // This is a catch all for very old installations.
            $query  = "SELECT asset_id FROM `#__it_issues` WHERE id = ".$pk;
            $this->_db->setQuery( $query );
            $assid = $this->_db->loadResult();

            if ( $assid == 0 ) {
               $query = "DELETE FROM `#__it_issues` WHERE id = ".$pk;
               $this->_db->setQuery( $query );
               try {
                  // Execute the query.
                  $result = $this->_db->execute();
               } catch (Exception $e) {
                  // catch any database errors.
                  $app->enqueueMessage(nl2br($this->_db->getError()),'error');
                  return false;
               }
               return true;
            }
            $this->setError( $row->getError() );
            return false;
         }
         return true;
      } else if ( $delmode > 2 ) {
         // Unknown mode.  Mode 2 is only applicable for user deletion.
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_UNKNOWN_MSG'),'error');
         return false;
      }
      return true;
   }

   /**
    *
    * Method to remove any attachments associated with the issue.
    *
    * @param $issue_id
    * @return mixed
    */
   private function delete_attachments($issue_id)
   {
      $query  = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = ";
      $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue_id."')";
      $this->_db->setQuery( $query );
      $delcnt = $this->_db->loadResult();

      if ( $delcnt > 0 ) {
         JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_issuetracker/tables');
         $row = JTable::getInstance('Attachment', 'IssueTrackerTable', array());

         $query  = "SELECT id FROM `#__it_attachment` WHERE issue_id = ";
         $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue_id."')";
         $this->_db->setQuery( $query );
         $pks = $this->_db->loadColumn();

         foreach ( $pks as $pk) {
            $row->delete($pk);
         }
         return $delcnt;
      }
   }

   /**
     * Method to checkin/unlock the issue
     *
     * @access   public
     * @param $id
     * @return   boolean   True on success
     * @since   1.5
     */
   function checkin($id)
   {
      if ($id) {
         JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');
         $itissues  = $this->getTable('itissues','IssueTrackerTable');
         if(! $itissues->checkin($id)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
         }
      }
      return false;
   }

   /**
    * Method to update the it_person notification field.
    *
    * @param $user_id
    * @param $value
    */
   private function _upd_user_notify($user_id, $value )
   {
      $app = JFactory::getApplication();
      if (empty($db)) { $db = JFactory::getDBO(); }
      $query = 'UPDATE `#__it_people` set email_notifications = '.$value.' WHERE id = '. $db->Quote($user_id);
      $db->setQuery($query);
      $ret = $db->execute();
      if (!$ret) {
         $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
      }
   }


   /**
    * Method to create a new unregistered user if not already unregistered or registered already.
    *
    * @param $Name
    * @param $Uname
    * @param $Email
    * @param $notify
    * @param $def_role
    * @param $def_project
    * @return mixed
    */
   private function create_new_person($Name, $Uname, $Email, $notify, $def_role, $def_project)
   {
      if (empty($db)) { $db = JFactory::getDBO(); }
      // Check if we have this user already registered.
      $query  = "SELECT count(person_name) from `#__it_people` WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
      $db->setQuery( $query );
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         $query  = "INSERT into `#__it_people` (person_name, username, person_email, email_notifications, registered, person_role, assigned_project)";
         $query .= "values('".$Name."','".$Uname."', '".$Email."', '".$notify."', '0', '".$def_role."','".$def_project."')";
         $db->setQuery($query);
         $ret = $db->execute();
         //  if (!$ret) {
         //     $app = JFactory::getApplication('site');
         //     $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
         //  }
      } else {
         $query  = "UPDATE `#__it_people` set email_notifications = ".$notify." WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
         $db->setQuery($query);
         $ret = $db->execute();
         //  if (!$ret) {
         //     $app = JFactory::getApplication('site');
         //     $app->enqueueMessage(nl2br($db->getErrorMsg()),'error');
         //  }
      }

      $query = "SELECT id from `#__it_people` WHERE person_name = '".$Name."' AND person_email = '".$Email."'";
      $db->setQuery( $query );
      $id = $db->loadResult();
      return $id;
   }

   /**
    * Method to perform internal check for configured spam
    *
    */
   private function _isSpam()
   {
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $user = JFactory::getUser();
      //filter out logged in users
      if (! $user->guest) { return 0; }
      $input = JFactory::getApplication()->input;

      //filters first
      $ipList = explode("\r\n",$this->_params->get('ip_list',''));
      $urlList = explode("\r\n",$this->_params->get('url_list',''));
      $emailList = explode("\r\n",$this->_params->get('email_list',''));

      if (in_array($_SERVER['REMOTE_ADDR'], $ipList)) { return 1; }

      if ($input->getSring('website') && in_array($input->getString('website'), $urlList)) { return 1; }
      if ($input->getString('email') && in_array($input->getString('email'), $emailList)) { return 1; }

      //OK, filters have passed. Now check link count & words
      $wordList = explode("\r\n",$this->_params->get('word_list',''));
      if (count($wordList) > 1) {
         foreach ($wordList as $word) {
            if (stristr($input->getString('issue_summary'), $word)) { return 1; }
            if (stristr($input->getString('issue_description'), $word)) { return 1; }
            if (stristr($input->getString('additional_info'), $word)) { return 1; }
          }
      }

      //how many urls - This is a basic form of caching.
      if (substr_count($input->getString('issue_description'), 'http://')   >= $this->_params->get('link_count',3))   { return 1; }
      if (substr_count($input->getString('issue_summary'), 'http://')       >= $this->_params->get('link_count',3))   { return 1; }
      if (substr_count($input->getString('additional_info'), 'http://')     >= $this->_params->get('link_count',3))   { return 1; }

      return 0;
   }

    /**
     * Method to get User's default defined project
     * @param $userid
     * @return object with data
     */
   public function getDefProject($userid)
   {
      // Load the data
      $query = 'SELECT assigned_project FROM `#__it_people` WHERE `user_id` = '.$userid;
      $this->_db->setQuery( $query );
      $projid = $this->_db->loadResult();

      return $projid;
   }

    /**
     * Method to get Project target end date
     *
     * @param $projectid
     * @return object with data
     */
   public function getProjectTargetDate($projectid)
   {
      // Load the data
      $query = 'SELECT target_end_date FROM `#__it_projects` WHERE `id` = '.$projectid;
      $this->_db->setQuery( $query );
      $penddate = $this->_db->loadResult();

      return $penddate;
   }

    /**
     * Method to get Anonymous user id
     *
     * @return int|mixed $aid Id if found 0 otherwise
     */
   public function getAnonymousId()
   {
      // Load the data
      // Set up access to parameters
      $params = JComponentHelper::getParams( 'com_issuetracker' );
      $def_identby   = $params->get('def_identifiedby','0');
      return $def_identby;
   }

   /**
    *
    * Method to check whether the included text is spam using Akismet
    * Details from akismet.com
    *
    * Input is an array with the text in the comment_content element.  Other fields should get populated in the _getAkismet method.
    *
    * $data = array('blog' => 'http://yourblogdomainname.com',
    *   'user_ip' => '127.0.0.1',
    *   'user_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6',
    *   'referrer' => 'http://www.google.com',
    *   'permalink' => 'http://yourblogdomainname.com/blog/post=1',
    *   'comment_type' => 'comment',
    *   'comment_author' => 'admin',
    *   'comment_author_email' => 'test@test.com',
    *   'comment_author_url' => 'http://www.CheckOutMyCoolSite.com',
    *   'comment_content' => 'It means a lot that you would take the time to review our software.  Thanks again.');
    *
    * @param $data
    * @return bool
    */
   public function _check_akismet($data)
   {
      try {
         if ($this->_getAkismet($data)->isCommentSpam()) {
            // Its defined as spam just return true
            return true;
         }
      } catch (Exception $e) {
         if (JDEBUG) JError::raiseWarning(500, $e->getMessage());
         return false;
      }

      return false;
   }

    /**
     * Method to get Project target end date
     *
     * @param $input
     * @throws Exception
     * @return object with data
     */
   private function _getAkismet($input)
   {
      // $data = $input['jform'];
      $data    = $input->get('jform','','array');

      $akismet = new Akismet($this->_params->get('site_url'), $this->_params->get('akismet_api_key'));
      if (!$akismet->isKeyValid()){
         throw new Exception(JText::_('COM_ISSUETRACKER_AKISMET_INVALID_API_KEY'));
      }
      $text = null;
      if ( ! empty ($data['issue_summary']) )
         $text .= $data['issue_summary'];
      if ( ! empty ($data['issue_description']) )
         $text .= ' ' . $data['issue_description'];

      $user = JFactory::getUser();    // Assumes registered user
      if ( $user->guest ) {
         $akismet->setCommentAuthor($data['user_details']['name']);
         // Use author set to 'viagra-test-123' to get a positive test back.
         $akismet->setCommentAuthorEmail($data['user_details']['email']);
      } else {
         // $akismet->setCommentAuthor($user->user_id ? $user->name : $user->name);
         // $akismet->setCommentAuthorEmail($user->user_id ? $user->email : $user->email);
         $akismet->setCommentAuthor($user->id ? $user->name : $user->name);
         $akismet->setCommentAuthorEmail($user->id ? $user->email : $user->email);
         // Guests cannot add additional information
         if ( array_key_exists ('additional_info', $data) && ! empty ($data['additional_info']) )
            $text .= ' ' . $data['additional_info'];
      }

      $akismet->setCommentContent($text);
      $akismet->setCommentType('comment');
      return $akismet;
   }

   /**
    * Method to remove any progress records associated with the issue.
    *
    * @param $issue_id
    *
    * @return boolean Return true or the number of records deleted.
    */
   private function delete_progress_recs($issue_id)
   {
      $query  = "SELECT count(*) FROM `#__it_progress` WHERE issue_id = '".$issue_id."'";
      $this->_db->setQuery( $query );
      $delcnt = $this->_db->loadResult();

      if ( $delcnt > 0 ) {
         $query  = "DELETE FROM `#__it_progress` WHERE issue_id = '".$issue_id."'";
         $this->_db->setQuery( $query );
         $delcnt = $this->_db->loadResult();
         return $delcnt;
      }

      return true;
   }

   /**
    * Method to transfer our entered data into the progress table.
    *
    * This is a stop gap until we write the full progress method.
    * @param $data
    * @return bool
    */
   private function update_progress_table($data)
   {
      // Extract the progress data and insert it into the progress table.
      $progtext = NULL;
      if (array_key_exists('progress', $data) )
         $progtext = $data['progress'];
         // $progtext = nl2br(str_replace ( "\"", "\"\"", $data['progress'])) ;

      if (empty($progtext) || $progtext == '' || $progtext == ' ') return true;

      // $progtext = $this->_db->quote($progtext);
      $progtext = str_replace(array("'", '"'), array("\\'", '\\"'), $progtext);

      if ( $data['id'] == 0 ) {
         $query = "SELECT id from `#__it_issues` where `alias` = '".$data['alias']."'";
         $this->_db->setQuery($query);
         $issue_id = $this->_db->loadResult();
         if ( empty($issue_id) ){
            $params   = JComponentHelper::getParams( 'com_issuetracker' );
            $logging  = $params->get('enablelogging', '0');
            if ( $logging )
               IssueTrackerHelperLog::dblog('Problem saving progress information - No issue id provided. ' .$data['alias'], JLog::ERROR);
            return false;
         }
      } else {
         $issue_id = $data['id'];
      }

      // Load the data
      $query = "SELECT max(lineno)+1 FROM `#__it_progress` WHERE `alias` = '".$data['alias']."'";
      $this->_db->setQuery( $query );
      $lineno = $this->_db->loadResult();

      if (empty($lineno)) $lineno = 1;

      $user = JFactory::getUser();
      if ( !array_key_exists ('public', $data) ) {
         $data['public'] = 1;
      }

      if ( !array_key_exists('progresspublic',$data) ) {
         $data['progresspublic'] = $data['public'];
      }

      if ( !array_key_exists ('pstate', $data) ) {
         $data['pstate'] = $data['state'];
      }

      if (array_key_exists('access', $data)) {
         $rgroup = $data['access'];
      } else {
         // Use the registered user group, make unpublished and set to private.
         $query = "SELECT id from `#__usergroups` WHERE title = 'Registered'";
         $this->_db->setQuery( $query );
         $rgroup = $this->_db->loadResult();
      }

      if ( array_key_exists ('paccess', $data) ) {
         $rgroup = $data['paccess'];
      }

      if ( empty($rgroup) ) $rgroup = 2;

      // Save record in the table.
      $query = 'INSERT INTO `#__it_progress` (issue_id, alias, progress, public, state, lineno, access, created_by, created_on) ';
      $query .= 'VALUES('.$issue_id .',"'. $data['alias'].'","'. $progtext .'",'. $data['progresspublic'] .','. $data['pstate'] .','. $lineno .','. $rgroup .',"'. $user->username .'", UTC_TIMESTAMP() )';
      $this->_db->setQuery( $query );
      $this->_db->execute();

      // Empty out our issue progress field.
      $data['progress'] = '';
      return true;
   }

   /**
    * Escapes input to stop sql injection and XSS attacks
    *
    * @param $str string The text to clean.
    * @return string
    */
   public static function safe($str)
   {
      //use of ENT_QUOTES necessary to prevent injection of single quotes
      // return htmlentities($str, ENT_QUOTES, 'UTF-8', FALSE);

      // Change any double quites to singles.
      $ntext = nl2br(str_replace ( "\"", "\"\"", $str)) ;

      // Change any UTF-8 non breaking spaces
      $ntext = preg_replace('#\xc2\xa0#', ' ', $ntext);

      // Remove blank lines.
      $ntext = preg_replace('#^(<br */?>\s*)+#i', '', $ntext);

      // Remove multiple consecutive <br /> with or without trailing spaces.
      $ntext = preg_replace('#(<br */?>\s*)+#i', '<br />', $ntext);

      // Remove new lines from end of strings
      $ntext = preg_replace('#(<br */?>\s*)+#i', '<br />', $ntext);

      return $ntext;

   }

    /**
     * Method to get an issues assignee value from an issue.
     *
     * Created as a band aid but notcurrently used.
     *
     * @param $id
     * @return assignee or zero if not defined.
     */

   public function getoldassignee($id)
   {
      // Load the data
      if ( $id > 0 ) {
         $query = 'SELECT assigned_to_person_id FROM `#__it_issues` WHERE `id` = '.$id;
         $this->_db->setQuery( $query );
         $assignee = $this->_db->loadResult();
      } else {
         $assignee = 0;
      }

      return $assignee;

   }
}