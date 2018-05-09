<?php
/*
 *
 * @Version       $Id: paction.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Table
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerTablePaction extends JTable
{
   var $id                       = null;         // Primary Key
   var $asset_id                 = null;
   var $alias                    = null;
   var $status                   = null;
   var $state                    = null;
   var $public                   = null;
   var $checked_out              = null;
   var $checked_out_time         = null;
   var $ordering                 = null;
   var $progress                 = null;
   var $created_on               = null;
   var $created_by               = null;
   var $modified_on              = null;
   var $modified_by              = null;

   /**
    * Constructor
    *
    * @param   JDatabaseDriver $db A database connector object
    *
    * @return \IssueTrackerTablePaction Progress@since   11.1
    */
   function __construct(&$db)
   {
      parent::__construct('#__it_progress', 'id', $db);

   }


   /**
    * Overloaded bind function
    *
    * @param   array  $array   Named array
    * @param   mixed  $ignore  An optional array or space separated list of properties
    *                          to ignore while binding.
    *
    * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
    *
    * @see     JTable:bind
    * @since   11.1
    */
   public function bind($array, $ignore = '')
   {
      if (isset($array['userdetails']) && is_array($array['userdetails'])) {
         $registry = new JRegistry;
         $registry->loadArray($array['userdetails']);
         $array['userdetails'] = (string)$registry;
      }

      // Bind the rules.
      $jversion = new JVersion();
      if (isset($data['rules']) && is_array($data['rules'])) {
         if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
            $rules = new JAccessRules($data['rules']);
         } else {
            $rules = new JRules($data['rules']);
         }
         $this->setRules($rules);
      }

      if (isset($array['metadata']) && is_array($array['metadata']))
      {
         $registry = new JRegistry;
         $registry->loadArray($array['metadata']);
         $array['metadata'] = (string) $registry;
      }

      return parent::bind($array, $ignore);
   }

   /**
    * Overloaded check function
    *
    * @return  boolean  True on success, false on failure
    *
    * @see     JTable::check
    * @since   11.1
    */
   public function check()
   {
      if (trim($this->progress) == '') {
         $this->setError(JText::_('COM_ISSUETRACKER_WARNING_PROVIDE_VALID_PROGRESS'));
         return false;
      }

      // Clean up keywords -- eliminate extra spaces between phrases
      // and cr (\r) and lf (\n) characters from string
/*
      if (!empty($this->progress)) {
         // Only process if not empty
         // $this->progress = JFilterOutput::cleanText($this->progress);
         $this->progress = strip_tags($this->progress, '<p><br>');
      }
*/

      //If there is an ordering column and this is a new row then get the next ordering value
      if (property_exists($this, 'ordering') && $this->id == 0) {
         $this->ordering = self::getNextOrder();
      }

      return parent::check();
   }

   /**
    * Override parent delete method.
    *
    * @param   integer  $pk  Primary key to delete.
    *
    * @return  boolean  True on success.
    *
    * @since   3.1
    * @throws  UnexpectedValueException
    */
   public function delete($pk = null)
   {
      // Need this since we have asset_id in the table even though we are not currently using it.
      $this->_trackAssets = false;

      $result = parent::delete($pk);
      return $result;
   }

   /**
    * Overrides JTable::store to set modified data and user id.
    *
    * @param   boolean  True to update fields even if they are null.
    *
    * @return  boolean  True on success.
    *
    * @since   11.1
    */
   public function store($updateNulls = false)
   {
      $date  = JFactory::getDate();
      $user  = JFactory::getUser();

      // Set up audit fields in here, and app defaults in the model.
      if ( ! $this->id ) {  // New record. The created_on and created_by fields can not be set by the user,
         $this->created_on = $date->toSql();
         $this->created_by = $user->get('username');
      }
      $this->modified_on   = $date->toSql();
      $this->modified_by   = $user->get('username');

      $result = parent::store($updateNulls);
      return $result;

   }

   /**
    * Method to set the publishing state for a row or list of rows in the database
    * table. The method respects checked out rows by other users and will attempt
    * to checkin rows that it can after adjustments are made.
    *
    * @param   mixed    $pks      An optional array of primary key values to update.  If not
    *                            set the instance property value is used.
    * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
    * @param   integer  $userId  The user id of the user performing the operation.
    *
    * @return  boolean  True on success.
    *
    * @since   11.1
    */
   public function publish($pks = null, $state = 1, $userId = 0)
   {
      // Initialise variables.
      $k = $this->_tbl_key;

      // Sanitize input.
      JArrayHelper::toInteger($pks);
      $userId = (int) $userId;
      $state  = (int) $state;

      // If there are no primary keys set check to see if the instance key is set.
      if (empty($pks)) {
         if ($this->$k) {
            $pks = array($this->$k);
         } else {
            // Nothing to set publishing state on, return false.
            $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
            return false;
         }
      }

      // Build the WHERE clause for the primary keys.
      $where = $k.'='.implode(' OR '.$k.'=', $pks);

      // Set the JDatabaseQuery object now to work with the below if clause
      // $query = $this->_db->getQuery(true);

      // Determine if there is checkin support for the table.
      if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
         $checkin = '';
      } else {
         $checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
      }

      // Update the publishing state for rows with the given primary keys.
      $this->_db->setQuery(
         'UPDATE '.$this->_db->quoteName($this->_tbl) .
         ' SET '.$this->_db->quoteName('state').' = '.(int) $state .
         ' WHERE ('.$where.')' .
         $checkin
      );

      try {
         $this->_db->execute();
      }
      catch (RuntimeException $e) {
         $this->setError($e->getMessage());
         return false;
      }

      // If checkin is supported and all rows were adjusted, check them in.
      if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
         // Checkin the rows.
         foreach($pks as $pk) {
            $this->checkin($pk);
         }
      }

      // If the JTable instance value is in the list of primary keys that were set, set the instance.
      if (in_array($this->$k, $pks)) {
         $this->state = $state;
      }

      $this->setError('');
      return true;
   }
}