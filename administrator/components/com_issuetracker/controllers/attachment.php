<?php
/*
 *
 * @Version       $Id: attachment.php 2167 2016-01-01 16:41:39Z geoffc $
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
defined('_JEXEC') or die( 'Restricted access' );

JLoader::import('joomla.application.component.controllerform');

/**
 * Attachment controller class.
 */
class IssueTrackerControllerAttachment extends JControllerForm
{

   /**
    *
    */
   function __construct() {
      $this->view_list = 'attachments';
      parent::__construct();
   }

   /**
    * @param null $key
    * @return bool|void
    */
   function cancel($key=null)
   {
      parent::cancel($key);

      // Entry may be from itissues, if so change redirection.
      $model = $this->getModel();

      $tt = $model->getReturnPage();
      // print("Return page $tt<p>");
      if ( strpos($tt, 'itissues') !== false ) {
         $this->setRedirect( $model->getReturnPage());
      }

      return;
   }

   /**
    * @param null $key
    * @param null $urlVar
    * @return bool|void
    */
   function save($key = null, $urlVar = null )
   {
      $task       = $this->getTask();
      parent::save($key, $urlVar);

      // Need to change redirection based on where we were called from.
      // $tt = $model->getReturnPage();
      // print("Return page $tt<p>");

      switch ( $task)
      {
         case 'apply':
         case 'save':
            // Entry may be from itissues or attachments views.
            $model = $this->getModel();
            $tt = $model->getReturnPage();
            if ( strpos($tt, 'itissues') !== false ) {
               $this->setRedirect( $model->getReturnPage());
            }
            break;
         case 'save2copy':
             // Should not ever get here!
             print("Task - $task<p>");
            die;
      }
   }

   /**
    * @return bool
    */
   public function read() {
      // Load the model
      $model = $this->getModel();
      $id = $model->getId();

      // Does the attachment exist?
      if ( $id != 0 ) {
         $model->DownloadFile($id);
      } else {
         // Should not get here.
         JError::raiseError(404, JText::_('COM_ISSUETRACKER_NO_ATTACHMENT_EXISTS'));
         return false;
      }
      return true;
   }
}