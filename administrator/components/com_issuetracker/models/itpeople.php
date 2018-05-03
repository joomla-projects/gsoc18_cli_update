<?php
/*
 *
 * @Version       $Id: itpeople.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import('joomla.application.component.modeladmin');

/**
 * Issue Tracker Model
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerModelItpeople extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type The type
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array $config Configuration array for model. Optional.
     * @internal param $type The table type to instantiate
     * @return  JTable   A database object
     * @since   1.6
     */
   public function getTable($type = 'Itpeople', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    * Method to get the record form.
    *
    * @param   array $data    An optional array of data for the form to interogate.
    * @param   boolean  $loadData   True if the form is to load its own data (default case), false if not.
    * @return  JForm A JForm object on success, false on failure
    * @since   1.6
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Initialise variables.
      // $app  = JFactory::getApplication();

      // Get the form.
      $form = $this->loadForm('com_issuetracker.itpeople', 'itpeople', array('control' => 'jform', 'load_data' => $loadData));
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
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.itpeople.data', array());

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
      if ($item = parent::getItem($pk)) {

         //Do any procesing on fields here if needed

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
            $db->setQuery('SELECT MAX(ordering) FROM #__it_people');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }

      }
   }

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
         return $user->authorise('core.delete', 'com_issuetracker.people.'.(int) $record->id);
      }
      return false;
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

      // Get default settings
      $def_project   = $this->_params->get('def_project', 1);
      // $def_published = $this->_params->get('def_published', 0);

      // Ensure FK relationship is set
      // If assigned_project is empty set it to default project.  Also enforced in DB.
      //     if "CEO" || "Manager"
      if ( $data['person_role'] == "1" || $data['person_role'] == "4" ) {
         $data['assigned_project'] = NULL;
      } else {
         // Get default from parameters if not specified.
         if (empty($data['assigned_project']) || $data['assigned_project'] == 0)
         {
            $data['assigned_project'] = $def_project;
         }
      }

      if (parent::save($data)) {
         // Force database change for role change to CEO or Manager.
         if ( $data['person_role'] == "1" || $data['person_role'] == "4" ) {
            $query = "UPDATE `#__it_people` SET assigned_project = NULL WHERE id = ".$data['id'];
            $this->_db->setQuery( $query );
            $this->_db->execute();
         }
         return true;
      }

      return false;
   }

   /**
    * Method to store record(s)
    *
    * @access  public
    * @return  boolean  True on success
    */
   public function store()
   {
      // This is not being used as it uses teh save routine above.
      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $def_project   = $this->_params->get('def_project', 1);

      $row =& $this->getTable('itpeople','IssueTrackerTable');

      $data = JFactory::getApplication()->input->get('post');
      // HTML content must be required!
      $data['id'] = JFactory::getApplication()->input->get('id', '', 'post', 'double');
      $data['assigned_project'] = JFactory::getApplication()->input->get('assigned_project', '', 'post', 'double');

      // Ensure FK relationship is set
      // If assigned_project is empty set it to default project.  Also enforced in DB.
      //     if ( $data['person_role'] == "CEO" || $data['person_role'] == "Manager" ) {
      if ( $data['person_role'] == "1" || $data['person_role'] == "4" ) {
         $data['assigned_project'] = NULL;
      } else {
         // Get default from parameters if not specified.
         if (empty($data['assigned_project']) || $data['assigned_project'] == 0)
         {
            $data['assigned_project'] = $def_project;
         }
      }

      // Audit data set in the table definition.

      // Bind the form fields to the table
      if (!$row->bind($data)) {
         $this->setError($this->_db->getErrorMsg());
         return false;
      }

      // Make sure the record is valid
      if (!$row->check()) {
         $this->setError($this->_db->getErrorMsg());
         return false;
      }

      // Store the web link table to the database
      if (!$row->store()) {
         $this->setError( $row->getError() );
         return false;
      }
      return true;
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

      $row =& $this->getTable();

      // Soft or hard delete?
      // Get parameters setting and default user if soft deleting.
      // Set reference to parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );
      $app =& JFactory::getApplication();
      $delmode = $this->_params->get('delete', 0);
      $def_assignee = $this->_params->get('def_assignee', 0);

      if ($delmode == 0 ) {
         // Delete mode disabled.  Should give a message as well.
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_DISABLED_MSG'));
         return false;
/*
      } else if ( $delmode == 1 ) {
         // Give a warning about deleting associated issues.
         // $message = preg_replace("/\r?\n/", "\\n", JText::_('COM_ISSUETRACKER_DELETE_WARNING'));

         if (count( $pks )) {
            // Need to check that we are not deleting the default user!
            $msg="";

            // Iterate the items to delete each one.
            foreach ($pks as $i => $pk) {
               if (!$row->delete( $pk )) {
                  $this->setError( $row->getError() );
                  return false;
               }
            }

            if ( $msg != "" ) {
               $app->enqueueMessage($msg);
            }
            $app->enqueueMessage(JText::_('COM_ISSUETRACKER_PEOPLE_ISSUES_DELETED_MSG'));
         }
         return true;
*/
      } else if ( $delmode == 2 || $delmode == 1 ) {
         // update rows to be userid.
         $deluser = $this->_params->get('deleteUser', 0);
         if ($deluser == 0 ) {
            $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_REASSIGNMENT_USER_NOT SPECIFIED_MSG'));
            return false;
         } else {
            // Need to check that we are not deleting the default user!
            $msg="";

            if (count( $pks )) {
               if ( $pks == $def_assignee ) {
                  $msg = JText::_('COM_ISSUETRACKER_ERROR_DELETE_DEFAULT_USER_MSG');
               } else {
                  // Update associated issues
                  $query   = 'UPDATE `#__it_issues` SET assigned_to_person_id = '.$def_assignee.', identified_by_person_id = '.$deluser;
                  $query  .= ' WHERE assigned_to_person_id in (';
                  $query1  = ' OR identified_by_person_id in (';

                  foreach($pks as $pk) {
                     $query .= $pk . ',';
                     $query1 .= $pk .',';
                  }

                  $query = substr($query, 0, -1) . ')' . substr($query1, 0, -1) .')' ;

                  $this->_db->setQuery( $query );
                  $this->_db->execute();
                  $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_REASSIGNED_MSG'));

                  // Now delete the users themselves
                  // Iterate the items to delete each one.
                  foreach ($pks as $pk) {
                     if (!$row->delete( $pk )) {
                        $this->setError( $row->getError() );
                        return false;
                     }
                  }
               }
            }

            if ( $msg != "" ) {
               $app->enqueueMessage($msg);
            }
            return true;
         }
      } else {
         // Unknown mode Give message.
         $app->enqueueMessage(JText::_('COM_ISSUETRACKER_DELETE_MODE_UNKNOWN_MSG'),'error');
         return false;
      }
   }

   /**
    * @param $pks
    * @param int $value
    * @return bool
    */
   function administration(&$pks, $value = 1)
   {
      // Initialise variables.
      // $dispatcher = JDispatcher::getInstance();
      $user       = JFactory::getUser();
      $table      = $this->getTable('itpeople');
      $pks        = (array) $pks;

      // Include the content plugins for the change of state event.
      JPluginHelper::importPlugin('content');

      // Access checks.
      foreach ($pks as $i => $pk) {
         if ($table->load($pk)) {
            if (!$this->canEditState($table)) {
               // Prune items that you can't change.
               unset($pks[$i]);
               JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_STATE_NOT_PERMITTED'));
            }
         }
      }

      // Attempt to change the state of the records.
      if (!$table->toggle($pks, $value, $user->get('id'),'admin')) {
         $this->setError($table->getError());
         return false;
      }

      // $context = $this->option.'.'.$this->name;

      // Trigger the onContentChangeState event.
      /* $result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));
      if (in_array(false, $result, true)) {
         $this->setError($table->getError());
         return false;
      } */

      return true;
   }


   /**
    * @param $pks
    * @param int $value
    * @return bool
    */
   function notify(&$pks, $value = 1)
   {
      // Initialise variables.
      // $dispatcher = JDispatcher::getInstance();
      $user       = JFactory::getUser();
      $table      = $this->getTable('itpeople');
      $pks        = (array) $pks;

      // Include the content plugins for the change of state event.
      JPluginHelper::importPlugin('content');

      // Access checks.
      foreach ($pks as $i => $pk) {
         if ($table->load($pk)) {
            if (!$this->canEditState($table)) {
               // Prune items that you can't change.
               unset($pks[$i]);
               JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_STATE_NOT_PERMITTED'));
            }
         }
      }

      // Attempt to change the state of the records.
      if (!$table->toggle($pks, $value, $user->get('id'),'notify')) {
         $this->setError($table->getError());
         return false;
      }

      // $context = $this->option.'.'.$this->name;

      // Trigger the onContentChangeState event.
      /* $result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));
      if (in_array(false, $result, true)) {
         $this->setError($table->getError());
         return false;
      } */

      return true;
   }

   /**
    * @param $pks
    * @param int $value
    * @return bool
    */
   function staff(&$pks, $value = 1)
   {
      // Initialise variables.
      // $dispatcher = JDispatcher::getInstance();
      $user       = JFactory::getUser();
      $table      = $this->getTable('itpeople');
      $pks        = (array) $pks;

      // Include the content plugins for the change of state event.
      JPluginHelper::importPlugin('content');

      // Access checks.
      foreach ($pks as $i => $pk) {
         if ($table->load($pk)) {
            if (!$this->canEditState($table)) {
               // Prune items that you can't change.
               unset($pks[$i]);
               JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_STATE_NOT_PERMITTED'));
            }
         }
      }

      // Attempt to change the state of the records.
      if (!$table->toggle($pks, $value, $user->get('id'),'staff')) {
         $this->setError($table->getError());
         return false;
      }

      // $context = $this->option.'.'.$this->name;

      // Trigger the onContentChangeState event.
      /* $result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));
      if (in_array(false, $result, true)) {
         $this->setError($table->getError());
         return false;
      } */

      return true;
   }

   /**
    * @param $pks
    * @param int $value
    * @return bool
    */
   function smsnotify(&$pks, $value = 1)
   {
      // Initialise variables.
      // $dispatcher = JDispatcher::getInstance();
      $user       = JFactory::getUser();
      $table      = $this->getTable('itpeople');
      $pks        = (array) $pks;

      // Include the content plugins for the change of state event.
      JPluginHelper::importPlugin('content');

      // Access checks.
      foreach ($pks as $i => $pk) {
         if ($table->load($pk)) {
            if (!$this->canEditState($table)) {
               // Prune items that you can't change.
               unset($pks[$i]);
               JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDIT_STATE_NOT_PERMITTED'));
            }
         }
      }

      // Attempt to change the state of the records.
      if (!$table->toggle($pks, $value, $user->get('id'),'smsnotify')) {
         $this->setError($table->getError());
         return false;
      }

      // $context = $this->option.'.'.$this->name;

      // Trigger the onContentChangeState event.
      /* $result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));
      if (in_array(false, $result, true)) {
         $this->setError($table->getError());
         return false;
      } */

      return true;
   }

}