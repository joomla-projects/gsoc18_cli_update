<?php
/*
 *
 * @Version       $Id: jchange.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined('_JEXEC') or die;

/**
 * jchange Table class
 */
class IssueTrackerTablejchange extends JTable
{
   var $id                       = null;         // Primary Key

   /**
     * Constructor
     *
     * @param JDatabaseDriver  $db A database connector object
     * @internal param
     */
    public function __construct(&$db) {
        parent::__construct('#__it_chistory', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param array $array Named array
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
       if (!JFactory::getUser()->authorise('core.admin', 'com_issuetracker.'.$array['id'])){
          $actions = JFactory::getACL()->getActions('com_issuetracker','jchange');
          $default_actions = JFactory::getACL()->getAssetRules('com_issuetracker.jchange.'.$array['id'])->getData();
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
     *
     * @param array $jaccessrules an array of JAccessRule objects.
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
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    $pks An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    integer $state The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer $userId The user id of the user performing the operation.
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
       try {
          $this->_db->execute();
       }
       catch (RuntimeException $e) {
          $this->setError($e->getMessage());
          return false;
       }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
            // Checkin each row.
            foreach ($pks as $pk) {
                $this->checkin($pk);
            }
        }

        // If the JChange instance value is in the list of primary keys that were set, set the instance.
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
      * @see JChange::_getAssetName
    */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_issuetracker.jchange.' . (int) $this->$k;
    }

   /**
    * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
    *
    * @see JChange::_getAssetParentId
    * @param JTable $table
    * @param null   $id
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
}
