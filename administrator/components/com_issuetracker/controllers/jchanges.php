<?php
/*
 *
 * @Version       $Id: jchanges.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Jchanges list controller class.
 */
class IssuetrackerControllerJchanges extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @param array  $config
    * @return object
    */
   public function getModel($name = 'jchange', $prefix = 'IssuetrackerModel', $config = array('ignore_request' => true))
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

      if ($return)
      {
         echo "1";
      }

      // Close the application
      JFactory::getApplication()->close();
   }
}