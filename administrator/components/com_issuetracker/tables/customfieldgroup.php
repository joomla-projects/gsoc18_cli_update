<?php
/*
 *
 * @Version       $Id: customfieldgroup.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.6
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * customfieldsrecord Table class
 */
class IssuetrackerTableCustomfieldgroup extends JTable
{
   var $id                       = null;         // Primary Key
   // var $ordering                 = null;

   /**
    * Constructor
    *
    * @param JDatabaseDriver  $db A database connector object
    */
   public function __construct(&$db)
   {
      parent::__construct('#__it_custom_field_group', 'id', $db);
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
      if ( ! $this->id ) {  // New record
         $this->created_on = $date->toSql();
         $this->created_by = $user->get('username');
      }
      $this->modified_on   = $date->toSql();
      $this->modified_by   = $user->get('username');

      return parent::store($updateNulls);
   }

   /**
    * Overloaded bind function to pre-process the params.
    *
    * @param $array
    * @param string $ignore
    * @internal param \Named $array array
    * @return  null|string null is operation was satisfactory, otherwise returns an error
    * @see     JTable:bind
    * @since   1.5
    */
   public function bind($array, $ignore = '')
   {
      if (isset($array['params']) && is_array($array['params'])) {
         $registry = new JRegistry();
         $registry->loadArray($array['params']);
         $array['params'] = (string)$registry;
      }

      if (isset($array['metadata']) && is_array($array['metadata'])) {
         $registry = new JRegistry();
         $registry->loadArray($array['metadata']);
         $array['metadata'] = (string)$registry;
      }
      return parent::bind($array, $ignore);
   }

    /**
    * Overloaded check function
    */
    public function check() {
       $this->name = JString::trim($this->name);
       if ($this->name == '') {
          $this->setError(JText::_('COM_ISSUETRACKER_GROUP_MUST_HAVE_A_NAME'));
          return false;
       }

       //If there is an ordering column and this is a new row then get the next ordering value
       if (property_exists($this, 'ordering') && $this->id == 0) {
          $this->ordering = self::getNextOrder();
       }

       return parent::check();
    }


    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to check in rows that it can after adjustments are made.
     *
     * @param    %pks mixed  An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param    $state integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    $userId integer The user id of the user performing the operation.
     * @return    boolean    True on success.
     * @since    1.0.4
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

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
            $checkin = '';
        } else {
            $checkin = ' AND (checked_out = 0 OR checked_out = '.(int) $userId.')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
            'UPDATE `'.$this->_tbl.'`' .
            ' SET `state` = '.(int) $state .
            ' WHERE ('.$where.')' .
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