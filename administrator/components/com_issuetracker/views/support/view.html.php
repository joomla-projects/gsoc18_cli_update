<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.1
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'joomla.application.component.view');

/**
 * Class IssueTrackerViewSupport
 */
class IssueTrackerViewSupport extends JViewLegacy
{
   /**
    * @param null $tpl
    * @return mixed|void
    */
   function display($tpl = null)
   {
      $user  = JFactory::getUser();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title("Issue Tracker - " . JText::_('COM_ISSUETRACKER_TITLE_SUPPORT'), "systeminfo");

      if($user->authorise('core.admin', 'com_issuetracker')){
         JToolBarHelper::preferences('com_issuetracker', '600', '800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->sidebar = JHtmlSidebar::render();
 //         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=attachments');
      } else {
         $this->setLayout("default25");
      }

      return parent::display($tpl);
   }

}
