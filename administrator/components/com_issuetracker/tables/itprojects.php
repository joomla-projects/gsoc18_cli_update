<?php
/*
 *
 * @Version       $Id: itprojects.php 2167 2016-01-01 16:41:39Z geoffc $
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

JLoader::import('joomla.database.tablenested');

/**
 * Issue Tracker Table
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerTableItprojects extends JTableNested
{
   /**
    * Helper object for storing and deleting tag information.
    *
    * @var    JHelperTags
    * @since  3.1
    */
   // protected $tagsHelper = null;

   var $id                    = null;       // Primary Key
   var $parent_id             = null;
   var $title                 = null;
   var $alias                 = null;
   var $customfieldsgroup     = null;
   var $description           = null;
   var $state                 = null;
   var $ordering              = null;
   var $checked_out           = null;
   var $checked_out_time      = null;
   var $start_date            = null;
   var $target_end_date       = null;
   var $actual_end_date       = null;
   var $created_on            = null;
   var $created_by            = null;
   var $modified_on           = null;
   var $modified_by           = null;
   var $lft                   = null;
   var $level                 = null;
   var $rgt                   = null;
   var $path                  = null;


    /**
     * Constructor
     *
     * @param JDatabaseDriver  $db A database connector object
     * @internal param
     */
   function __construct(&$db)
   {
      parent::__construct('#__it_projects', 'id', $db);
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JTableObserverTags::createObserver($this, array('typeAlias' => 'com_issuetracker.itproject'));
      }
   }

   /**
    * Overloaded bind function to pre-process the params.
    *
    * @param mixed $array
    * @param string $ignore
    * @internal param array $arry Named array
    * @internal param \Named $array array
    *
    * @return  null|string null is operation was satisfactory, otherwise returns an error
    * @see     JTable:bind
    * @since   1.5
    */
   public function bind($array, $ignore = '')
   {
      if (isset($array['params']) && is_array($array['params']))  {
         $registry = new JRegistry;
         $registry->loadArray($array['params']);
         $array['params'] = (string) $registry;
      }

      // Bind the rules.
      if (isset($data['rules']) && is_array($data['rules'])) {
         $jversion = new JVersion();
         if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
            $rules = new JAccessRules($data['rules']);
         } else {
            $rules = new JRules($data['rules']);
         }
         $this->setRules($rules);
      }

      if (isset($array['metadata']) && is_array($array['metadata'])) {
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
    * @see JTable::check
    * @since 1.5
    */
   function check()
   {
      //If there is an ordering column and this is a new row then get the next ordering value
      if (property_exists($this, 'ordering') && $this->id == 0) {
         $this->ordering = self::getNextOrder();
      }

      // Data validation code
      if (trim($this->title) == '') {
         $this->setError(JText::_('COM_ISSUETRACKER_WARNING_PROVIDE_VALID_PROJECT_NAME'));
         return false;
      }

/*
      if (!empty($this->description)) {
         // Only process if not empty
         $this->description = JFilterOutput::cleanText($this->description);
      }
*/

      $this->alias = trim($this->alias);
      if (empty($this->alias)) {
         $this->alias = $this->title;
      }

      $this->alias = JApplication::stringURLSafe($this->alias);
      if (trim(str_replace('-', '', $this->alias)) == '') {
         $this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
      }

      return parent::check();
   }

   /**
    * Add the root node to an empty table.
    *
    * @return    integer  The id of the new root node.
    */
   public function addRoot()
   {
       $db = JFactory::getDbo();
       $sql = 'INSERT INTO `#__it_projects` '
           . ' SET parent_id = 0'
           . ', lft = 0'
           . ', rgt = 1'
           . ', level = 0'
           . ', title = '.$db->quote( 'Root' )
           . ', description = '.$db->quote( 'Root' )
           . ', alias = '.$db->quote( 'Root' )
           . ', access = 1'
           . ', path = '.$db->quote( '' )
           ;
       $db->setQuery( $sql );
       $db->execute();

       return $db->insertid();
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
      if ( ! $this->id ) { // New project. A project created_on and created_by field can not be set by the user,
         $this->created_on = $date->toSql();
         $this->created_by = $user->get('username');
      }
      $this->modified_on   = $date->toSql();
      $this->modified_by   = $user->get('username');

      // Verify that the alias is unique
      $table = JTable::getInstance('Itprojects','IssueTrackerTable');
      if ($table->load(array('alias'=>$this->alias, 'parent_id'=>$this->parent_id)) && ($table->id != $this->id || $this->id==0)) {
         $this->setError(JText::_('COM_ISSUETRACKER_ERROR_UNIQUE_ALIAS'));
         return false;
      }

      $result = parent::store($updateNulls);

      return $result;
   }

   /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param    mixed    $pks An optional array of primary key values to update.  If not
     *                         set the instance property value is used.
     * @param    integer  $state The publishing state. eg. [0 = unpublished, 1 = published]
     * @param    integer  $userId The user id of the user performing the operation.
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
    * Method to delete a node and, optionally, its child nodes from the table.
    *
    * @param   integer  $pk        The primary key of the node to delete.
    * @param   boolean  $children  True to delete child nodes, false to move them up a level.
    *
    * @return  boolean  True on success.
    *
    * @see     http://docs.joomla.org/JTableNested/delete
    * @since   2.5
    */
   public function delete($pk = null, $children = false)
   {
      $result = parent::delete($pk, $children);
      if ( $result) {
         // Also remove the ucm_content entry, present if tags were used!
         $ucmContentTable = JTable::getInstance('Corecontent');
         $ucmContentTable->deleteByContentId($pk, 'com_issuetracker.itproject');
      }
      return $result;
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
       return 'com_issuetracker.itprojects.' . (int) $this->$k;
   }

   /**
    * Method to return the title to use for the asset table.
    *
    * @return      string
    * @since       2.5
    */
   protected function _getAssetTitle()
   {
      return 'Project_'.$this->alias;
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

   /**
    * Rebuild the table.
    *
    * Copy for Joomla version since we have an ordering column in our projects table which messes up the
    * rebuild so it returns to what it was. Dohhh.
    *
    * @param null   $parentId
    * @param int    $leftId
    * @param int    $level
    * @param string $path
    * @internal param JTable|null $table
    * @internal param null $id
    * @return  int
    * @since   2.5
    */
   public function rebuild($parentId = null, $leftId = 0, $level = 0, $path = '')
   {
      // If no parent is provided, try to find it.
      if ($parentId === null) {
         // Get the root item.
         $parentId = $this->getRootId();

         if ($parentId === false) {
            return false;
         }
      }

      $query = $this->_db->getQuery(true);

      // Build the structure of the recursive query.
      if (!isset($this->_cache['rebuild.sql'])) {
         $query->clear()
            ->select($this->_tbl_key . ', alias')
            ->from($this->_tbl)
            ->where('parent_id = %d');

         // If the table has an ordering field, use that for ordering.
//         if (property_exists($this, 'ordering'))  {
//            $query->order('parent_id, ordering, lft');
//         } else {
            $query->order('parent_id, lft');
//         }
         $this->_cache['rebuild.sql'] = (string) $query;
      }

      // Make a shortcut to database object.

      // Assemble the query to find all children of this node.
      $this->_db->setQuery(sprintf($this->_cache['rebuild.sql'], (int) $parentId));

      $children = $this->_db->loadObjectList();

      // The right value of this node is the left value + 1
      $rightId = $leftId + 1;

      // Execute this function recursively over all children
      foreach ($children as $node) {
         /*
         * $rightId is the current right value, which is incremented on recursion return.
         * Increment the level for the children.
         * Add this item's alias to the path (but avoid a leading /)
         */
         $rightId = $this->rebuild($node->{$this->_tbl_key}, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->alias);

         // If there is an update failure, return false to break out of the recursion.
         if ($rightId === false) {
            return false;
         }
      }

      // We've got the left value, and now that we've processed
      // the children of this node we also know the right value.
      $query->clear()
         ->update($this->_tbl)
         ->set('lft = ' . (int) $leftId)
         ->set('rgt = ' . (int) $rightId)
         ->set('level = ' . (int) $level)
         ->set('path = ' . $this->_db->quote($path))
         ->where($this->_tbl_key . ' = ' . (int) $parentId);
      $this->_db->setQuery($query)->execute();

      // Return the right value of this node + 1.
      return $rightId + 1;
   }
}