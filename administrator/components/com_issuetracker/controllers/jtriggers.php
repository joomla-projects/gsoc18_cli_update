<?php
/*
 *
 * @Version       $Id: jtriggers.php 2167 2016-01-01 16:41:39Z geoffc $
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
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Jtriggers controller class.
 */
class IssuetrackerControllerJtriggers extends JControllerAdmin
{
   /**
    * @param array $config
    */
   public function __construct($config = array())
   {
      parent::__construct($config);

      $this->registerTask('disabletrig',   'enabletrig');

   }

   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @param array  $config
    * @return object
    */
   public function getModel($name = 'jtrigger', $prefix = 'IssuetrackerModel', $config = array('ignore_request' => true))
   {
      $model = parent::getModel($name, $prefix, $config);

      return $model;
   }


   /**
    * Method to save the submitted ordering values for records via AJAX.
    *
    * @return  void
    *
    * @since   3.0
    */
   public function saveOrderAjax()
   {
      // Get the input
      $input = JFactory::getApplication()->input;
      $pks = $input->post->get('cid', array(), 'array');
      $order = $input->post->get('order', array(), 'array');

      // Sanitize the input
      JArrayHelper::toInteger($pks);
      JArrayHelper::toInteger($order);

      // Get the model
      $model = $this->getModel();

      // Save the ordering
      $return = $model->saveorder($pks, $order);

      if ($return) {
         echo "1";
      }

      // Close the application
      JFactory::getApplication()->close();
   }

   function enabletrig()
   {
      // Check for request forgeries
      JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      // $cid  = JRequest::getVar('cid', array(), '', 'array');
      $cid     = JFactory::getApplication()->input->get('cid', array(), '', 'array');
      $data    = array('enabletrig' => 1, 'disabletrig' => 0);
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
         if (!$model->enabletrig($cid, $value)) {
            JError::raiseWarning(500, $model->getError());
         } else {
            $ntext = $this->text_prefix.'_N_ITEMS_ENABLED';
            if ($value == 0) {
               $ntext = $this->text_prefix.'_N_ITEMS_DISABLED';
            }
            $this->setMessage(JText::plural($ntext, count($cid)));
         }
      }

      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
   }

}