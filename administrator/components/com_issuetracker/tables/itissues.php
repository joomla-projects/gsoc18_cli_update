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

/**
 * Issue Tracker Table
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerTableItissues extends JTable
{
   var $id                       = null;         // Primary Key
   var $asset_id                 = null;
   var $alias                    = null;
   var $issue_summary            = null;
   var $issue_description        = null;
   var $identified_by_person_id  = null;
   var $identified_date          = null;
   var $related_project_id       = null;
   var $assigned_to_person_id    = null;
   var $issue_type               = null;
   var $status                   = null;
   var $state                    = null;
   var $public                   = null;
   var $checked_out              = null;
   var $checked_out_time         = null;
   var $ordering                 = null;
   var $priority                 = null;
   var $target_resolution_date   = null;
   var $progress                 = null;
   var $actual_resolution_date   = null;
   var $resolution_summary       = null;
   var $created_on               = null;
   var $created_by               = null;
   var $modified_on              = null;
   var $modified_by              = null;

    /**
     * Constructor
     *
     * @param   JDatabaseDriver  $db A database connector object
     *
     * @return \IssueTrackerTableItissues
    @since   11.1
     */
   function __construct(&$db)
   {
      parent::__construct('#__it_issues', 'id', $db);

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JTableObserverTags::createObserver($this, array('typeAlias' => 'com_issuetracker.itissue'));
      }
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
      if (trim($this->issue_summary) == '') {
         $this->setError(JText::_('COM_ISSUETRACKER_WARNING_PROVIDE_VALID_SUMMARY'));
         return false;
      }

      // Clean up keywords -- eliminate extra spaces between phrases
      // and cr (\r) and lf (\n) characters from string
      if (!empty($this->issue_summary)) {
         // Only process if not empty
         $this->issue_summary = JFilterOutput::cleanText($this->issue_summary);
      }
/*
      // Split the line if no breaks provided in first 100 characters.
      $pos = strpos($this->issue_description, PHP_EOL);
      if ($pos === FALSE || $pos > 100 ) {
         $this->issue_description = wordwrap($this->issue_description, 80,'<br />');
      }
*/
      // Check for a string with no spaces that is longer than 80 characters
      if ( preg_match('/[^\s]{80,}$/', $this->issue_description) ){
         $this->issue_description = wordwrap($this->issue_description, 80,'<br />',true);
      }
/*
      if (!empty($this->issue_description)) {
         // Only process if not empty
         $this->issue_description = JFilterOutput::cleanText($this->issue_description);
      }

      if (!empty($this->resolution_summary)) {
         // Only process if not empty
         $this->resolution_summary = JFilterOutput::cleanText($this->resolution_summary);
      }

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
      $result = parent::delete($pk);
      if ( $result) {
         // Also remove the ucm_content entry, present if tags were used!
         $ucmContentTable = JTable::getInstance('Corecontent');
         $ucmContentTable->deleteByContentId($pk, 'com_issuetracker.itissue');
      }
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
      if ( ! $this->id ) {  // New issue. An issue created_on and created_by field can not be set by the user,
         $this->created_on = $date->toSql();
         $this->created_by = $user->get('username');
      }
      $this->modified_on   = $date->toSql();
      $this->modified_by   = $user->get('username');

        // Verify that the alias is unique
//        $table = JTable::getInstance('Itissues','IssueTrackerTable');
//        if ($table->load(array('alias'=>$this->alias, 'related_project_id'=>$this->related_project_id)) && ($table->id != $this->id || $this->id==0)) {
//           $this->setError(JText::_('COM_ISSUETRACKER_ERROR_UNIQUE_ALIAS'));
//           return false;
//        }

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

      // IF we are publishing check that we are not private.
      if ( $state == 1 ) {
         // print("Inputs pks:<p>");
         // echo "<pre>";var_dump($pks);echo "</pre>";
         // Get the array of elements from the db where the public flag is not 0.
         $query = "SELECT ".$k." FROM ".$this->_db->quoteName($this->_tbl);
         $query .= 'WHERE '.$k.' IN ('.implode(',', $pks).')';
         $query .= ' AND public != 0 ';
         $this->_db->setQuery($query);
         $resa = $this->_db->loadColumn();
         // print("DB returned values:<p>");
         //echo "<pre>";var_dump($resa);echo "</pre>";

         $ddd = array_intersect($resa, $pks);
         // print("Intersection:<p>");
         // echo "<pre>";var_dump($ddd);echo "</pre>";

         if (empty($ddd)) {
            // No records to set publishing state on, return false.
            $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
            return false;
         }

         $pks = $ddd;
      }

      // Build the WHERE clause for the primary keys.
      // $where = $k.'='.implode(' OR '.$k.'=', $pks);
      $where = $k.' IN ('.implode(',', $pks).')';

      // Ensure we are not setting private issues to public. Now redundant since we eliminiate above.
      // $where .= ' AND public != 0 ';

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

   /**
    * Method to compute the default name of the asset.
    * The default name is in the form `table_name.id`
    * where id is the value of the primary key of the table.
    *
    * @return  string
    * @since   2.5
    */
   protected function _getAssetName()
   {
       $k = $this->_tbl_key;
       return 'com_issuetracker.itissues.' . (int) $this->$k;
   }

   /**
    * Method to return the title to use for the asset table.
    *
    * @return      string
    * @since       2.5
    */
   protected function _getAssetTitle()
   {
      return 'Issue_'.$this->alias;
   }

   /**
    * Get the parent asset id for the record
    *
    * @param \JTable|null $table
    * @param null $id
    * @return  int
    * @since   2.5
    */
   // protected function _getAssetParentId($table = null, $id = null)
   protected function _getAssetParentId(JTable $table = null, $id = null)
   {
      $asset = JTable::getInstance('Asset');
      $asset->loadByName('com_issuetracker');
      return $asset->id;
   }
}