<?php
/*
 *
 * @Version       $Id: form.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.10
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_issuetracker/models/itissues.php';

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Issue Tracker Component Issue Model
 *
 * @package    Issue Tracker
 * @subpackage com_issuetracker
 * @since 1.5
 */
class IssuetrackerModelForm extends IssuetrackerModelItissues
{
   /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    * @since   1.6
    */
   protected function populateState()
   {
      $app = JFactory::getApplication();

      // Load state from the request.

      $pk = JFactory::getApplication()->input->get('a_id');
      $this->setState('itissues.id', $pk);

      $return = JFactory::getApplication()->input->get('return', null, 'default', 'base64');
      $this->setState('return_page', base64_decode($return));

      // Load the parameters.
      $params  = $app->getParams();
      $this->setState('params', $params);

      $this->setState('layout', JFactory::getApplication()->input->get('layout'));

      $pid = JFactory::getApplication()->input->get('pid');
      $this->setState('project_value', $pid);
   }

    /**
     * Method to get issue data.
     *
     * @param null $itemId
     * @internal param int $itemid The id of the issue.
     *
     * @return  mixed Issues item data object on success, false on failure.
     */
   public function getItem($itemId = null)
   {

      // Set up access to default parameters
      $this->_params = JComponentHelper::getParams( 'com_issuetracker' );

      // Get default settings
      $wysiwyg = $this->_params->get('wysiwyg_editor', 0);

      // Initialise variables.
      $itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('itissues.id');

      // Get a row instance.
      $table = $this->getTable();

      // Attempt to load the row.
      $return = $table->load($itemId);

      // Check for a table object error.
      if ($return === false && $table->getError()) {
         $this->setError($table->getError());
         return false;
      }

      $properties = $table->getProperties(1);
      $value = JArrayHelper::toObject($properties, 'JObject');

      $user = JFactory::getUser();

      // Extra code check for a locked issue.
      if ($value->checked_out > 0 && $value->checked_out != $user->id ) {
         $app = JFactory::getApplication();
         $checkoutUser  = JFactory::getUser($value->checked_out);
         $app->enqueueMessage(JText::sprintf('COM_ISSUETRACKER_ISSUE_BEING_EDITED_MSG',$checkoutUser->name),'info');
         $return = JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.(int) $value->id);
         $app->redirect($return, false);
      }

      // Convert attrib field to Registry.
      $value->params = new JRegistry;
      $value->params->loadString($value->params);

      // Compute selected asset permissions.
      $asset   = 'com_issuetracker.itissues.'.$value->id;

      // Technically guest could edit an issue, but lets not check that to improve performance a little.
      if (!$user->get('guest')) {
         $userId  = $user->get('id');
         // $asset   = 'com_issuetracker.itissues.'.$value->id;

         $value->params->set('wysiwyg_editor', $wysiwyg);

         // Check general edit permission first.
         if ($user->authorise('core.edit', $asset)) {
            $value->params->set('access-edit', true);
         }
         // Now check if edit.own is available.
         elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
            // Check for a valid user and that they are the owner.
            // Note that the issue created by is a string not an id.
            // if ($userId == $value->created_by) {
            if ($user->username == $value->created_by ) {
               $value->params->set('access-edit', true);
            }
            $person_id = IssueTrackerHelper::get_itpeople_id($user->id);
            if ($person_id == $value->identified_by_person_id ) {
                $value->params->set('access-edit', true);
            }
         // Now add check if issue admin
         elseif ( IssueTrackerHelper::isIssueAdmin($userId) ) {
            $value->params->set('access-edit', true);
            }
         elseif ( IssueTrackerHelper::isIssueStaff($userId) ) {
            $value->params->set('access-edit', true);
            }
         }
      }

      // Compute view access permissions.
      if ($access = $this->getState('filter.access')) {
         // If the access filter has been set, we already know this user can view.
         $value->params->set('access-view', true);
      } else {
         // If no access filter is set, the layout takes some responsibility for display of limited information.
         $user = JFactory::getUser();
         // $groups = $user->getAuthorisedViewLevels();

//         if ($value->catid == 0 || $this->_data->category_access === null) {
//            $value->params->set('access-view', in_array($value->access, $groups));
//         }  else {
//                  $value->params->set('access-view', in_array($value->access, $groups) && in_array($value->category_access, $groups));
//         }
      }

      // Check edit state permission.
      if ($itemId) {
         // Existing item
         $value->params->set('access-change', $user->authorise('core.edit.state', $asset));
         $table->checkout($user->id, $itemId);  // Check it out

         $jversion = new JVersion();
         if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
            if (!empty($value->id)) {
               $value->tags = new JHelperTags;
               $value->tags->getTagIds($value->id, 'com_issuetracker.itissue');
            }
         }
      } else {
         // New item.

          // Override the issue data with any issue data that may be in the session.
         $app = JFactory::getApplication();
         $temp = (array)$app->getUserState('com_issuetracker.itissues.data', array());
         foreach ($temp as $k => $v) {
            $value->$k = $v;
         }

         // If called from the projects list the pid will be set.
         $pid = $this->getState('project_value');
         if ( !empty($pid) )
            $value->related_project_id = $pid;

//         $catId = (int) $this->getState('issue.catid');
//         if ($catId) {
//            $value->params->set('access-change', $user->authorise('core.edit.state', 'com_issuetracker.category.'.$catId));
//         }
//         else {
            $value->params->set('access-change', $user->authorise('core.edit.state', 'com_issuetracker'));
//         }
      }
      return $value;
   }

   /**
    * Get the return URL.
    *
    * @return  string   The return URL.
    * @since   1.6
    */
   public function getReturnPage()
   {
      return base64_encode($this->getState('return_page'));
   }

   /**
    * Get select list for edit form
    *
    */
   public function &getProject_name()
   {
      $db = JFactory::getDBO();

      //build the list of categories
      $query = 'SELECT a.project_name AS text, a.id AS value, a.parent_id as parentid'
      . ' FROM #__it_projects AS a'
      . ' WHERE a.state = 1'
      . ' ORDER BY a.ordering';
      $db->setQuery( $query );
      $data = $db->loadObjectList();

      $catId   = -1;
      $tree = array();
      $text = '';
      $tree = IssueTrackerHelper::ProjectTreeOption($data, $tree, 0, $text, $catId);

      // array_unshift($tree, JHTML::_('select.option', '', '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -', 'value', 'text'));

      return $tree;
   }

   /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param string $type
    * @param string $prefix A prefix for the table class name. Optional.
    * @param array $config Configuration array for model. Optional.
    * @internal param \The $type table type to instantiate
    * @return  JTable   A database object
    * @since   1.6
    */
   public function getTable($type = 'Itissues', $prefix = 'IssueTrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

}