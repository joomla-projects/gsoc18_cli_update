<?php
/*
 *
 * @Version       $Id: itlog.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issuetracker Table
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssueTrackerTableItlog extends JTable
{
   var $id                 = null;   // Primary Key
   var $priority           = null;
   var $message            = null;
   var $date               = null;
   var $category           = null;

    /**
     * Constructor
     *
     * @param JDatabaseDriver  $db A database connector object
     * @internal param
     */
   function __construct(& $db)
   {
      parent::__construct('#__it_issues_log', 'id', $db);
   }

   /**
    * @return bool
    */
   function check()
   {
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
      return parent::store($updateNulls);
   }
}