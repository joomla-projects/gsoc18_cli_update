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

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.application.component.view');
//JLoader::import('joomla.html.pane' );

// import Joomla controlleradmin library
//JLoader::import('joomla.application.component.controlleradmin');

/**
 * Class IssueTrackerViewCPanel
 */
class IssueTrackerViewCPanel extends JViewLegacy
{
   protected $params;
   //public $tmpl;
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null)
   {
      $user  = JFactory::getUser();

      $this->params = JComponentHelper::getParams( 'com_issuetracker' );

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);
      JToolBarHelper::title("Issue Tracker - " . JText::_('COM_ISSUETRACKER_CPANEL_TITLE'), 'home-2 cpanel');

      if($user->authorise('core.admin', 'com_issuetracker')){
         JToolBarHelper::divider();
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      require_once ( JPATH_COMPONENT.DS.'models'.DS.'itissueslist.php');
      $issuesModel = new IssueTrackerModelItissueslist;

      $this->latestIssues = $issuesModel->latestIssues( 10);       // get 10 latest issues

      $this->overdueIssues = $issuesModel->overdueIssues( 10);     // get 10 worse overdue issues

      if ($this->params->get('show_summary_rep', 0)) {
         $this->summaryIssues = $issuesModel->issueSummary();      // get project summary counts
      }

      $this->unassignedIssues = $issuesModel->unassignedissues();  // get unassigned issues

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->sidebar = JHtmlSidebar::render();
      } else {
         $this->setLayout("default25");
      }

      parent::display($tpl);
   }
}