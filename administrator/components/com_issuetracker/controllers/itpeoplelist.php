<?php
/*
 *
 * @Version       $Id: itpeoplelist.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
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
 * Class IssueTrackerControllerItpeoplelist
 */
class IssueTrackerControllerItpeoplelist extends JControllerAdmin
{
   protected   $option     = 'com_issuetracker';

   /**
    * @param array $config
    */
   public function __construct($config = array())
   {
      parent::__construct($config);

      $this->registerTask('notadministrator',   'administrator');
      $this->registerTask('nonotify',     'notify');
      $this->registerTask('notstaff',     'staff');
      $this->registerTask('nosmsnotify',  'smsnotify');
   }

   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @param array  $config
    * @return object
    */
   public function getModel($name = 'itpeople', $prefix = 'IssuetrackerModel', $config = array('ignore_request' => true))
   {
      $model = parent::getModel($name, $prefix, $config);
      return $model;
   }

   function administrator()
   {
      // Check for request forgeries
      // JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
      JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      // $cid  = JRequest::getVar('cid', array(), '', 'array');
      $cid     = JFactory::getApplication()->input->get('cid', array(), '', 'array');
      $data    = array('administrator' => 1, 'notadministrator' => 0);
      $task    = $this->getTask();
      $value   = JArrayHelper::getValue($data, $task, 0, 'int');

      if (empty($cid)) {
         JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.

         if (!$model->administration($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            $ntext = $this->text_prefix.'_N_ITEMS_ADMINISTRATOR';
            if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTADMINISTRATOR';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }

   function notify()
   {
      // Check for request forgeries
      // JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
      JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      // $cid  = JRequest::getVar('cid', array(), '', 'array');
      $cid     = JFactory::getApplication()->input->get('cid', array(), '', 'array');
      $data = array('notify' => 1, 'nonotify' => 0);
      $task    = $this->getTask();
      $value   = JArrayHelper::getValue($data, $task, 0, 'int');

      if (empty($cid)) {
         JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.

         if (!$model->notify($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            $ntext = $this->text_prefix.'_N_ITEMS_NOTIFIED';
            if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTNOTIFIED';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }

   function staff()
   {
      // Check for request forgeries
      // JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
      JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      // $cid  = JRequest::getVar('cid', array(), '', 'array');
      $cid     = JFactory::getApplication()->input->get('cid', array(), '', 'array');
      $data    = array('staff' => 1, 'notstaff' => 0);
      $task    = $this->getTask();
      $value   = JArrayHelper::getValue($data, $task, 0, 'int');

      if (empty($cid)) {
         JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.

         if (!$model->staff($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            $ntext = $this->text_prefix.'_N_ITEMS_STAFF';
            if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTSTAFF';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }

   function smsnotify()
   {
      // Check for request forgeries
      // JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));
      JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      // $cid  = JRequest::getVar('cid', array(), '', 'array');
      $cid     = JFactory::getApplication()->input->get('cid', array(), '', 'array');
      $data = array('smsnotify' => 1, 'nosmsnotify' => 0);
      $task    = $this->getTask();
      $value   = JArrayHelper::getValue($data, $task, 0, 'int');

      if (empty($cid)) {
         JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.

         if (!$model->smsnotify($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            $ntext = $this->text_prefix.'_N_ITEMS_SMSNOTIFIED';
            if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_NOTSMSNOTIFIED';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }

}
