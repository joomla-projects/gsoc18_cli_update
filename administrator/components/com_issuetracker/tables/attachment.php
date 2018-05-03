<?php
/*
 *
 * @Version       $Id: attachment.php 2167 2016-01-01 16:41:39Z geoffc $
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
defined('_JEXEC') or die;

/**
 * attachment Table class
 */
class IssueTrackerTableAttachment extends JTable
{
   var $id                       = null;         // Primary Key

    /**
     * Constructor
     *
     * @param JDatabaseDriver  $db A database connector object
     * @internal param
     */
   public function __construct(&$db)
   {
      parent::__construct('#__it_attachment', 'id', $db);
   }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param array $array Named $array
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

      // Set up audit fields in here, and app defaults in the model.
      if ( ! $this->id ) {  // New attachment. An attachment created_on and created_by field can not be set by the user,
         $this->created_on = $date->toSQL();
         $this->created_by = $user->get('username');
      }
      $this->modified_on   = $date->toSql();
      $this->modified_by   = $user->get('username');

      return parent::store($updateNulls);
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
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

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

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
            'UPDATE `'.$this->_tbl.'`' .
            ' SET `state` = '.(int) $state .
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
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin the rows.
            foreach($pks as $pk)
            {
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
    * @return mixed
    */
   public function getType()
   {
      $type = explode("/", $this->mime);

      return $type[0];
   }

   /**
    * @return mixed
    */
   public function getSubtype()
   {
      $type = explode("/", $this->file_type);

      return $type[1];
   }

   /**
    * @return bool
    */
   public function upload()
   {
      if( empty( $this->hashname ) )
      {
         $this->hashname = $this->hash();
      }

      return $this->store();
   }

   public function rollback()
   {
      $this->delete();
   }

   /**
    * @return string
    */
   private function hash()
   {
      return md5( $this->filename . JFactory::getDate()->toSQL() );
   }

   /**
    * Method to delete a node.
    *
    * @param   integer  $pk        The primary key of the node to delete.
    *
    * @return  boolean  True on success.
    *
    * @see     http://docs.joomla.org/JTable/delete
    * @since   2.5
    */
   public function delete($pk = null)
   {
      // Get filepath and remove file from filesystem.
      // $filename = JPATH_ROOT.'/media/com_issuetracker/attachments/'.$this->hashname;
      // Get filepath and remove file from filesystem.
      $this->_db->setQuery(
         'SELECT filepath FROM  `'.$this->_tbl.'`' .
         ' WHERE id = '.(int) $pk
      );
      try {
         $filename = $this->_db->loadResult();
      }
      catch (RuntimeException $e) {
         $this->setError($e->getMessage());
         return false;
      }

      if( JFile::exists($filename) && !@unlink($filename)) {
          JLoader::import('joomla.filesystem.file');
          if(JFile::exists($filename)) {
             JFile::delete($filename);
          }
      }

      return parent::delete($pk);
   }

}
