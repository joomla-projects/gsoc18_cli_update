<?php
/*
 *
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import('joomla.application.component.modeladmin');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

// Load log helper
if (! class_exists('IssueTrackerHelperLog')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
}

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    com_issuetracker
 * @since       1.7
 */
class IssueTrackerModelItissues extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';
   protected $_params;

   /**
    * Method override to check if you can edit an existing record.
    *
    * @param   array $data An array of input data.
    * @param   string   $key  The name of the key for the primary key.
    *
    * @return  boolean
    * @since   2.5
    */
   protected function allowEdit($data = array(), $key = 'id')
   {
      // Check specific edit permission then general edit permission.
      return JFactory::getUser()->authorise('core.edit', 'com_issuetracker.itissue.'.
                                            ((int) isset($data[$key]) ? $data[$key] : 0))
             or parent::allowEdit($data, $key);
   }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type The type
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array $config Configuration array for model. Optional.
     * @internal param $type The table type to instantiate
     * @return  JTable   A database object
     * @since   1.7
     */
   public function getTable($type = 'Itissues', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    * Method to get the record form.
    *
    * @param   array $data          An optional array of data for the form to interogate.
    * @param   boolean  $loadData   True if the form is to load its own data (default case), false if not.
    * @return  JForm                A JForm object on success, false on failure
    * @since   1.7
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Initialise variables.
      // $app  = JFactory::getApplication();

      // Get the form.
      $form = $this->loadForm('com_issuetracker.itissues', 'itissues', array('control' => 'jform', 'load_data' => $loadData));
      if (empty($form)) {
         return false;
      }

      return $form;
   }

   /**
    * Method to get the data that should be injected in the form.
    *
    * @return  mixed The data for the form.
    * @since   1.6
    */
   protected function loadFormData()
   {
      // Check the session for previously entered form data.
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.itissues.data', array());

      if (empty($data)) {
         $data = $this->getItem();
      }
      return $data;
   }

   /**
    * Method to get a single record.
    *
    * @param   integer  $pk The id of the primary key.
    *
    * @return  mixed Object on success, false on failure.
    * @since   1.6
    */
   public function getItem($pk = null)
   {
      if ($item = parent::getItem($pk))
      {
         // Convert the params field to an array.
         $registry = new JRegistry;
         $registry->loadString($item->metadata);
         $item->metadata = $registry->toArray();

         //Do any procesing on fields here if needed
         if ($item->id) {
            $jversion = new JVersion();
            if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
               $item->tags = new JHelperTags;
               $item->tags->getTagIds($item->id, 'com_issuetracker.itissue');
            }
            $user = JFactory::getUser();
            $itable = $this->getTable();
            $itable->checkout($user->id, $item->id);
         }
      }

      return $item;
   }

   /**
    * Prepare and sanitise the table prior to saving.
    *
    * @since   1.6
    * @param JTable $table
    */
   protected function prepareTable($table)
   {
      JLoader::import('joomla.filter.output');

      if (empty($table->id)) {
         // Set ordering to the last item if not set
         if (@$table->ordering === '') {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT MAX(ordering) FROM #__it_issues');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }
      }
   }

   /**
    * Method to save the form data.
    *
    * @param   array $data The form data.
    *
    * @return  boolean  True on success.
    * @since   1.6
    */
   public function save($data)
   {
      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $fchar         = $this->_params->get('initial_admin', 'A');
      $def_assignee  = $this->_params->get('def_assignee', 0);
      $iformat       = $this->_params->get('iformat', 0);

      if ( $data['id'] == 0) {
         $new = 1;
      } else {
         $new = 0;
         $data['alias'] = JFactory::getApplication()->input->get('alias');
      }

      // Check defaults all set correctly.
      $this->_setdefaults($data);

      // Ensure published state is not set if private issue.
      if ( array_key_exists ('public', $data) && $data['public'] == 0 && $data['state'] == 1 ) {
         $data['state'] = 0;
      }

      // If assigned_to field is empty set it to default assignee if it is valid, NULL otherwise.
      // This is efectively not used since it is performed in the _setDefaults method!
      if ( ! array_key_exists('assigned_to_person_id',$data) || empty($data['assigned_to_person_id']) || $data['assigned_to_person_id'] == 0) {
         // Check default assignee
         if ( $def_assignee ) {
            $data['assigned_to_person_id'] = $def_assignee;
         } else {
            $data['assigned_to_person_id'] = NULL;
         }
      }

      // Extract the progress data and insert it into the progress table.
      if ( $data['id'] != 0 && !empty($data['progress']) ) {
         $this->update_progress_table($data);
         $ndata = NULL;
      } else {
         $ndata = $data;
      }
      $data['progress'] = NULL;   // Empty out our issue progress field.

      // Handle the Custom fields
      $objects = array();
      // $variables = JRequest::get('post', 2);
      $variables = JFactory::getApplication()->input->getArray($_POST);
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
      // echo "<pre>";var_dump($objects);echo "</pre>";
      $data['custom_fields'] = json_encode($objects);

      // Alter the title for save as copy
      if (JFactory::getApplication()->input->get('task') == 'save2copy') {
         $issue_summary = $this->_generateNewSummary($data['issue_summary']);
         $data['issue_summary'] = $issue_summary;
         $len = 10;
         $data['alias'] = IssueTrackerHelper::generateNewAlias($len, $fchar, $iformat);
         // $data['alias'] = $this->_generateNewAlias($len, $fchar);
      }

      if (parent::save($data)) {
         if ( $new ) {
            $idata   = self::getItem();
            $iformat = $this->_params->get('iformat', '0');
            $oalias  = $idata->alias;
            if ( $iformat > 0 ) {
               $rid     = $idata->id;
               $len     = 10;
               $nalias = IssueTrackerHelper::checkAlias ($rid, $oalias, $len, $iformat );
               $data['alias'] = $nalias;
            } else {
               $nalias = $data['alias'];
            }

            $data['id'] = $idata->id;

            // Update progress details if this was a new record.
            if ( !empty($ndata) ) {
               if ( $nalias != $oalias ) $ndata['alias'] = $nalias;
               $this->update_progress_table($ndata);
            }
         }
         $itable = $this->getTable();
         $itable->checkIn($data['id']);

         IssueTrackerHelper::prepare_messages( $data, $new);
         return true;
      }

      return false;
   }

   /**
    * Method to transfer our entered progress data into the progress table.
    *
    * This may be a stop gap if we write the full progress method.
    * @param $data
    * @return bool
    */
   private function update_progress_table($data)
    {
      // Extract the progress data and insert it into the progress table.
      // $progtext = nl2br(str_replace ( "\"", "\"\"", $data['progress'])) ;
      $progtext = $data['progress'];

      if (empty($progtext) || $progtext == '') return true;

      if ( $data['id'] == 0 ) {
         $query = "SELECT id from `#__it_issues` where `alias` = '".$data['alias']."'";
         $this->_db->setQuery($query);
         $issue_id = $this->_db->loadResult();
         if ( empty($issue_id) ){
            $params = JComponentHelper::getParams( 'com_issuetracker' );
            $logging   = $params->get('enablelogging', '0');
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
      if ( !array_key_exists ('pstate', $data) ) {
         $data['pstate'] = $data['state'];
      }
      if ( !array_key_exists ('paccess', $data) ) {
         $data['paccess'] = $data['access'];
      }

      $progtext = $this->safe($progtext);

      // Save record in the table.
      $query = 'INSERT INTO `#__it_progress` (issue_id, alias, progress, public, state, lineno, access, created_by, created_on) ';
      $query .= 'VALUES('.$issue_id .',"'. $data['alias'].'","'. $progtext .'",'. $data['progresspublic'] .','. $data['pstate'] .','. $lineno .','. $data['paccess'] .',"'. $user->username .'", UTC_TIMESTAMP() )';
      $this->_db->setQuery( $query );
      $this->_db->execute();

      return true;
   }

   /**
    * escapes input to stop sql injection and XSS attacks
    *
    * @param $str string The text to clean.
    * @return string
    */
   public static function safe($str)
   {
      //use of ENT_QUOTES necessary to prevent injection of single quotes
      // return htmlentities($str, ENT_QUOTES, 'UTF-8', FALSE);
      $ntext = str_replace(array("'", '"'), array("\\'", '\\"'), $str);

      // Change any double quotes to singles.
      // $ntext = nl2br(str_replace ( "\"", "\"\"", $ntext)) ;

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
    *  Method to set up the record defaults.
    *
    * @param   array  $data   The form data.
    * @return  array  $data   The modified data
    *
    */
   private function _setdefaults( & $data )
   {
      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $def_published = $this->_params->get('def_published', 0);
      $def_assignee  = $this->_params->get('def_assignee', 1);
      $def_project   = $this->_params->get('def_project', 1);
      $def_type      = $this->_params->get('def_type', 1);
      $def_priority  = $this->_params->get('def_priority', 2);  // Low
      $def_status    = $this->_params->get('def_status', 4);   // Open
      // $notify        = $this->_params->get('email_notify', 0);
      $fchar         = $this->_params->get('initial_admin', 'A');
      $open_status   = $this->_params->get('open_status', '4');
      $closed_status = $this->_params->get('closed_status', '1');
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
      }

      // Audit fields are set in the table definition store procedure.
      // Ensure FK relationships are set as a minimum: assigned_to, identified_by and related_project_id
      // Ensure default published state is set:
      // if (empty($data['state'])) { $data['state'] = $def_published; }
      if (! array_key_exists('state',$data) ) { $data['state'] = $def_published; }
      if ($data['id'] ==  0 && $data['state'] == '') { $data['state'] = $def_published; }
      if (empty($data['issue_type'])) { $data['issue_type'] = $def_type; }

      // If status is closed and actual resolution date is not set, then set it.
      if ($data['status'] == $closed_status ) {
         // Check time elements on date fields
         if ( empty($data['actual_resolution_date']) || $data['actual_resolution_date'] == "0000-00-00 00:00:00" )  {
            $data['actual_resolution_date'] = "$date";
         } else {
            // Form has already applied convertion to UTC. We convert it back to the user timezone.
            // Then when the issue is saved the timezone is extracted automatically.
            $ddd = $data['actual_resolution_date'];
            $dd2 = IssueTrackerHelperDate::getDate($ddd);
            $data['actual_resolution_date'] = $dd2->format('Y-m-d H:i:s', true, false);
            $this->checktime($data['actual_resolution_date']);
         }
      } else {
         // If status is not closed set actual_resolution_date to null
         $data['actual_resolution_date'] = "";
      }

      // If identified date is empty set it to today.
      if (empty($data['identified_date']) || $data['identified_date'] == "0000-00-00 00:00:00" ) {
         $data['identified_date'] = "$date";
      }
      // If identified by field is empty them set it to the current user.  Need to get the id field from the it_people table for the current $user->id.
      // $aid = $this->getAnonymousId();
      // if (empty($data['identified_by_person_id']) || ( && $data['identified_by_person_id'] == $aid)) {
      if (empty($data['identified_by_person_id']) ) {
         $data['identified_by_person_id'] = IssueTrackerHelper::get_itpeople_id($user->id);
      }

      // Should check if we displayed the project name before we check the assignee, otherwise we
      // cannot base the assignee on the project.
      if ( ! array_key_exists('related_project_id',$data) || $data['related_project_id'] == 10 ||
         $data['related_project_id'] == 0 ) {
         // Double check - get default project for the identifying user if specified.
         $proj_id = $this->getDefProject2($data['identified_by_person_id']);
         if ( empty($proj_id) ) {
            $proj_id = $def_project;
         }
         $data['related_project_id'] = $proj_id;
      }

      // If assigned_to field is empty set it to default assignee if valid, NULL otherwise.
      if ( empty($data['assigned_to_person_id']) || $data['assigned_to_person_id'] == 0) {
         // Check if we have a project default assignee specified. If we do use that.
         $asnid = IssueTrackerHelper::getDefassignee($data['related_project_id']);

         if ( $asnid != 0 ) {
            $data['assigned_to_person_id'] = $asnid;
         } else {
            // Check default assignee.  Should be greater than zero.
            if ( $def_assignee ) {
               $data['assigned_to_person_id'] = $def_assignee;
            } else {
               $data['assigned_to_person_id'] = NULL;
            }
         }
      }

      // If target_resolution_date is empty or greater than that for the associated project
      // set the target to be the project_target_date.
      // However if current date is greater than project target date then we leave it alone. Assumption is that it is a defect.
      if (!empty($data['related_project_id']) ) {
         // If a date is provided use that else get it from the project.
         if ( !empty($data['target_resolution_date']) ) {
            // Form has already applied convertion to UTC. We convert it back to the user timezone.
            // Then when the issue is saved the timezone is extracted automatically.
            $ddd = $data['target_resolution_date'];
            $dd2 = IssueTrackerHelperDate::getDate($ddd);
            $data['target_resolution_date'] = $dd2->format('Y-m-d H:i:s', false, false);
         } else {
            // First get the project target_end_date.
            $tdate      = $this->getProjectTargetDate($data['related_project_id']);
            if (! (empty($tdate) || $tdate == "0000-00-00 00:00:00") ) {
               $tdatetime  = strtotime($tdate);
               // $cdate      = JFactory::getDate();
               $cdate = IssueTrackerHelperDate::getDate();
               if ( strtotime($cdate) < $tdatetime) {
                  if ( empty($data['target_resolution_date']) || ( strtotime($data['target_resolution_date']) > $tdatetime) ) {
                     $data['target_resolution_date'] = $tdate;
                  }
               }
            }
         }
      }

      // If empty status or Undefined set it to the defined default.
      if (empty($data['status']) ) { $data['status'] = $def_status; }
      // If priority not set set it to Low
      if (empty($data['priority']) ) { $data['priority'] = $def_priority; }

      // Check time elements on identified date fields
      if ( $data['status'] == $open_status)
         $this->checktime($data['identified_date']);

      return;
   }

   /**
    * Method to append time element on a date.
    *
    * If the time element on a date is missing add the current time.
    * Typically this is the situation where the 'calendar JForm driopo down has been used.
    *
    * Note that often the hour has actually been set with an offset for the time zone applied
    * so it is only the minutes and seconds beinfg zero that we can check.
    * There is a small chance that the time was exactly on the hour but that is hopefully rare
    *
    * @param   string $idate      Time element.
    * @return  object $idate      The modified date
    *
    *
    */
   private function checktime( & $idate)
   {
      if ( empty( $idate ) ) return;

      $cdate = JFactory::getDate();
      if ( strlen($idate) == 10 || substr($idate, 14, 5) == '00:00') {
         $string = $cdate->Format('H:i:s');
         $idate = substr($idate,0,10)." ".$string;
      }
      return;
   }

   /**
    * Method to change the issue summary (title).
    *
    * @param string $title The title
    * @return string $title The modified title
   */

   private function _generateNewSummary($title)
   {
      // Alter the title
      $title .= ' (2)';
      return $title;
   }

/*
   private function _generateNewSummary($title)
   {
      // Alter the title
      $table = $this->getTable();
      while ($table->load(array('issue_summary' => $title))) {
         $m = null;
         if (preg_match('#\((\d+)\)$#', $title, $m)) {
            $title = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $title);
         } else {
            $title .= ' (2)';
         }
      }
      return $title;
   }
*/

   /**
    * Method to test whether a record can be deleted.
    *
    * @param   object   $record  A record object.
    *
    * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
    * @since   1.6
    */
   protected function canDelete($record)
   {
      if (!empty($record->id)) {
         if ($record->state != -2) {
            return true;
         }
         $user = JFactory::getUser();
         return $user->authorise('core.delete', 'com_issuetracker.issues.'.(int) $record->id);
      }
      return false;
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
      // $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
      $pks = (array) $pks;

      $row = $this->getTable();

      // Set reference to parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $app = JFactory::getApplication();
      $delmode = $this->_params->get('delete', 0);

      if ($delmode == 0 ) {
         // Delete mode disabled.  Should give a message as well.
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_DISABLED_MSG'));
         return false;
      } else if ( $delmode == 1 || $delmode == 2 ) {
         // Iterate the items to delete each one.
         foreach ($pks as $pk)
         {
            // Remove attachments if any.
            $this->delete_attachments($pk);

            // Remove progress records.
            $this->delete_progress_recs($pk);

            if (!$row->delete( $pk )) {
               $this->setError( $row->getError() );
               return false;
            }
         }
         return true;
      } else if ( $delmode > 2 ) {
         // Unknown mode.  Mode 2 is only applicable for user deletion..
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_UNKNOWN_MSG'),'error');
         return false;
      }
      return false;
   }

   /**
    * Method to get User's default defined project
    *
    * @param string $userid Input user identifier
    *
    * @return string        The users default project id.
    *
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
    * Method to get User's default defined project
    *
    * @param $id
    * @return string The users default project id.
    *
    * @internal param string $userid Input user identifier
    */
   public function getDefProject2($id)
   {
      // Load the data
      $query = 'SELECT assigned_project FROM `#__it_people` WHERE `id` = '.$id;
      $this->_db->setQuery( $query );
      $projid = $this->_db->loadResult();

      return $projid;
   }

   /**
    * Method to get the Project target end date if set.
    * @param $projid
    * @return mixed|null $tdate
    */
   public function getProjectTargetDate($projid)
   {
      $tdate = null;
      // Load the data
      $query = 'SELECT target_end_date FROM `#__it_projects` WHERE `id` = '.$projid;
      $this->_db->setQuery( $query );
      $tdate = $this->_db->loadResult();

      return $tdate;
   }

   /**
    * Method to get Anonymous user id
    * @return integer $aid if found 0 otherwise
    */
   public function getAnonymousId()
   {
      // Load the data

      // Set up access to parameters
      $params = JComponentHelper::getParams( 'com_issuetracker' );
      $def_identby   = $params->get('def_identifiedby','0');
      return $def_identby;

      /*
      $query = "SELECT id FROM `#__it_people` WHERE `id` = 'Anonymous'";
      $this->_db->setQuery( $query );
      $aid = $this->_db->loadResult();

      if (empty($aid) ) $aid=0;

      return $aid;
      */
   }

   /**
    * Method to remove any progress records associated with the issue.
    *
    * @param integer $issue Input issue identifier.
    *
    * @return boolean Return true or the number of records deleted.
    */
   private function delete_progress_recs($issue)
   {
      $query  = "SELECT count(*) FROM `#__it_progress` WHERE issue_id = '".$issue."'";
      $this->_db->setQuery( $query );
      $delcnt = $this->_db->loadResult();

      if ( $delcnt > 0 ) {
         $query  = "DELETE FROM `#__it_progress` WHERE issue_id = '".$issue."'";
         $this->_db->setQuery( $query );
         $delcnt = $this->_db->loadResult();
         return $delcnt;
      }

      return true;
   }

   /**
    * Method to remove any attachments associated with the issue.
    *
    * @param integer $issue Input issue identifier.
    *
    * @return boolean Return true or the number of attachments deleted.
    */
   private function delete_attachments($issue)
   {
      $query  = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = ";
      $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue."')";
      $this->_db->setQuery( $query );
      $delcnt = $this->_db->loadResult();

      if ( $delcnt > 0 ) {
         JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_issuetracker/tables');
         $row = JTable::getInstance('Attachment', 'IssueTrackerTable', array());

         $query  = "SELECT id FROM `#__it_attachment` WHERE issue_id = ";
         $query .= "(SELECT alias FROM `#__it_issues` WHERE id = '".$issue."')";
         $this->_db->setQuery( $query );
         $pks = $this->_db->loadColumn();

         foreach ( $pks as $pk) {
            $row->delete($pk);
         }
         return $delcnt;
      }

      return true;
   }

}