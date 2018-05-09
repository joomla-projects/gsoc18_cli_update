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

JLoader::import( 'joomla.application.component.view');

/**
 * HTML View class for the Issue Tracker Component
 *
 * @package    Issue Tracker
 * @subpackage Components
 */
class IssueTrackerViewItissueslist extends JViewLegacy
{
   protected $print;
   protected $state;
   protected $pagination;
   protected $params;
   protected $pageclass_sfx;
   protected $data;
   protected $sortDirection;
   protected $sortColumn;
   protected $pid;
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null){
      $app = JFactory::getApplication();

      // Filter for userid
      $user = JFactory::getUser();
      if (!$user->guest) {
         JFactory::getApplication()->input->set('cuserid', $user->id);
      }

      $this->state      = $this->get('State');

      $this->params  = $app->getParams();
      $this->data    = $this->get('Data');

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

      $this->print      = JFactory::getApplication()->input->getBool('print');

      $this->pagination    = $this->get('Pagination');

      $this->sortDirection    = $this->state->get('filter_order_Dir');
      $this->sortColumn       = $this->state->get('filter_order');
      $this->pid              = $this->state->get('project_value');

      $this->_prepareDocument();

      parent::display($tpl);
   }

   /**
   * Prepares the document
   */
   protected function _prepareDocument()
   {
      $app        = JFactory::getApplication();
      $menus      = $app->getMenu();
      $pathway    = $app->getPathway();
      $title      = null;
      $document   = JFactory::getDocument();

      // Because the application sets a default page title,
      // we need to get it from the menu item itself
      $menu       = $menus->getActive();

      if ($menu) {
         $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
      } else {
         $this->params->def('page_heading', JText::_('COM_ISSUETRACKER_ISSUES'));
      }

      // $id      = (int) @$menu->query['id'];

      $title   = $this->params->get('page_title', '');
      if (empty($title)) {
         $title = $app->get('sitename');
      } elseif ($app->get('sitename_pagetitles', 0) == 1) {
         $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
      } elseif ($app->get('sitename_pagetitles', 0) == 2) {
         $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
      }

      $document->setTitle($title);

      // Special case for when we are called via the Project menu item link.
      $ppid = JFactory::getApplication()->input->get('pid');
      if ( $ppid) {
         $ntitle = JText::_('COM_ISSUETRACKER_PROJECT_ISSUESLIST_TITLE');
         $pathway->addItem($ntitle, '');
         // Set page title and heading.
         $document->setTitle($ntitle);
         $this->params->set('page_heading', $ntitle);
      }
   }
}