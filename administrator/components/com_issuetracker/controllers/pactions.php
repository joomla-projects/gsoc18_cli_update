<?php
/*
 *
 * @Version       $Id: pactions.php 2167 2016-01-01 16:41:39Z geoffc $
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
class IssueTrackerControllerPactions extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @return object
    */
   public function &getModel($name = 'paction', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

   /**
    * Save the manual order inputs from the pactions list page.
    *
    * @return  void
    * @since   1.6
    */
   public function saveorder()
   {
      JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

      // Get the arrays from the Request
      $order     = JFactory::getApplication()->input->get('order', null, 'post', 'array');
      $originalOrder = explode(',', JFactory::getApplication()->input->getString('original_order_values'));

      // Make sure something has changed
      if (!($order === $originalOrder)) {
         parent::saveorder();
      } else {
         // Nothing to reorder
         $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
         return;
      }
   }
}