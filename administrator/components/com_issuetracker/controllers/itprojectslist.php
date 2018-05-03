<?php
/*
 *
 * @Version       $Id: itprojectslist.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.6
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    com_issuetracker
 */

JLoader::import('joomla.application.component.controlleradmin');

/**
 * Class IssuetrackerControllerItprojectslist
 */
class IssuetrackerControllerItprojectslist extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @return object
    */
   public function getModel($name = 'itprojects', $prefix = 'IssuetrackerModel')
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

  /**
   * Save the manual order inputs from the categories list page.
   *
   * Deprecated in Joomla 4.0
   *
   * @return  void
   * @since   1.6
   */
  public function saveorder()
  {
     JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

     // Get the arrays from the Request
     $order = $this->input->post->get('order', null, 'array');
     $originalOrder = explode(',', $this->input->getString('original_order_values'));

     // Make sure something has changed
     if (!($order === $originalOrder)) {
        parent::saveorder();
     } else {
        // Nothing to reorder
        $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
        return;
     }
  }

  /**
    * Rebuild the nested set tree.
    *
    * @return  bool  False on failure or error, true on success.
    * @since   1.6
    */
   public function rebuild()
   {
      JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

      $extension = 'com_issuetracker';
      $this->setRedirect(JRoute::_('index.php?option=com_issuetracker&view=itprojectslist&extension='.$extension, false));

      // Initialise variables.
      $model = $this->getModel();

      if ($model->rebuild()) {
         // Rebuild succeeded.
         $this->setMessage(JText::_('COM_ISSUETRACKER_PROJECTS_REBUILD_SUCCESS'));
         return true;
      } else {
         // Rebuild failed.
         $this->setMessage(JText::_('COM_ISUETRACKER_PROJECTS_REBUILD_FAILURE'));
         return false;
      }
   }

   /**
    * Deletes and returns correctly.
    *
    * @return  void
    *
    * @since   3.1.2
    */
   public function delete()
   {
      JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

      // Get items to remove from the request.
      $input = JFactory::getApplication()->input;
      $cid = $input->get('cid', array(), 'array');

      if (!is_array($cid) || count($cid) < 1) {
         JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         jimport('joomla.utilities.arrayhelper');
         JArrayHelper::toInteger($cid);

         // Remove the items.
         if ($model->delete($cid)) {
            $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
         } else {
            $this->setMessage($model->getError());
         }
      }

      $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=itprojectslist', false));
   }
}