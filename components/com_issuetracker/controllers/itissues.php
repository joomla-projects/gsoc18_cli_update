<?php
/*
 *
 * @Version       $Id: itissues.php 2300 2016-06-01 15:19:28Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.10
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-06-01 16:19:28 +0100 (Wed, 01 Jun 2016) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

JLoader::import('joomla.application.component.controllerform');

/**
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 */
class IssuetrackerControllerItissues extends JControllerForm
{
   /**
    * @since   1.6
    */
   protected $view_item = 'form';
   protected $view_list = 'itissueslist';

   /**
    * Method to add a new record.
    *
    * @return  boolean  True if the issue can be added, false if not.
    * @since   1.6
    */
   public function add()
   {
      if (!parent::add()) {
         // Redirect to the return page.
         $this->setRedirect($this->getReturnPage());
      }
   }

   /**
    * Method override to check if you can add a new record.
    *
    * @param   array
    *
    * @return  boolean
    * @since   1.6
    */
   protected function allowAdd($data = array())
   {
      // Initialise variables.
      // $user    = JFactory::getUser();
      //  $categoryId = JArrayHelper::getValue($data, 'catid', JRequest::getInt('catid'), 'int');
      $allow      = null;
/*
      if ($categoryId) {
         // If the category has been passed in the data or URL check it.
         $allow   = $user->authorise('core.create', 'com_issuetracker.category.'.$categoryId);
      }
*/
      if ($allow === null) {
         // In the absence of better information, revert to the component permissions.
         return parent::allowAdd();
      }
      else {
         return $allow;
      }
   }

   /**
    * Method override to check if you can edit an existing record.
    *
    * @param   array $data An array of input data.
    * @param   string   $key  The name of the key for the primary key.
    *
    * @return  boolean
    * @since   1.6
    *
    * Issues administrator can edit any issue.
    */
   protected function allowEdit($data = array(), $key = 'id')
   {
      // Initialise variables.
      $recordId   = (int) isset($data[$key]) ? $data[$key] : 0;
      $user       = JFactory::getUser();
      $userId     = $user->get('id');
      $asset      = 'com_issuetracker.itissues.'.$recordId;

      // Check general edit permission first.
      if ($user->authorise('core.edit', $asset)) {
         return true;
      }

      // Check if issues admin
      if ( $this->issue_admin( $user->id) ) return true;

      // Fallback on edit.own.
      // First test if the permission is available.
      if ($user->authorise('core.edit.own', $asset)) {
         // Now test the owner is the user.   Note that our created_by is a name not an id.
           $ownerId = '';
//         $ownerId = $data['identified_by_person_id'];
//         $ownerId = (int) isset($data['identified_by_person_id']) ? $data['identified_by_person_id'] : 0;
//       if (empty($ownerId) && $recordId) {
         if ( $recordId) {
            // Need to do a lookup from the model.
            $record     = $this->getModel()->getItem($recordId);

            if (empty($record)) {
               return false;
            }

            $ownerId = $this->getuserid($record->created_by);
            $identby = $record->identified_by_person_id;
            $person_id = $this->getpersonid($userId);

            if ( $ownerId == $userId || $identby == $person_id ) {
               return true;
            }
         }

         // If the creator or identified by user matches current user then do the test.
         if ( $ownerId == $userId ) {
            return true;
         }
      }

      // Check if Joomla admin
      $app = JFactory::getApplication();
      if ( $app->isAdmin() || JDEBUG ) { return true; }

      // Since there is no asset tracking, revert to the component permissions.
      return parent::allowEdit($data, $key);
   }

   /**
    * Method to check if an issue administrator
    * @param null $id
    * @return mixed
    */
   public function issue_admin ($id = null)
   {
      // Check it_people table to see if this user is an issue administrator
      $db = JFactory::getDBO();
      $query = 'SELECT issues_admin FROM #__it_people WHERE user_id = '.$id;
      $db->setQuery($query);
      $isadmin = $db->loadResult();

      return $isadmin;
   }

   /**
    * Method to return the id of a specified user id
    * @param null $name
    * @return int|mixed
    */
   public function getuserid ($name = null)
   {
      $db = JFactory::getDBO();
      $query = "SELECT user_id FROM #__it_people WHERE username = '".$name."'";
      $db->setQuery($query);
      $uid = $db->loadResult();

      if ( empty($uid) ) $uid = 0;

      return $uid;
   }

   /**
    * Method to return the id of a specified user id
    * @param null $uid
    * @return int|mixed
    */
   public function getpersonid ($uid = null)
   {
      $db = JFactory::getDBO();
      $query = "SELECT id FROM #__it_people WHERE user_id = '".$uid."'";
      $db->setQuery($query);
      $pid = $db->loadResult();

      if ( empty($pid) ) $pid = 0;

      return $pid;
   }


   /**
    * Method to cancel an edit.
    *
    * @param   string   $key  The name of the primary key of the URL variable.
    *
    * @return  Boolean  True if access level checks pass, false otherwise.
    * @since   1.6
    */
   public function cancel($key = 'a_id')
   {
      parent::cancel($key);

      // If we are called by the form then the caller will be set to the calling page.
      $app = JFactory::getApplication();
      $input   = $app->input;

      // Check in if required.
      $iid = $input->getInt('issue_id');
      if ( $iid > 0 ) {
         $issues = JTable::getInstance('itissues', 'IssuetrackerTable');
         $issues->checkIn($iid);
      }

      // Flush any old issue data from the session
      $app->setUserState('com_issuetracker.itissues.data', null);

      $return_edit = $this->input->get('return_edit', null, 'base64');
      $caller = $this->input->get('caller', null, 'base64');

      if ( ($return_edit != $caller) && ! empty($caller) ) {
         /*
         if (! class_exists('IssueTrackerHelperLog')) {
            require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'log.php');
         }
         IssueTrackerHelperLog::dblog('Using address Caller: '.base64_decode($caller), JLog::DEBUG);
         */

         $this->setRedirect(base64_decode($caller));
         // $input->set('return', $caller);
      } else {
         // Redirect to the return page.
         $this->setRedirect($this->getReturnPage());
      }
   }

   /**
    * Method to edit an existing record.
    *
    * @param   string   $key  The name of the primary key of the URL variable.
    * @param   string   $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
    *
    * @return  Boolean  True if access level check and checkout passes, false otherwise.
    * @since   1.6
    */
   public function edit($key = null, $urlVar = 'a_id')
   {
      $result = parent::edit($key, $urlVar);

      return $result;
   }

   /**
    * Method to get a model object, loading it if required.
    *
    * @param   string   $name The model name. Optional.
    * @param   string   $prefix  The class prefix. Optional.
    * @param   array $config  Configuration array for model. Optional.
    *
    * @return  object   The model.
    * @since   1.5
    */
   public function &getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
   {
      $model = parent::getModel($name, $prefix, $config);

      return $model;
   }

   /**
    * Gets the URL arguments to append to an item redirect.
    *
    * @param   int      $recordId   The primary key id for the item.
    * @param   string   $urlVar     The name of the URL variable for the id.
    *
    * @return  string   The arguments to append to the redirect URL.
    * @since   1.6
    */
   protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
   {
      // Need to override the parent method completely.
      $tmpl    = JFactory::getApplication()->input->get('tmpl');
      // $layout  = JFactory::getApplication()->input->get('layout', 'edit');
      $append  = '';

      // Setup redirect info.
      if ($tmpl) {
         $append .= '&tmpl='.$tmpl;
      }

      // TODO This is a bandaid, not a long term solution.
//    if ($layout) {
//       $append .= '&layout='.$layout;
//    }
      $append .= '&layout=edit';

      if ($recordId) {
         $append .= '&'.$urlVar.'='.$recordId;
      }

      // $itemId  = JRequest::getInt('Itemid');
      $itemId  = JFactory::getApplication()->input->get('Itemid');
      $return  = $this->getReturnPage();

      if ($itemId) {
         $append .= '&Itemid='.$itemId;
      }

      if ($return) {
         $append .= '&return='.base64_encode($return);
      }

      return $append;
   }

   /**
    * Get the return URL.
    *
    * If a "return" variable has been passed in the request
    *
    * @return  string   The return URL.
    * @since   1.6
    */
   protected function getReturnPage()
   {
/*
      $return = JFactory::getApplication()->input->get('return', null, 'default', 'base64');

      // Work around for Joomla 3.2 which objects if there is an ampersand in some return addresses.
      // Not sure that this is just Joomla 3.2 either. Have raised a report on Joomla to see if it is a bug.
      // Seen on our test 3.2.3 instances.
      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.2', 'ge' ) ) {
         if (empty($return) || !JUri::isInternal(str_replace('&','\&', base64_decode($return)))) {
            return JURI::base();
         }
      } else {
         if (empty($return) || !JUri::isInternal(base64_decode($return))) {
            return JURI::base();
         }
      }
      return base64_decode($return);
*/
      $input = JFactory::getApplication()->input;
      $return = $this->input->get('return', null, 'base64');

      if (empty($return) || !JUri::isInternal(base64_decode($return))) {
         return JUri::base();
      } else {
         return base64_decode($return);
      }
   }


    /**
     * Function that allows child controller access to model data after the data has been saved.
     *
     * @param  JModelLegacy $model The data model object.
     * @param   array $validData  The validated data.
     *
     * @return  void
     * @since   1.6
     */
   protected function postSaveHook(JModelLegacy $model, $validData = array())
   {
      $task = $this->getTask();

      if ($task == 'save') {
         // $this->setRedirect(JRoute::_('index.php?option=com_issuetracker&view=category&id='.$validData['catid'], false));
         $this->setRedirect(JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.$validData['id'], false));
      }
   }

   /**
    * Method to save a record.
    *
    * @param   string   $key  The name of the primary key of the URL variable.
    * @param   string   $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
    *
    * @return  Boolean  True if successful, false otherwise.
    * @since   1.6
    */
   public function save($key = null, $urlVar = 'a_id')
   {
      // Load the backend helper for filtering.
      require_once JPATH_ADMINISTRATOR.'/components/com_issuetracker/helpers/issuetracker.php';

      $model = $this->getModel('itissues');

      $result = $model->store();
      if ($result) {
          $msg = JText::_( 'COM_ISSUETRACKER_ISSUE_SAVED_MSG' );
      } else {
          $msg = JText::_( 'COM_ISSUETRACKER_ISSUE_SAVING_ERROR_MSG' );
      }

      $this->setRedirect($this->getReturnPage(), $msg);
      return $result;
   }

   /**
    * Method to delete an existing record.
    *
    * @param   string   $key  The name of the primary key of the URL variable.
    * @param   string   $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
    *
    * @return  Boolean  True if access level check and checkout passes, false otherwise.
    * @since   1.6
    */
   public function delete($key = null, $urlVar = 'a_id')
   {
      $model = $this->getModel('itissues');

      $id = JFactory::getApplication()->input->get('a_id', null);
      $result = $model->delete($id);
      if ($result) {
         $msg = JText::_( 'COM_ISSUETRACKER_ISSUE_DELETED_MSG' );
         // Since we have deleted the issue we need to modify the return page because we cannot return to the issue display page!
         $ret = JURI::base();        // Set to home page.
         $this->setRedirect($ret, $msg);
         // $this->setRedirect($this->getReturnPage(), $msg);
      } else {
         $msg = JText::_( 'COM_ISSUETRACKER_ISSUE_DELETING_ERROR_MSG' );
         $this->setRedirect($this->getReturnPage(), $msg, 'error');
      }

      return $result;
   }

   /**
    * Method to return the custom fields for a project to the Ajax call.
    *
    *
    */
   function customFields()
   {
      $app        = JFactory::getApplication();
      $language   = JFactory::getLanguage();
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR);
      $itemID     = $app->input->get('id', NULL);

      $params        = JComponentHelper::getParams('com_issuetracker');
      $def_project   = $params->get('def_project', '10');

      JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
      $pid     = $app->input->get('pid');
      if ( empty($pid) || $pid == 0 ) $pid = $def_project;

      $project = JTable::getInstance('itprojects', 'IssuetrackerTable');
      $project->load($pid);

      require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'customfield.php');
      $cfModel = new IssuetrackerModelCustomField;

      $pstate = 1;   // Only display published fields
      $astate = 0;   // Since this is the form we are not controlling access. We want them to enter the information or they are checked by the form display itself.
      $customFields = $cfModel->getCustomFieldByGroup($project->customfieldsgroup, $pstate, $astate);

      // Add checks for user access groups on custom fields.
      $user       = JFactory::getUser();
      $userGroups = JAccess::getGroupsByUser($user->get('id'));

      $jversion = new JVersion();
      $displayopt = 0;
      $output = '<div id="customFields">';
      $counter = 0;
      if (count($customFields)) {
         foreach ($customFields as $extraField) {
            if ( in_array($extraField->access, $userGroups) ) {
               $output .= '<div class="formelm">';
               if ($extraField->type == 'header') {
                  $output .= '<legend>'.$extraField->name.'</legend>';
               } else {
                  // Determine whether we are a required field.
                  $required = false;
                  $defs = json_decode($extraField->value);
                  foreach ($defs as $val) {
                     if (isset($val->required) && $val->required == 1) {
                        $required = true;
                     }
                     break;
                  }

                  if (!empty($extraField->tooltip)) {
                     if ($required) {
                        $attributes = 'class="hasTooltip required"';
                     } else {
                        $attributes = 'class="hasTooltip"';
                     }
                     $output .= '<label data-original-title="<strong>'.$extraField->name.'</strong><br />'.$extraField->tooltip.'" for="CustomField_'.$extraField->id.'" id="ITCustomField_'.$extraField->id.'-id" '.$attributes.' title="'.$extraField->tooltip.'">'.$extraField->name;
                  } else {
                     if ($required) {
                        $attributes = 'class="required"';
                     } else {
                        $attributes = '';
                     }
                     $output .= '<label for="CustomField_'.$extraField->id.'" id="ITCustomField_'.$extraField->id.'-id" '.$attributes.' >'.$extraField->name;
                  }

                  if ( $required )
                     $output .= '<span class="star">&#160;*</span>';
                  $output .= '</label>';

                  if($extraField->type == 'radio') {
                     if ( version_compare( $jversion->getShortVersion(), '3.2', 'ge' ) ) {
                        $output .= '<fieldset class="btn-group" style="vertical-align:top; text-align:left">';
                     } else {
                        $output .= '<fieldset class="radio" style="vertical-align:top; text-align:left">';
                     }
                     $output .= str_replace('</label>','</label><br />',$cfModel->renderCustomField($extraField, $itemID, $displayopt));
                     $output .= '</fieldset>';
                  } elseif($extraField->type == 'multipleCheckbox') {
                     $output .= '<fieldset class="checkbox" style="vertical-align:top; text-align:left">';
                     $output .= str_replace('</option>','</option><br />',$cfModel->renderCustomField($extraField, $itemID, $displayopt));
                     $output .= '</fieldset>';
                  } else {
                     $output .= $cfModel->renderCustomField($extraField, $itemID, $displayopt);
                     if ( version_compare( $jversion->getShortVersion(), '3.2', 'ge' ) ) {
                        $output .= '<div class="clearfix"></div>';
                     } else {
                        $output .= '<div class="clr"></div>';
                     }
                  }
               }
               $output .= '</div>';
               $counter++;
            }
         }
      }
      $output .= '</div>';

      if ($counter == 0)
         $output = NULL;
         // $output = JText::_('COM_ISSUETRACKER_THIS_PROJECT_DOESNT_HAVE_ASSIGNED_CUSTOM_FIELDS');
      echo $output;
      $app->close();
   }

   /**
    * Method to return the option types for a given project to the Ajax call.
    *
    *
    */
   function projectTypes()
   {
      $app           = JFactory::getApplication();
      $language      = JFactory::getLanguage();
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR);
      $pid           = $app->input->get('pid');

      $params        = JComponentHelper::getParams('com_issuetracker');
      $def_project   = $params->get('def_project', '10');

      // Handle empty pid - get settings from default project.
      if ( empty($pid) || $pid == 0 )
         $pid = $def_project;

      $db = JFactory::getDBO();
      $query = "SELECT itypes FROM #__it_projects WHERE id = ". $db->Quote($pid);
      $db->setQuery($query);
      $types = $db->loadResult();
      $types = json_decode($types);  // Get as an array.

      // Get strings for type ids in display.
      $output = null;
      $query = "SELECT 0 AS id, ".$db->Quote('- '.JText::_('COM_ISSUETRACKER_SELECT_TYPE').' -')." AS type_name UNION ";

      if ( count($types) == 0 || $types[0] == 0 ) {
         $query .= "SELECT id, type_name FROM #__it_types WHERE state = 1 ";
      } else {
         $query .= "SELECT id, type_name FROM #__it_types WHERE state = 1 AND id IN (". implode(',', $types).")";
      }

      $db->setQuery($query);
      // $output2 = $db->loadRowList();
      $output = $db->loadAssocList();

      echo json_encode($output);
      $app->close();
   }

   /**
    * Method to set the deault notifications fields for a user from an ajax call.
    *
    *
    */
   function setnotify()
   {
      $app           = JFactory::getApplication();
      $language      = JFactory::getLanguage();
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR);
      $iid           = $app->input->get('iid');

      // Use default notify settings.
      $params        = JComponentHelper::getParams('com_issuetracker');
      $def_notify    = $params->get('def_notify', '0');

      // Handle empty pid - get settings from default project.
      if ( $iid > 0 ) {
         $db = JFactory::getDBO();

         switch ($def_notify) {
         case 0:
            break;
         case 1:
            $query = "UPDATE #__it_people SET email_notifications = 1";
            $query .= " WHERE  id = ". $db->Quote($iid);
            break;
         case 2:
            $query = "UPDATE #__it_people SET sms_notify = 1";
            $query .= " WHERE  id = ". $db->Quote($iid);
            break;
         case 3:
            $query = "UPDATE #__it_people SET email_notifications = 1, sms_notify = 1";
            $query .= " WHERE  id = ". $db->Quote($iid);
            break;
         }

         if ( $def_notify > 0 ) {
            $db->setQuery($query);
            $res = $db->execute();
         }
      }
      echo json_encode('OK');
      $app->close();
   }

   /**
    * Method to get the deault notifications fields for a user from an ajax call.
    *
    *
    */
   function getnotify()
   {
      $app           = JFactory::getApplication();
      $language      = JFactory::getLanguage();
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR);
      $iid           = $app->input->get('iid');

      $db = JFactory::getDBO();

      $query = "SELECT email_notifications, sms_notify FROM #__it_people";
      $query .= " WHERE  id = ". $db->Quote($iid);
      $db->setQuery($query);
      $notifications = $db->loadRow();
      $ret = 0;

      if ($notifications[0] == 1)
         $ret++;

      if ( $notifications[1] == 1 )
         $ret++;

      echo json_encode($ret);
      $app->close();
   }
}