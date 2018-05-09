<?php
/*
 *
 * @Version       $Id: jtrigger.php 2280 2016-04-24 15:54:22Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.11
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-04-24 16:54:22 +0100 (Sun, 24 Apr 2016) $
 *
 */

// No direct access
defined('_JEXEC') or die;

/**
 * jtable Table class
 */
class IssueTrackerTablejtrigger extends JTable
{
   var $id                       = null;         // Primary Key

    /**
     * Constructor
     *
     * @param JDatabaseDriver  $db database connector object
     * @internal param
     */
    public function __construct(&$db) {
        parent::__construct('#__it_triggers', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param array $array Named $array
     * @param string $ignore
     * @internal param \Named $array array
     * @return null|string null is operation was satisfactory, otherwise returns an error
     * @see    JTable:bind
     * @since  1.5
     */
    public function bind($array, $ignore = '') {

      if (isset($array['params']) && is_array($array['params'])) {
         $registry = new JRegistry();
         $registry->loadArray($array['params']);
         $array['params'] = (string) $registry;
      }

      if (isset($array['metadata']) && is_array($array['metadata'])) {
         $registry = new JRegistry();
         $registry->loadArray($array['metadata']);
         $array['metadata'] = (string) $registry;
      }
      // TODO Change to use JAccess::getActionsFromFile.
      if(!JFactory::getUser()->authorise('core.admin', 'com_issuetracker.jtrigger.'.$array['id'])){
         $actions = JFactory::getACL()->getActions('com_issuetracker','jtrigger');
         $default_actions = JFactory::getACL()->getAssetRules('com_issuetracker.jtrigger.'.$array['id'])->getData();
         $array_jaccess = array();
         foreach($actions as $action){
            $array_jaccess[$action->name] = $default_actions[$action->name];
         }
         $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
      }
      //Bind the rules for ACL where supported.
      if (isset($array['rules']) && is_array($array['rules'])) {
         $this->setRules($array['rules']);
      }

        return parent::bind($array, $ignore);
    }

    /**
     * This function convert an array of JAccessRule objects into an rules array.
     * @param array $jaccessrules an arrao of JAccessRule objects.
     * @return array
     */
   private function JAccessRulestoArray($jaccessrules){
      $rules = array();
      foreach($jaccessrules as $action => $jaccess){
         $actions = array();
         foreach($jaccess->getData() as $group => $allow){
            $actions[$group] = ((bool)$allow);
         }
         $rules[$action] = $actions;
      }
      return $rules;
   }

   /**
    * Overloaded check function
    */
   public function check() {

      //If there is an ordering column and this is a new row then get the next ordering value
      if (property_exists($this, 'ordering') && $this->id == 0) {
         $this->ordering = self::getNextOrder();
      }

      return parent::check();
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

      // Set up audit fields in here.
      $this->created_on = $date->toSql();
      $this->created_by = $user->id;
      $this->created_by_alias = $user->username;

      $result = parent::store($updateNulls);
      return $result;

   }

   /**
    * Method to set the publishing state for a row or list of rows in the database
    * table.  The method respects checked out rows by other users and will attempt
    * to checkin rows that it can after adjustments are made.
    *
    * @param    mixed $pks An optional array of primary key values to update.  If not
    *                    set the instance property value is used.
    * @param int $state
    * @param    integer $userId The user id of the user performing the operation.
    * @internal param int $status The publishing state. eg. [0 = unpublished, 1 = published]
    * @return    boolean    True on success.
    * @since    1.0.4
    */
   public function publish($pks = null, $state = 1, $userId = 0) {
      // Initialise variables.
      $k = $this->_tbl_key;

      // Sanitize input.
      JArrayHelper::toInteger($pks);
      $userId = (int) $userId;
      $state = (int) $state;

      // If there are no primary keys set check to see if the instance key is set.
      if (empty($pks)) {
         if ($this->$k) {
            $pks = array($this->$k);
         }
         // Nothing to set publishing state on, return false.
         else {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
            return false;
         }
      }

      // Build the WHERE clause for the primary keys.
      $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

      // Determine if there is checkin support for the table.
      if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
         $checkin = '';
      } else {
         $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
      }

      // Update the publishing state for rows with the given primary keys.
      $this->_db->setQuery(
          'UPDATE `' . $this->_tbl . '`' .
          ' SET `state` = ' . (int) $state .
          ' WHERE (' . $where . ')' .
          $checkin
      );
      $this->_db->execute();

      // Check for a database error.
      if ($this->_db->getErrorNum()) {
         $this->setError($this->_db->getErrorMsg());
         return false;
      }

      // If checkin is supported and all rows were adjusted, check them in.
      if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
         // Checkin each row.
         foreach ($pks as $pk) {
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
     * Define a namespaced asset name for inclusion in the #__assets table
     * @return string The asset name
     *
     * @see JTable::_getAssetName
   */
   protected function _getAssetName() {
      $k = $this->_tbl_key;
      return 'com_issuetracker.jtrigger.' . (int) $this->$k;
   }

   /**
    * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
    *
    * @see JTable::_getAssetParentId
    * @param null $table
    * @param null $id
    * @return int
    */
   protected function _getAssetParentId(JTable $table = null, $id = null){
      // We will retrieve the parent-asset from the Asset-table
      $assetParent = JTable::getInstance('Asset');
      // Default: if no asset-parent can be found we take the global asset
      $assetParentId = $assetParent->getRootId();
      // The item has the component as asset-parent
      $assetParent->loadByName('com_issuetracker');
      // Return the found asset-parent-id
      if ($assetParent->id){
         $assetParentId=$assetParent->id;
      }
      return $assetParentId;
   }

   /*
    * Functions to toggle  table fields on or off
    *
    */

   /**
    * @param null $pks
    * @param int $state
    * @param int $userId
    * @param string $ttype
    * @return bool
    */
   public function toggle($pks = null, $state = 1, $userId = 0, $ttype = 'enabletrig' )
   {
      // Initialise variables.
      $k = $this->_tbl_key;

      if (empty($ttype)) {
         $this->setError(JText::_('COM_ISSUETRACKER_NO_TOGGLE_SPECIFIED'));
         return false;
      }

      // Sanitize input.
      JArrayHelper::toInteger($pks);
      $userId = (int) $userId;
      $state  = (int) $state;

      // If there are no primary keys set check to see if the instance key is set.
      if (empty($pks))
      {
         if ($this->$k) {
            $pks = array($this->$k);
         }
         // Nothing to set publishing state on, return false.
         else {
            $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
            return false;
         }
      }

      // Build the WHERE clause for the primary keys.
      $where = $k.'='.implode(' OR '.$k.'=', $pks);


      // Determine if there is checkin support for the table.
      if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
         $checkin = '';
      }
      else {
         $checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
      }

      // Update the applied field for rows with the given primary keys.
      switch ($ttype) {
         case 'enabletrig':
            if ($state == 1) {
               foreach ($pks as $pk) {
                  $this->_db->setQuery('SELECT trigger_text FROM `'.$this->_tbl.'` WHERE id = '.$pk);
                  $ttext = $this->_db->loadResult();
                  if (!empty($ttext)) {
                     $this->_db->setQuery( strip_tags($ttext) );
                     $this->_db->execute();
                  }
               }
            } else {
               foreach ($pks as $pk) {
                  $this->_db->setQuery('SELECT trigger_name FROM `'.$this->_tbl.'` WHERE id = '.$pk);
                  $tname = $this->_db->loadResult();
                  $this->_db->setQuery("DROP TRIGGER IF EXISTS `".$tname."`");
                  $this->_db->execute();
               }
            }

            $this->_db->setQuery(
               'UPDATE `'.$this->_tbl.'`' .
               ' SET `applied` = '.(int) $state .
               ' WHERE ('.$where.')' .
               $checkin
            );
            break;
         default:
            $this->setError(JText::_('COM_ISSUETRACKER_INVALID_TOGGLE_FIELD'));
            return false;
      }

      try {
         $this->_db->execute();
      }
      catch (RuntimeException $e) {
         $this->setError($e->getMessage());
         return false;
      }

      // If checkin is supported and all rows were adjusted, check them in.
      if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
      {
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