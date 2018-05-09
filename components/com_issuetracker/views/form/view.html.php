<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
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

JLoader::import('joomla.application.component.view');

if (! class_exists('IssueTrackerHelperSite')) {
    require_once( JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php');
}

/**
 * HTML Article View class for the Issue Tracker component
 *
 * @package    Joomla.Site
 * @subpackage com_issuetracker
 * @since      1.5
 */
class IssueTrackerViewForm extends JViewLegacy
{
   protected $form;
   protected $item;
   protected $return_page;
   protected $return_edit;
   protected $state;
   protected $viewonly;
   protected $print;
   protected $pid;
   protected $pageclass_sfx;
   protected $parameters;
   protected $user;

   /**
    * @param null $tpl
    * @return bool
    */
   public function display($tpl = null)
   {
      // Initialise variables.
      $app     = JFactory::getApplication();
      $user    = JFactory::getUser();

      $buttons1 = 'articlesanywhere,modulesanywhere,tabs,tabber,image,pagebreak,readmore,article';
      $buttons2 = 'articlesanywhere,modulesanywhere,tabs,tabber,pagebreak,readmore,image,article,toggle editor';
      $buttons3 = 'articlesanywhere,modulesanywhere,tabs,tabber,pagebreak,readmore,article';

      // Get model data.
      $this->state      = $this->get('State');
      $this->item       = $this->get('Item');
      $this->form       = $this->get('Form');
      $this->print      = JFactory::getApplication()->input->getBool('print');
      $this->pid        = $this->state->get('project_value');

      $viewonly         = 0;

      $this->return_page   = $this->get('ReturnPage');
      $this->return_edit   = JURI::current();
      $isadmin             = IssueTrackerHelperSite::isIssueAdmin($user->id);
      $isstaff             = IssueTrackerHelperSite::isIssueStaff($user->id);
      $this->state->params->set('issues_admin',$isadmin);
      $this->state->params->set('issues_staff',$isstaff);

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         if ( empty($this->item->tags) ) $this->item->tags = new JHelperTags;
         if (!empty($this->item->id)) {
            $this->item->tags->getItemTags('com_issuetracker.itissue', $this->item->id);
         }
      }

      if (empty($this->item->id)) {
         $authorised = $user->authorise('core.create', 'com_issuetracker') || (count($user->getAuthorisedCategories('com_issuetracker', 'core.create')));
         $userauth = NULL;
      } else {
         $userauth   = $this->item->params->get('access-edit');
         $authorised = $userauth || $isadmin || $isstaff;
      }

      if ( $this->item->id != 0 ) {
         $this->progress   = $this->get_progress_info($this->item);
         $this->attachment = $this->check_attachments($this->item);
      }

      $wysiwyg       = $this->item->params->get('wysiwyg_editor');
      $privacy       = $this->state->params->get('allow_private_issues');
      $def_privacy   = $this->state->params->get('def_privacy', 1);

      // Need to check that our users actually has a specified editor.
      // Staff and admin get an editor unless their specific profile says none.
      if ( $isadmin || $isstaff ) {
         // echo "<pre>";var_dump($user);echo "</pre>";
         $arrrr = json_decode($user->params);
         if ( isset($arrrr->editor) ) {
            if ( $arrrr->editor == "none" ) {
               $wysiwyg = 0;
            } else {
               if ( empty($arrrr->editor) || $arrrr->editor == null ) {
                  // User setting to use site default editor.
                  if ( $app->get('editor') == "none" ) {
                     $wysiwyg = 0;
                  } else {
                     $wysiwyg = 1;
                  }
               } else {
                     $wysiwyg = 1;
              }
           }
         } else {
           // Stange key doesn't seem to exist assume editor is set.
           $wysiwyg = 1;
         }
      } elseif ( ! $user->guest ) {
         $arrrr = json_decode($user->params);
         if ( isset($arrrr->editor) ) {
            if ( $arrrr->editor == "none" ) {
               $wysiwyg = 0;
            } else {
               if ( empty($arrrr->editor) || $arrrr->editor == null ) {
                  // User setting to use site default editor.
                  // If component parameter was specified but no editor specified reset parameter
                  if ( $wysiwyg && $app->get('editor') == "none" ) {
                     $wysiwyg = 0;
                  }
               }
            }
         } else {
           // Strange key doesn't seem to exist assume editor is set, leave it to the component setting.
         }
      } else {
         // Guests do not get an editor!
         $wysiwyg = 0;
      }

      $this->state->params->set('wysiwyg', $wysiwyg);   //Save it for the form template

      // Hide published field by default.
      $this->form->setFieldAttribute('state', 'type',     'hidden');

      if ($privacy == 0) {
         $this->form->setFieldAttribute('public', 'type',     'hidden');
         $this->form->setFieldAttribute('public', 'disabled', 'true');
         $this->form->setFieldAttribute('public', 'required', 'false');
      } else {
         // If an existing issue restrict changing of the privacy.
         if ( empty($this->item->id)) {
            // $this->form->setFieldAttribute('public', 'type',     'radio');
            $this->form->setFieldAttribute('public', 'disabled', 'false');
            $this->form->setFieldAttribute('public', 'required', 'true');
            $this->form->setFieldAttribute('public', 'default',  $def_privacy);
         } else {
            // The user can make it private if it is public
            // but if it is private, prevent it being made public.
            if ($this->item->public == 0 ) {
               // $this->form->setFieldAttribute('public', 'type',     'radio');
               $this->form->setFieldAttribute('public', 'disabled', 'true');
            }
         }
      }

      // Change the progress defaults if required.
      // if ( empty($this->item->id) ) {
         $def_pstate = $this->state->params->get('def_pstate',0);
         $this->form->setFieldAttribute('pstate', 'default',  $def_pstate);
         $def_progresspublic = $this->state->params->get('def_progresspublic',1);
         $this->form->setFieldAttribute('progresspublic', 'default',  $def_progresspublic);
         $def_paccess = $this->state->params->get('def_paccess',2);
         $this->form->setFieldAttribute('paccess', 'default',  $def_paccess);
      // }

      $this->state->params->set('new_record','0');
      $this->state->params->set('admin_edit','0');

      // Field defaults are set up for a new issue creation so we have to change this if we are editing.
      if ( $authorised === true ) {
         if (! empty($this->item->id)) {
            // Change display for editable fields
            if ( $wysiwyg ) $this->form->setFieldAttribute('additional_info',     'type',     'editor');
            $this->form->setFieldAttribute('issue_summary',       'readonly', 'true');
            $this->form->setFieldAttribute('issue_summary',       'required', 'false');
            $this->form->setFieldAttribute('issue_description',   'readonly', 'true');
            $this->form->setFieldAttribute('issue_description',   'required', 'false');
            $this->form->setFieldAttribute('identified_date',     'readonly', 'true');
            $this->form->setFieldAttribute('identified_date',     'disabled', 'true');
            $this->form->setFieldAttribute('status',              'type',     'text');
            $this->form->setFieldAttribute('status',              'readonly', 'true');
            $this->form->setFieldAttribute('priority',            'type',     'text');
            $this->form->setFieldAttribute('priority',            'readonly', 'true');

            if ( $userauth ) {
               // Specific user only changeable fields
               $this->state->params->set('admin_edit','0');
               // additional details
               $this->form->setFieldAttribute('additional_info',           'required', 'true');
               if ( $wysiwyg == 1 ) {
                  $this->form->setFieldAttribute('additional_info',        'type',     'editor');
                  $this->form->setFieldAttribute('additional_info',        'hide',     $buttons3);
               } else {
                  $this->form->setFieldAttribute('additional_info',        'type',     'textarea');
               }
               $this->form->setFieldAttribute('additional_info',           'filter',   'safehtml');
               $this->form->setFieldAttribute('identified_by_person_id',   'readonly', 'true');
               $this->form->setFieldAttribute('identified_by_person_id',   'required', 'false');
               $this->form->setFieldAttribute('identified_by_person_id',   'disabled', 'true');
               $this->form->setFieldAttribute('identified_by_person_id',   'type',     'personname');
               $this->form->setFieldAttribute('status',                    'type',     'statusname');
               $this->form->setFieldAttribute('status',                    'readonly', 'true');
               $this->form->setFieldAttribute('priority',                  'type',     'priorityname');
               $this->form->setFieldAttribute('priority',                  'readonly', 'true');
               $this->form->setFieldAttribute('assigned_to_person_id',     'readonly', 'true');
               $this->form->setFieldAttribute('assigned_to_person_id',     'disabled', 'true');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'staffname');
               $this->form->setFieldAttribute('target_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('target_resolution_date',    'readonly', 'true');
               $this->form->setFieldAttribute('target_resolution_date',    'disabled', 'true');
               $this->form->setFieldAttribute('actual_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'readonly', 'true');
               $this->form->setFieldAttribute('actual_resolution_date',    'disabled', 'true');
               $this->form->setFieldAttribute('progress',                  'readonly', 'true');
            }

            if ( $isadmin || $isstaff ) {
               // Specific administrator and staff only changable fields
               $this->state->params->set('show_details_section','1');
               $this->state->params->set('admin_edit','1');
               $this->state->params->set('show_target_date_field','1');
               if ( $wysiwyg) $this->form->setFieldAttribute('issue_summary',             'type',     'editor');
               $this->form->setFieldAttribute('issue_summary',             'filter',   'safehtml');
               $this->form->setFieldAttribute('issue_summary',             'hide',     $buttons1);
               $this->form->setFieldAttribute('additional_info',           'type',     'hidden');
               $this->form->setFieldAttribute('additional_info',           'required', 'false');
               $this->form->setFieldAttribute('notify',                    'type',     'hidden');
               if ( $wysiwyg ) $this->form->setFieldAttribute('issue_description',         'type',     'editor');
               $this->form->setFieldAttribute('issue_description',         'filter',   'safehtml');
               $this->form->setFieldAttribute('issue_description',         'hide',     $buttons2);
               $this->form->setFieldAttribute('identified_by_person_id',   'type',     'issuetrackerpersonfe');
               $this->form->setFieldAttribute('identified_date',           'type',     'calendar');
               $this->form->setFieldAttribute('identified_date',           'readonly', 'false');
               $this->form->setFieldAttribute('identified_date',           'disabled', 'false');
               // $this->state->params->set('show_issue_state','1');
               $this->form->setFieldAttribute('state', 'type',     'list');

               $this->form->setFieldAttribute('status',                    'type',     'issuetrackerstatus');
               $this->form->setFieldAttribute('priority',                  'type',     'issuetrackerpriority');
               if ( $wysiwyg ) $this->form->setFieldAttribute('resolution_summary',        'type',     'editor');
               $this->form->setFieldAttribute('resolution_summary',        'filter',   'safehtml');
               $this->form->setFieldAttribute('resolution_summary',        'hide',     $buttons3);
               $this->state->params->set('show_target_date_field','1');
               $this->form->setFieldAttribute('target_resolution_date',   'type',     'calendar');
               $this->state->params->set('show_actual_res_date','1');
               $this->form->setFieldAttribute('actual_resolution_date',    'type',     'calendar');
               $this->form->setFieldAttribute('target_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('target_resolution_date',    'readonly', 'false');
               $this->form->setFieldAttribute('target_resolution_date',    'disabled', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'required', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'readonly', 'false');
               $this->form->setFieldAttribute('actual_resolution_date',    'disabled', 'false');
               $this->state->params->set('show_staff_details','1');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'issuetrackerstaff');

               // Since we are an issue admin we can update the progress field.
               $this->state->params->set('show_progress_field','1');
               if ( $wysiwyg ) $this->form->setFieldAttribute('progress', 'type',    'editor');
               $this->form->setFieldAttribute('progress', 'filter',  'safehtml');
               $this->form->setFieldAttribute('progress', 'hide',    $buttons3);

               // Required to prevent saving error
               $this->form->setFieldAttribute('product_version', 'required', 'false');
               $this->form->setFieldAttribute('pdetails', 'required', 'false');

            }
         } else {
            $this->form->setFieldAttribute('additional_info', 'required', 'false');
            // New record creation
            $date = JFACTORY::getdate();
            $cdate = $date->toSQL();
            $this->form->setFieldAttribute('identified_date', 'default',  $cdate);

            $this->state->params->set('new_record','1');
            if ( $user->guest ) {
               // New issue and a guest so no point displaying identified by field.
               $this->form->setFieldAttribute('identified_by_person_id',           'type',     'hidden');
               $this->form->setFieldAttribute('identified_by_person_id',           'required', 'false');
            } else {
               // A logged in user  Give them a proper editor if configured.
               if ( $wysiwyg == 1 ) {
                  $this->form->setFieldAttribute('issue_summary',       'type', 'editor');
                  $this->form->setFieldAttribute('issue_description',   'type', 'editor');
               } else {
                  $this->form->setFieldAttribute('issue_summary',       'type', 'textarea');
                  $this->form->setFieldAttribute('issue_description',   'type', 'textarea');
               }
               $this->form->setFieldAttribute('issue_summary',       'filter',   'safehtml');
               $this->form->setFieldAttribute('issue_summary',       'hide',     $buttons1);
               $this->form->setFieldAttribute('additional_info',     'type',     'hidden');
               $this->form->setFieldAttribute('issue_description',   'hide',     $buttons3);
               $this->form->setFieldAttribute('issue_description',   'readonly', 'false');
               $this->form->setFieldAttribute('issue_description',   'disabled', 'false');
               $this->form->setFieldAttribute('issue_description',   'required', 'true');
               $this->form->setFieldAttribute('issue_description',   'filter',   'safehtml');
               // Allow them to set a priority as well.  We do not have to stick to it!
               $this->form->setFieldAttribute('priority',            'type',     'issuetrackerpriority');
            }

            if ( $isadmin || $isstaff ) {
               // Allow admin to open and closed with all progress and resolution fields available
               $this->state->params->set('show_details_section','1');
               $this->state->params->set('admin_edit','1');
               // $this->state->params->set('show_product_req','0');
               $this->form->setFieldAttribute('notify',                    'type',     'hidden');
               $this->state->params->set('show_resolution_field','1');
               if ( $wysiwyg ) $this->form->setFieldAttribute('resolution_summary',        'type',     'editor');
               $this->form->setFieldAttribute('resolution_summary',        'filter',   'safehtml');
               $this->form->setFieldAttribute('resolution_summary',        'hide',     $buttons3);
               $this->state->params->set('show_progress_field','1');
               if ( $wysiwyg ) $this->form->setFieldAttribute('progress',                  'type',     'editor');
               $this->form->setFieldAttribute('progress',                  'filter',   'safehtml');
               $this->form->setFieldAttribute('progress',                  'hide',     $buttons3);
               $this->state->params->set('show_target_date_field','1');
               $this->form->setFieldAttribute('target_resolution_date',    'type',     'calendar');
               $this->state->params->set('show_actual_res_date','1');
               $this->form->setFieldAttribute('actual_resolution_date',    'type',     'calendar');
               $this->form->setFieldAttribute('identified_by_person_id',   'type',     'issuetrackerpersonfe');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'issuetrackerperson');
               $this->form->setFieldAttribute('priority',                  'type',     'issuetrackerpriority');
               $this->state->params->set('show_issue_status','1');
               $this->form->setFieldAttribute('state', 'type',     'list');

               $this->form->setFieldAttribute('status',                    'type',     'issuetrackerstatus');
               $this->state->params->set('show_identified_by','1');
               $this->state->params->set('show_staff_details','1');
               $this->form->setFieldAttribute('assigned_to_person_id',     'type',     'issuetrackerstaff');
               $this->form->setFieldAttribute('additional_info',           'type',     'hidden');
               $this->form->setFieldAttribute('additional_info',           'required', 'false');
               $this->form->setFieldAttribute('product_version',           'required', 'false');
               $this->form->setFieldAttribute('pdetails',                  'required', 'false');
            }
         }
      } else {
         // View only now instead of redirect back.   Not authorised fall through.
         // Should not ever get here but just in case!
         $previousurl = $_SERVER['HTTP_REFERER'];
         $msg = JText::_('COM_ISSUETRACKER_LOGON_OR_REG_MSG');
         $app->enqueueMessage($msg);
         $app->redirect($previousurl);
      }

      if (empty($this->item)) {
        } else {
         $this->form->bind($this->item);
      }

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseWarning(500, implode("\n", $errors));
         return false;
      }

      // Create a shortcut to the parameters.
      $parameters = &$this->state->params;

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($parameters->get('pageclass_sfx'));

      $this->parameters = $parameters;
      $this->user = $user;

      // Set the default project and type in the display - May need to move this.
      // If a guest set general defaults otherwise set user defaults.
      if ( $user->guest ) {
         $def_proj = $parameters->get('def_project', 10);
      } else {
         // Get users default project
         $def_proj = IssueTrackerHelperSite::getUserdefproj($user->id);
      }

      // Get project from menu item if only one possible.
      $menu = $app->getMenu()->getActive();
      if ($menu) {
         $projects =  $this->parameters->get('projects');
         $cnt = count($projects);
         if ( $cnt == 1 && $projects[0] != 0 )
            $def_proj = $projects[0];
      }

      $def_type = $parameters->get('def_type', 2);
      if ( empty( $this->item->id ) ) {
         $this->form->setFieldAttribute('related_project_id', 'default', $def_proj);
         $this->form->setFieldAttribute('issue_type', 'default', $def_type);
      }

      JLoader::import('customfield',JPATH_COMPONENT_ADMINISTRATOR.'/'.'models');
      $cfmodel    = JModelLegacy::getInstance('customfield', 'IssuetrackerModel');

      $dmode   = 0; // Controls display mode
      $pstate  = 1; // Used to select published state of custom fields.
      $astate  = 1; // Used to control selection of custom fields access state.
      // For the form we do not need to control the values displayed since the user is supplying them.
      // If it is an edit, then it is either the user themselves or an approved editor.
      // Extract custom fields for views
      if ($this->item->related_project_id != 0) {
         $this->custom = $cfmodel->check_customfields($this->item->related_project_id, $this->item->id, $pstate, $astate, $dmode);
      }

      // Extract custom fields for views
      if (empty($this->item->id) && empty($this->item->related_project_id) ) {
         $this->custom = $cfmodel->check_customfields($def_proj, NULL, $pstate, $astate, $dmode);
      }

      if ( $user->get('isRoot') || $isadmin || $isstaff ) {
        $this->form->setFieldAttribute('language', 'readonly', 'false');
      } else {
        $this->form->setFieldAttribute('language', 'readonly', 'true');
      }

      $this->_prepareDocument($this->item);
      parent::display($tpl);
      return true;
   }

   /**
    * Get the custom fields group name for any project.
    * @param $pid
    * @return mixed|null|string
    */
   function getCustomGroupName($pid)
   {
      $gname = null;
      if ( ! empty($pid) ) {
         $db = JFactory::getDBO();
         $query  = 'SELECT name FROM `#__it_custom_field_group` AS cfg ';
         $query .= ' LEFT JOIN `#__it_projects` AS p ';
         $query .= ' ON p.customfieldsgroup = cfg.id ';
         $query .= ' WHERE p.id = '.$pid;
         $db->setQuery( $query );
         $gname = $db->loadResult();
      }

      if (empty($gname) || $gname == '')
         $gname = JText::_( 'COM_ISSUETRACKER_CUSTOMFIELDS' );
      return $gname;
   }


   /**
    * Check if the field is a required value.
    * @param $value
    * @return bool
    */
   function checkrequired($value)
   {
      $required = false;
      // Expand out the field and check the elements.
      $defs = json_decode($value);
      foreach ($defs as $val) {
         if ( isset($val->required) && $val->required == 1 ) {
            $required = true;
         }
      }
      return $required;
  }

   /**
    * Get progress information from separate table.
    * This code should be in the model. Move when convenient.
    * @param $data
    * @return mixed
    */
   function get_progress_info($data)
   {
      $issue_id = $data->id;

      $db      = JFactory::getDbo();
      $user    = JFactory::getUser();
      $isadmin = IssueTrackerHelperSite::isIssueAdmin($user->id);
      $isstaff = IssueTrackerHelperSite::isIssueStaff($user->id);

      $groups  = implode(',', $user->getAuthorisedViewLevels());
      $where   = ' AND access IN ('.$groups.')';

      // If our current user raised the issue no need to check the groups.
//      if ( $data->identified_by_person_id == IssueTrackerHelperSite::getitPersonid($user->id) ) {
//         $where = '';
//      }

      // Only display published and unpublished items.
      $where .= ' AND state IN (0,1) ';

      $query = "SELECT count(*) FROM `#__it_progress` WHERE issue_id = '".$issue_id."'";
      $query .= $where;
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         return false;
      } else {
          // $query = "SELECT * FROM `#__it_progress` WHERE issue_id = '".$issue_id."'";
          $query  = "SELECT * ";
          // $query .= " CASE state ";
          // $query .= " WHEN 0 THEN '".JText::_('JUNPUBLISHED')."'";
          // $query .= " WHEN 1 THEN '".JText::_('JPUBLISHED')."'";
          // $query .= " END AS statevalue ";
          $query .= " FROM `#__it_progress`";
          $query .= " WHERE issue_id = '".$issue_id."'";

         $query .= $where;
         $query .= " ORDER BY lineno ASC";
         $db->setQuery($query);
         $progress = $db->loadObjectList();
         return $progress;
      }
   }

   /**
    * Prepares the document
    * @param $data
    */
   protected function _prepareDocument($data)
   {
      $app        = JFactory::getApplication();
      $menus      = $app->getMenu();
      $title      = null;
      $document   = JFactory::getDocument();

      // Because the application sets a default page title,
      // we need to get it from the menu item itself
      $menu = $menus->getActive();
      if ($menu) {
         $this->parameters->def('page_heading', $this->parameters->get('page_title', $menu->title));
      } else {
         $this->parameters->def('page_heading', JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE'));
      }

      $title = $this->parameters->def('page_title', JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE'));
      if ($app->get('sitename_pagetitles', 0) == 1) {
         $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
      } elseif ($app->get('sitename_pagetitles', 0) == 2) {
         $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
      }

      $document->setTitle($title);

      // Special case to trap situation where we are called from the projects list links.
      if ( empty($menu) || strpos($menu->link, 'itprojectslist') ) {
         if (empty($data->id)) {
            $ntitle = JText::_('COM_ISSUETRACKER_FORM_CREATE_ISSUE_TITLE');
         } else {
            $ntitle = JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE_TITLE');
         }
         $document->setTitle($ntitle);
         $this->parameters->set('page_heading', $ntitle);
      }

      if (!empty($data->id)) {
         $pathway = $app->getPathWay();
         $pathway->addItem('Issue '.$data->alias, '');
      }

      $js = "var IT_BasePath = '".JURI::base(true)."/'";
      $document->addScriptDeclaration($js);
   }

   /**
    * Check if any attachments and get details.
    * TODO This code should be in the model. Move when convenient.
    * @param $data
    * @return bool|mixed
    */
   function check_attachments($data)
   {
      $issue_id = $data->alias;

      $db = JFactory::getDbo();
      $query = "SELECT count(*) FROM `#__it_attachment` WHERE issue_id = '".$issue_id."'";
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         return false;
      } else {
         $query = "SELECT * FROM `#__it_attachment` WHERE issue_id = '".$issue_id."'";
         $db->setQuery($query);
         $attachment = $db->loadObjectList();
         return $attachment;
      }
   }
}