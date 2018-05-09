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

/**
 * View class for a list of IssueTracker.
 */
class IssuetrackerViewJchanges extends JViewLegacy
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
//      IssueTrackerHelper::addSubmenu('jchanges');

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

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_CHISTORY'), 'history.png');

      //Check if the form exists before showing the add/edit buttons
      $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/jchange';
      if (file_exists($formPath)) {
/*
            if ($canDo->get('core.create')) {
             JToolBarHelper::addNew('jchange.add','JTOOLBAR_NEW');
          }
*/
         // TODO Change this to view only.  No editing allowed on audit data!!!!!
         if ($canDo->get('core.edit') && isset($this->items[0])) {
            JToolBarHelper::editList('jchange.edit','JTOOLBAR_EDIT');
         }

      }

      if ($canDo->get('core.edit.state')) {

/*
         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::custom('jchanges.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('jchanges.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
         } else if (isset($this->items[0])) {
*/
            //If this component does not use state then show a direct delete button as we can not trash
            JToolBarHelper::deleteList('', 'jchanges.delete','JTOOLBAR_DELETE');
//         }
/*
         if (isset($this->items[0]->state)) {
            JToolBarHelper::divider();
            JToolBarHelper::archiveList('jchanges.archive','JTOOLBAR_ARCHIVE');
         }
         if (isset($this->items[0]->checked_out)) {
            JToolBarHelper::custom('jchanges.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
         }
      }
*/
      //Show trash and delete for components that uses the state field
/*
      if (isset($this->items[0]->state)) {
         if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'jchanges.delete','JTOOLBAR_EMPTY_TRASH');
            JToolBarHelper::divider();
         } else if ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('jchanges.trash','JTOOLBAR_TRASH');
            JToolBarHelper::divider();
         }
*/
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker');
      }

      //Set sidebar action - New in 3.0
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=jchanges');

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
            JHtml::_('select.options', IssueTrackerHelper::getTablename(1), 'value', 'text', $this->state->get('filter.tablename'))
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
      'a.component' => JText::_('COM_ISSUETRACKER_COMPONENT'),
      'a.row_key' => JText::_('COM_ISSUETRACKER_ROW_KEY'),
      'a.row_key_link' => JText::_('COM_ISSUETRACKER_ROW_KEY_LINK'),
      'a.column_name' => JText::_('COM_ISSUETRACKER_COLUMN_NAME'),
      'a.column_type' => JText::_('COM_ISSUETRACKER_COLUMN_TYPE'),
      'a.state' => JText::_('JPUBLISHED'),
      'a.action' => JText::_('COM_ISSUETRACKER_ACTION'),
      'a.old_value' => JText::_('COM_ISSUETRACKER_OLD_VALUE'),
      'a.new_value' => JText::_('COM_ISSUETRACKER_NEW_VALUE'),
      'a.change_by' => JText::_('COM_ISSUETRACKER_CHANGED_BY'),
      'a.change_date' => JText::_('COM_ISSUETRACKER_CHANGE_DATE'),
      );
   }
}