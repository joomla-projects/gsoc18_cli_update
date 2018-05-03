<?php
/*
 *
 * @Version       $Id: itissueslist.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
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
 * Class IssueTrackerControllerItissueslist
 */
class IssueTrackerControllerItissueslist extends JControllerAdmin
{
   /**
    * Proxy for getModel.
    * @since   1.6
    * @param string $name
    * @param string $prefix
    * @param array $config
    * @return object
    */
   public function &getModel($name = 'itissues', $prefix = 'IssuetrackerModel', $config = Array())
   {
      $model = parent::getModel($name, $prefix, array('ignore_request' => true));
      return $model;
   }

   /**
    * Modified version of publish method since we are restricting private issues being made public.
    */
   public function publish()
   {
      // Check for request forgeries
      JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

      // Get items to publish from the request.
      $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
      $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
      $task = $this->getTask();
      $value = JArrayHelper::getValue($data, $task, 0, 'int');

      if ( ! empty($cid) && $value == 1 ) {
         $db = JFactory::getDbo();
         // Get the array of elements from the db where the public flag is not 0.
         $query = "SELECT id FROM `#__it_issues` ";
         $query .= 'WHERE id IN ('.implode(',', $cid).')';
         $query .= ' AND public != 0 ';
         $db->setQuery($query);
         $resa = $db->loadColumn();

         $ddd = array_intersect($resa, $cid);
         $cid = $ddd;
      }

      if (empty($cid)) {
         JLog::add(JText::_($this->text_prefix . '_NO_SUITABLE_ITEM_SELECTED'), JLog::WARNING, 'jerror');
      } else {
         // Get the model.
         $model = $this->getModel();

         // Make sure the item ids are integers
         JArrayHelper::toInteger($cid);

         // Publish the items.
         try {
            $model->publish($cid, $value);

            if ($value == 1) {
               $ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
            } elseif ($value == 0) {
               $ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
            } elseif ($value == 2) {
               $ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
            } else {
               $ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
            }

            $this->setMessage(JText::plural($ntext, count($cid)));
         }
         catch (Exception $e)
         {
             $this->setMessage($e->getMessage(), 'error');
         }
      }

      $extension = $this->input->get('extension');
      $extensionURL = ($extension) ? '&extension=' . $extension : '';
      $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
   }
}