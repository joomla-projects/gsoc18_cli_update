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
 * @package    Joomla.Components
 * @subpackage Issue Tracker
 */
class IssueTrackerViewItpeople extends JViewLegacy
{
   protected $print;
   protected $params;
   protected $pageclass_sfx;
   protected $data;
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null)
   {
      $app = JFactory::getApplication();
      $pathway = $app->getPathway();
      $params  = $app->getParams();
      $this->params = $params;
      $document = JFactory::getDocument();

      //Escape strings for HTML output
      $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

      $this->print      = JFactory::getApplication()->input->getBool('print');

      $data = $this->get('Data');
      $this->data = $data;

      if ( is_null($data) || $data->id == 0 ) {
         // No person was found.
         if ( isset($_SERVER['HTTP_REFERER']) ) {
            $previousurl = $_SERVER['HTTP_REFERER'];
         } else {
            $previousurl = JURI::base();
         }
         $msg = JText::_('COM_ISSUETRACKER_PERSON_NOT_FOUND');
         $app->enqueueMessage($msg);
         $app->redirect($previousurl);
      }

      // Special case capture for title and page heading where called from a link
      // rather than a menu item directly.
      $menus   = $app->getMenu();
      $menu    = $menus->getActive();

      $ntitle = JText::_('COM_ISSUETRACKER_PEOPLE_DETAIL_TITLE');
      if ( strpos($menu->link, 'itpeoplelist') ) {
         $document->setTitle($ntitle);
         $params->set('page_heading', $ntitle);
      }

      $pathway->addItem($ntitle, '');

      parent::display($tpl);
   }
}
