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
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

// Load helpers
if (! class_exists('IssuetrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
if (! class_exists('IssuetrackerAuditHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'audit.php');
}

/**
 * View class for a list of Issue Tracker.
 */
class IssuetrackerViewJtriggers extends JViewLegacy
{
   protected $items;
   protected $pagination;
   protected $state;

   /**
    * Display the view
    * @param null $tpl
    * @throws Exception
    * @return mixed
    */
   public function display($tpl = null)
   {
      $this->state      = $this->get('State');
      $this->items      = $this->get('Items');
      $this->pagination = $this->get('Pagination');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         throw new Exception(implode("\n", $errors));
      }

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);
//      IssuetrackerHelper::addSubmenu('jtriggers');

      $this->addToolbar();

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->sidebar = JHtmlSidebar::render();
      } else {
         $this->setLayout("default25");
      }

      return parent::display($tpl);
   }

   /**
    * Add the page title and toolbar.
    *
    * @since   1.6
    */
   protected function addToolbar()
   {
      require_once JPATH_COMPONENT.'/helpers/issuetracker.php';

      $state   = $this->get('State');
      $canDo   = IssueTrackerHelper::getActions($state->get('filter.category_id'));

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_JTRIGGERS'), 'trigger.png');

      //Check if the form exists before showing the add/edit buttons
      $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/jtrigger';
      if (file_exists($formPath)) {

         if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('jtrigger.add','JTOOLBAR_NEW');
         }

         // Change this to view only.  No editing allowed on audit data!!!!!
         if ($canDo->get('core.edit') && isset($this->items[0])) {
            JToolBarHelper::editList('jtrigger.edit','JTOOLBAR_EDIT');
         }
      }

      if ($canDo->get('core.edit.state')) {
         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::custom('jtriggers.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('jtriggers.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
         } else if (isset($this->items[0])) {
            //If this component does not use state then show a direct delete button as we can not trash
            JToolBarHelper::deleteList('', 'jtriggers.delete','JTOOLBAR_DELETE');
         }

         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::archiveList('jtriggers.archive','JTOOLBAR_ARCHIVE');
         }
         if (isset($this->items[0]->checked_out)) {
            JToolBarHelper::custom('jtriggers.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
         }
      }

      JToolBarHelper::divider();
      // Use same icons as publish/unpublish for simplicity.
      JToolBarHelper::custom('jtriggers.enabletrig',  'publish.png',   'publish_f2.png',   'COM_ISSUETRACKER_ENABLE',  true);
      JToolBarHelper::custom('jtriggers.disabletrig', 'unpublish.png', 'unpublish_f2.png', 'COM_ISSUETRACKER_DISABLE', true);

      //Show trash and delete for components that uses the state field
      if (isset($this->items[0]->state)) {
         if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'jtriggers.delete','JTOOLBAR_EMPTY_TRASH');
            JToolBarHelper::divider();
         } else if ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('jtriggers.trash','JTOOLBAR_TRASH');
            JToolBarHelper::divider();
         }
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker');
      }

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         //Set sidebar action - New in 3.0
         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=jtriggers');

         $this->extra_sidebar = '';
/*
         JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
               'filter_state',
               JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
            );
*/
         JHtmlSidebar::addFilter(
            '- '.JText::_('COM_ISSUETRACKER_SELECT_TABLE').' -',
            'filter_tablename',
            JHtml::_('select.options', IssueTrackerAuditHelper::getTrig_tablename(1), 'value', 'text', $this->state->get('filter.tablename'))
         );
      }
   }

   /**
    * @return array
    */
   protected function getSortFields()
   {
      return array(
      'a.id' => JText::_('JGRID_HEADING_ID'),
      'a.table_name' => JText::_('COM_ISSUETRACKER_TABLE_NAME'),
      'a.trigger_schema' => JText::_('COM_ISSUETRACKER_JTRIGGERS_TRIGGER_SCHEMA'),
      'a.trigger_name' => JText::_('COM_ISSUETRACKER_JTRIGGERS_TRIGGER_NAME'),
      'a.trigger_type' => JText::_('COM_ISSUETRACKER_JTRIGGERS_TRIGGER_TYPE'),
      'a.trigger_event' => JText::_('COM_ISSUETRACKER_JTRIGGERS_TRIGGER_EVENT'),
      'a.trigger_text' => JText::_('COM_ISSUETRACKER_JTRIGGERS_TRIGGER_TEXT'),
      'a.columns' => JText::_('COM_ISSUETRACKER_JTRIGGERS_COLUMNS'),
      'a.action_orientation' => JText::_('COM_ISSUETRACKER_JTRIGGERS_ACTION_ORIENTATION'),
      'a.applied' => JText::_('COM_ISSUETRACKER_ENABLED'),
      'a.created_by' => JText::_('COM_ISSUETRACKER_CREATED_BY'),
      'a.created_by_alias' => JText::_('COM_ISSUETRACKER_JTRIGGERS_CREATED_BY_ALIAS'),
      'a.created_on' => JText::_('COM_ISSUETRACKER_CREATED_ON'),
      );
   }
}