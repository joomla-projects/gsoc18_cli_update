<?php
/*
 *
 * @Version       $Id: attachments.php 2167 2016-01-01 16:41:39Z geoffc $
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

JLoader::import('joomla.application.component.controlleradmin');

/**
 * Attachments list controller class.
 */
class IssueTrackerControllerAttachments extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @return object
    */
   public function &getModel($name = 'attachment', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }
}