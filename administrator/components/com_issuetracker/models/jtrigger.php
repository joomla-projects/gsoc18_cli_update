<?php
/*
 *
 * @Version       $Id: jtrigger.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

// Load Audit helper
if (! class_exists('IssuetrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
if (! class_exists('IssuetrackerAuditHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'audit.php');
}
/**
 * Issuetracker model.
 */
class IssuetrackerModeljtrigger extends JModelAdmin
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
     * @internal param \The $type table type to instantiate
     * @return  JTable   A database object
     * @since   1.6
     */
   public function getTable($type = 'Jtrigger', $prefix = 'IssuetrackerTable', $config = array())
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
      $form = $this->loadForm('com_issuetracker.jtrigger', 'jtrigger', array('control' => 'jform', 'load_data' => $loadData));

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
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.jtrigger.data', array());

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

         // columns is stored as a JSON array.
         if (!empty($item->columns) ) {
            $item->columns = json_decode($item->columns);
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
      jimport('joomla.filter.output');

      if (empty($table->id)) {
         // Set ordering to the last item if not set
         if (@$table->ordering === '') {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT MAX(ordering) FROM #__it_triggers');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }
      }
   }

  /**
    * Method to save the form data.
    *
    * @param   array    $data The form data.
    *
    * @return  boolean  True on success.
    * @since   1.6
    */
   public function save($data)
   {
      // Set up access to default parameters
      $app = JFactory::getApplication();
      // echo "<pre>";var_dump($data);echo "</pre>";
      // If we have all in our array the rest are redundant so remove them.
      if ( empty($data['columns']) || (is_array($data['columns']) && in_array('All' , $data['columns'])) ) {
         $data['columns'] = array("All");
      }
      if ( !empty($data['columns'] ) ) {
         $cols = json_encode($data['columns']);
         $data['columns'] = $cols;
      }

      // Generate trigger text.
      if ( ! IssuetrackerAuditHelper::createAuditTrigger($data) ) {
         $this->setError(JText::_('COM_ISSUETRACKER_TRIGGER_ALREADY_EXISTS'));
         return false;
      }

      if (parent::save($data)) {
         // If they have enabled the trigger create it in the database
         if ( $data['applied'] == 1 ) {
            // Check if already applied if not create the trigger.
            IssuetrackerAuditHelper::applyTrigger($data);
            $app->enqueueMessage(JText::_('COM_ISSUETRACKER_TRIGGER_CREATED').$data['trigger_name']);
         } else {
            // Check if trigger exists if it does delete it.
            IssuetrackerAuditHelper::rem_trigger($data['trigger_name']);
         }

         return true;
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

      // Set reference to parameters
      $app = JFactory::getApplication();
      $row = $this->getTable();

      foreach ($pks as $pk)
      {
         $db = JFactory::getDbo();
         $db->setQuery("SELECT trigger_name FROM `#__it_triggers` WHERE id = ".$pk);
         $trigname = $db->loadResult();

         // Remove trigger from database if enabled.          ADD CHECK AS WELL.
         if (!empty ($trigname) ) {
            IssuetrackerAuditHelper::rem_trigger($trigname);
            $app->enqueueMessage(JText::_('COM_ISSUETRACKER_TRIGGER_REMOVED').$trigname);
         }

         if (!$row->delete( $pk )) {
            $this->setError( $row->getError() );
            return false;
         }
      }
      return true;
   }

   /**
    * @param $pks
    * @param int $value
    * @return bool
    */
   function enabletrig(&$pks, $value = 1)
   {
      // Initialise variables.
      // $dispatcher = JDispatcher::getInstance();
      $user       = JFactory::getUser();
      $table      = $this->getTable('jtrigger');
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

      // Attempt to create the trigger in the database.
      if (!$table->toggle($pks, $value, $user->get('id'),'enabletrig')) {
         $this->setError($table->getError());
         return false;
      }

      // $context = $this->option.'.'.$this->name;

      return true;
   }
}