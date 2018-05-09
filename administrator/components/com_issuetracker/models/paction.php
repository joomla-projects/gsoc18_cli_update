<?php
/*
 *
 * @Version       $Id: paction.php 2167 2016-01-01 16:41:39Z geoffc $
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
defined('_JEXEC') or die( 'Restricted access' );

JLoader::import('joomla.application.component.modeladmin');
JLoader::import('joomla.filesystem.folder');
JLoader::import('joomla.filesystem.file');
JLoader::import('joomla.utilities.date');

/**
 * Issuetracker Progress Actions model.
 */
class IssueTrackerModelPaction extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';

   protected function populateState()
   {
      $app = JFactory::getApplication();

      $pk = $app->input->get('id');
      $this->setState('paction.id', $pk);

      // Load state from the request.
      $return = $app->input->get('return', null, 'default', 'base64');
      $this->setState('return_page', base64_decode($return));

      // Load the parameters.
      $params = JComponentHelper::getParams('com_issuetracker');
      $this->setState('params', $params);

   }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array $config Configuration array for model. Optional.
     * @internal param $type The table type to instantiate
     * @return  JTable   A database object
     * @since   1.6
     */
   public function getTable($type = 'Paction', $prefix = 'IssueTrackerTable', $config = array())
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
      $form = $this->loadForm('com_issuetracker.paction', 'paction', array('control' => 'jform', 'load_data' => $loadData));
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
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.paction.data', array());

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
            $db->setQuery('SELECT MAX(ordering) FROM #__it_progress');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }
      }
   }

    /**
     * Method to store a record
     *
     * @access  public
     * @param array $data
     * @return  boolean  True on success
     */
   public function save($data)
   {
      // $app = JFactory::getApplication();
      // Get parameters for new user creation.
      // $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Update issue_id in record if it is a new record.
      if ( $data['id'] == 0) {
         // Set issue id and lineno to correct values!
         $db = JFactory::getDbo();
         $db->setQuery('SELECT id FROM `#__it_issues` WHERE alias = "' . $data['alias']. '"');
         $data['issue_id'] = $db->loadResult();

         $db->setQuery('SELECT MAX(lineno)+1 FROM `#__it_progress` where alias = "'.$data['alias'] . '"');
         $data['lineno'] = $db->loadResult();
      }

      JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'tables');
      $row = $this->getTable('paction','IssueTrackerTable');

      // Bind the form fields to the table
      if (!$row->bind($data)) {
         $this->setError($row->getError());
         return false;
      }

      // Make sure the record is valid
      if (!$row->check()) {
         $this->setError($row->getError());
         return false;
      }

      // Store record in the database
      if (!$row->store()) {
         $this->setError( $row->getError() );
         return false;
      }

      $this->setState($this->getName() . '.id', $row->id);

      if ( array_key_exists('id', $data) ) {
      // Ensure it is checked in.
         $pk = $data['id'];
         $this->checkin($pk);
      }

      return true;
   }


   /**
    * Returns the currently set ID
    * @return int
    */
   public function getId()
   {
      $id   = JFactory::getApplication()->input->getInt('id', 0);
      return $id;
   }

   /**
    * @return null|string
    */
   public function getReturnPage()
   {
      $app  = JFactory::getApplication();
      $val  = $app->input->get('return', null, 'base64');
      $return = base64_decode($val);

      if (!empty($return) ) {
         return $return;
      } else {
         return null;
      }
   }
}