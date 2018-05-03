<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if (! class_exists('IssueTrackerHelperSite')) {
   require_once( JPATH_ROOT.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php');
}

JLoader::import( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package Joomla.Components
 * @subpackage Issue Tracker
 */
class IssueTrackerViewItissues extends JViewLegacy
{
   protected $form;
   protected $print;
   protected $state;
   protected $data;
   protected $attachment;
   protected $params;
   protected $pageclass_sfx;
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null)
   {
      $app     = JFactory::getApplication();
      // Get model data.
      $state = $this->get('State');
      $data    = $this->get('Item');
      $this->data = $data;
      $item = $data;

      $this->form    = $this->get('Form');
      $this->print   = JFactory::getApplication()->input->getBool('print');

      // Create a shortcut to the parameters.
      $params  = $app->getParams();
      $this->params = $params;

      if ( ! is_null($data) && $data->id != 0 ) {
         if ( $data->id != 0 ) {
            $this->attachment = $this->check_attachments($data);
            // Permission for downloading attachments.
            if ( $this->attachment) {
               $this->can_download = false;

               $user    = JFactory::getUser();
               $isadmin = IssueTrackerHelperSite::isIssueAdmin($user->id);
               $isstaff = IssueTrackerHelperSite::isIssueStaff($user->id);
               if ( $isadmin || $isstaff )
                  $this->can_download = true;
               if ( ! $user->guest  && $data->identified_by_person_id == IssueTrackerHelperSite::getitPersonid($user->id) )
                  $this->can_download = true;
               // If a public record then check component parameters
               if ( $data->public == 1 ) {
                  if ( ! $user->guest && $params->get('reg_download','0') == 1) {
                     // print("Reg user can download<p>");
                     $this->can_download = true;
                  }
               }
            }
            $this->progress   = $this->get_progress_info($data);
         }

         // Create a shortcut for $item.
         $item = $data;
         $jversion = new JVersion();
         if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
            $item->tagLayout  = new JLayoutFile('joomla.content.tags');
         }

         //Escape strings for HTML output
         $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      } else {
         // No issue was found.
         if ( isset($_SERVER['HTTP_REFERER']) ) {
            $previousurl = $_SERVER['HTTP_REFERER'];
         } else {
            $previousurl = JURI::base();
         }
         $msg = JText::_('COM_ISSUETRACKER_ISSUE_NOT_FOUND');
         $app->enqueueMessage($msg);
         $app->redirect($previousurl);
      }

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $item->tags = new JHelperTags;
         $item->tags->getItemTags('com_issuetracker.itissue' , $data->id);
      }

      // Extract custom fields for views
      JLoader::import('customfield',JPATH_COMPONENT_ADMINISTRATOR.'/'.'models');
      $cfmodel    = JModelLegacy::getInstance('customfield', 'IssuetrackerModel');
      $dmode  = 1;
      $pstate = 1;
      $astate = 1;
      $this->custom = $cfmodel->check_customfields($data->related_project_id, $data->id, $pstate, $astate, $dmode);

      $this->_prepareDocument($data);
      parent::display($tpl);
   }

   /**
    * Get progress information from separate table.
    * This code should be in the model. Move when convenient.
    * @param $data
    * @return bool|mixed
    */
   function get_progress_info($data)
   {
      $issue_id = $data->id;

      $user    = JFactory::getUser();
      $isadmin = IssueTrackerHelperSite::isIssueAdmin($user->id);
      $isstaff = IssueTrackerHelperSite::isIssueStaff($user->id);
      $owner = 0;

      if ( !($isadmin || $isstaff) &&  ! $this->params->get('show_progress_field', 0) ) return null;

      $db      = JFactory::getDbo();
//      $user    = JFactory::getUser();
      $groups  = implode(',', $user->getAuthorisedViewLevels());
      $where   = ' AND access IN ('.$groups.')';

      if ( ! $user->guest  && $data->identified_by_person_id == IssueTrackerHelperSite::getitPersonid($user->id) )
         $owner = 1;

      if ( ($isadmin || $isstaff || $owner) ) {
         $where .= " AND state IN (0,1) ";
         $where .= ' AND public IN (0,1) ';
      } else {
         $where .= " AND state = 1 ";
         $where .= ' AND public = 1 ';
      }

      $query = "SELECT count(*) FROM `#__it_progress` WHERE issue_id = '".$issue_id."'";

      $query .= $where;
      $db->setQuery($query);
      $cnt = $db->loadResult();

      if ( $cnt == 0 ) {
         return false;
      } else {
         $query = "SELECT * FROM `#__it_progress` WHERE issue_id = '".$issue_id."'";
         $query .= $where;
         $query .= " ORDER BY lineno ASC";
         $db->setQuery($query);
         $progress = $db->loadObjectList();
         return $progress;
      }
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
    * Check if any attachments and get details.
    * This code should be in the model. Move when convenient.
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

   /**
    * Prepares the document
    * @param $data
    */
   protected function _prepareDocument($data)
   {
      $app        = JFactory::getApplication();
      $menus      = $app->getMenu();
      $pathway    = $app->getPathway();
      $title      = null;

      $document = JFactory::getDocument();

      // Because the application sets a default page title,
      // we need to get it from the menu item itself
      $menu = $menus->getActive();
      if ($menu) {
         $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
      }

      $title = $this->params->def('page_title', JText::_('COM_ISSUETRACKER_FORM_ISSUE'));
      if ($app->get('sitename_pagetitles', 0) == 1) {
         $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);

      } elseif ($app->get('sitename_pagetitles', 0) == 2) {
         $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
      }
      /*
      if ($app->get('sitename_pagetitles', 0) == 1) {
         $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
      } elseif ($app->get('sitename_pagetitles', 0) == 2) {
         $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
      }
      */
      $document->setTitle($title);

      // Special case to trap situation where we are called from the projects list links.
      if ( $menu && strpos($menu->link, 'itprojectslist') ) {
         $ntitle = JText::_('COM_ISSUETRACKER_PROJECT_ISSUEDETAIL_TITLE');
         $document->setTitle($ntitle);
         $this->params->set('page_heading', $ntitle);
      }

      // Check if we are called as a popup. i.e. From the latest issues module.
      // If so blank out the page title.  Ideally we want the module title.
      $cururl  = JURI::getInstance()->toString();
      $popup   = strpos($cururl, 'tmpl=component');
      if ( $popup ) {
         $this->params->set('page_heading', null);
      }

      $pathway->addItem('Issue '.$data->alias, '');

   }
}