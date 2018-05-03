<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'joomla.application.component.view' );

/**
 * Issuetracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issuetracker
 */
class IssuetrackerViewItloglist extends JViewLegacy
{
   protected $items;
   protected $pagination;
   protected $state;

   /**
    * Display the view
    * @param null $tpl
    * @return mixed
    */
   public function display($tpl = null)
   {
      $this->state      = $this->get('State');
      $this->items      = $this->get('Items');
      $this->pagination = $this->get('Pagination');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      JHtml::stylesheet(JPATH_COMPONENT_ADMINISTRATOR.DS.'css', array(), true, false, false);

      $this->addToolbar();

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->sidebar = JHtmlSidebar::render();
      } else {
         $this->setLayout("default25");
      }
      // JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=attachments');

      return parent::display($tpl);
   }

   /**
    * Add the page title and toolbar.
    *
    * @since   1.6
    */
   protected function addToolbar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      $state   = $this->get('State');
      $canDo   = IssueTrackerHelper::getActions($state->get('filter.category_id'));

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_LOG'), 'file-2');

      if ($canDo->get('core.edit.state')) {
         //If this component does not use state then show a direct delete button as we can not trash
         JToolBarHelper::deleteList('', 'itloglist.delete','JTOOLBAR_DELETE');
      }
      JToolBarHelper::custom('itloglist.purge', 'trash.png', '', 'COM_ISSUETRACKER_PURGE', false);

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=itloglist');

         JHtmlSidebar::addFilter(
            JText::_('COM_ISSUETRACKER_SELECT_PRIORITY'),
            'filter_priority',
            JHtml::_('select.options', IssueTrackerHelper::getLogPriorities(1), 'value', 'text', $this->state->get('filter.priority'))
         );
      }
   }

   /**
    * Returns an array of fields the table can be sorted by
    *
    * @return  array  Array containing the field name to sort by as the key and display text as value
    *
    * @since   3.0
    */
   protected function getSortFields()
   {
      return array(
        'a.priority' => JText::_('COM_ISSUETRACKER_LOG_PRIORITY_NAME'),
        'a.date'     => JText::_('COM_ISSUETRACKER_DATE'),
        'a.message'  => JText::_('COM_ISSUETRACKER_LOG_MESSAGE'),
        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
        'a.id'       => JText::_('JGRID_HEADING_ID')
      );
   }
}