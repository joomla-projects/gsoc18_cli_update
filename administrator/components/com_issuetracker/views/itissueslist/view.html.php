<?php
/*
 *
 * @Version       $Id: view.html.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// JLoader::import( 'joomla.application.component.view' );

/**
 * Issue Tracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewItissueslist extends JViewLegacy
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

      /*
       * We need to load all items because of creating tree
       * After creating tree we get info from pagination
       * and will set displaying of categories for current pagination
       * E.g. pagination is limitstart 5, limit 5 - so only categories from 5 to 10 will be displayed
       */

      if (!empty($this->items)) {
//         $istrt = 0;
//         if (count($this->items) == 1 ) {
//            // Cludge for situation where we have performed a search and have only one element.
//            $istrt = $this->items[0]->parent_id;
//         }
         $this->items = IssueTrackerHelper::updateprojectname($this->items);
      }

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
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      $state   = $this->get('State');
      // $canDo   = IssueTrackerHelper::getActions($state->get('filter.category_id'));
      $canDo   = IssueTrackerHelper::getActions();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_ISSUES'), 'issues');

      //Check if the form exists before showing the add/edit buttons
      $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'itissues';
      if (file_exists($formPath)) {
         if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('itissues.add','JTOOLBAR_NEW');
         }
         if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('itissues.edit','JTOOLBAR_EDIT');
         }
      }

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $bar = JToolBar::getInstance('toolbar');
         $bar->appendButton('Popup', 'download', 'JTOOLBAR_EXPORT', 'index.php?option=com_issuetracker&amp;view=download&amp;tmpl=component', 600, 300);
      }

      if ($canDo->get('core.edit.state')) {
         if (isset($this->items[0]->state)) {
            // JToolBarHelper::divider();
            JToolBarHelper::custom('itissueslist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('itissueslist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
         } else {
            //If this component does not use state then show a direct delete button as we can not trash
            JToolBarHelper::deleteList('', 'itissueslist.delete','JTOOLBAR_DELETE');
         }

         if (isset($this->items[0]->state)) {
            // JToolBarHelper::divider();
            JToolBarHelper::archiveList('itissueslist.archive','JTOOLBAR_ARCHIVE');
         }
         if (isset($this->items[0]->checked_out)) {
            JToolBarHelper::custom('itissueslist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
         }
      }

      //Show trash and delete for components that uses the state field
      if (isset($this->items[0]->state)) {
         if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'itissueslist.delete','JTOOLBAR_EMPTY_TRASH');
            // JToolBarHelper::divider();
         } else if ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('itissueslist.trash','JTOOLBAR_TRASH');
            // JToolBarHelper::divider();
         }
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker');
      }

      // JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      $show_assigned = $this->state->params->get('show_assigned_to_headings');
      $show_identifier = $this->state->params->get('show_identified_by_headings');

//      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {

         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=attachments');

         if ($show_assigned == 1 ) {
            JHtmlSidebar::addFilter(
               '- '.JText::_('COM_ISSUETRACKER_SELECT_ASSIGNED').' -',
               'filter_assigned',
               JHtml::_('select.options', IssueTrackerHelper::getAssignedPeople(1), 'value', 'text', $this->state->get('filter.assigned'))
            );
         }

         if ($show_identifier == 1 ) {
            JHtmlSidebar::addFilter(
               '- '.JText::_('COM_ISSUETRACKER_SELECT_IDENTIFIER').' -',
               'filter_identifier',
               JHtml::_('select.options', IssueTrackerHelper::getIdentifyingPeople(1), 'value', 'text', $this->state->get('filter.identifier'))
            );
         }

         JHtmlSidebar::addFilter(
            '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -',
            'filter_project_id',
            JHtml::_('select.options', IssueTrackerHelper::getProject_name(1), 'value', 'text', $this->state->get('filter.project_id'))
         );

         JHtmlSidebar::addFilter(
            '- '.JText::_('COM_ISSUETRACKER_SELECT_STATUS').' -',
            'filter_status_id',
            JHtml::_('select.options', IssueTrackerHelper::getStatuses(1), 'value', 'text', $this->state->get('filter.status_id'))
         );

         JHtmlSidebar::addFilter(
            '- '.JText::_('COM_ISSUETRACKER_SELECT_PRIORITY').' -',
            'filter_priority_id',
            JHtml::_('select.options', IssueTrackerHelper::getPriorities(1), 'value', 'text', $this->state->get('filter.priority_id'))
         );

         JHtmlSidebar::addFilter(
            '- '.JText::_('COM_ISSUETRACKER_SELECT_TYPE').' -',
            'filter_type_id',
            JHtml::_('select.options', IssueTrackerHelper::getTypes(1), 'value', 'text', $this->state->get('filter.type_id'))
         );

         JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
              'filter_state',
              JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
         );

         JHtmlSidebar::addFilter(
             '-' . JText::_('JSELECT') . ' ' . JText::_('JTAG') . '-',
            'filter_tag',
            JHtml::_('select.options', JHtml::_('tag.options', true, true), 'value', 'text', $this->state->get('filter.tag'))
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
      $show_assigned = $this->state->params->get('show_assigned_to_headings');
      $show_identifier = $this->state->params->get('show_identified_by_headings');
      $show_created_by = $this->state->params->get('show_created_by_headings');
      $show_created_on = $this->state->params->get('show_created_on_headings');
      $show_modified_by = $this->state->params->get('show_modified_by_headings');
      $show_modified_on = $this->state->params->get('show_modified_on_headings');

      $show_start_date = $this->state->params->get('show_identified_date_headings');
      $show_close_date = $this->state->params->get('show_close_date_headings');

      $allow_private = $this->state->params->get('allow_private_issues');

      $array = array();

        $array['a.issue_summary']   = JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY');
        $array['a.alias']           = JText::_('COM_ISSUETRACKER_ISSUE_NUMBER');
        $array['t2.path']      = JText::_('COM_ISSUETRACKER_PROJECT_NAME');

        if ($show_identifier == 1 ) {
           $array['t7.person_name'] = JText::_( 'COM_ISSUETRACKER_IDENTIFYING_PERSON' );
        }

        if ($show_assigned == 1 ) {
            $array['t3.person_name'] = JText::_( 'COM_ISSUETRACKER_ASSIGNED_PERSON' );
        }

        $array['a.status']          = JText::_('JSTATUS');
        $array['a.issue_type']      = JText::_('COM_ISSUETRACKER_TYPE');
        $array['a.priority']        = JText::_('COM_ISSUETRACKER_PRIORITY');

        $array['a.ordering']          = JText::_('JGRID_HEADING_ORDERING');
        if (isset($this->items[0]->state)) {
           $array['a.state']           = JText::_('JPUBLISHED');
        }

        if ($allow_private == 1 ) {
           if (isset($this->items[0]->public)) {
              $array['a.public'] = JText::_( 'COM_ISSUETRACKER_FIELD_PUBLIC');
           }
        }

        if ($show_start_date == 1 ) {
           $array['a.identified_date'] = JText::_( 'COM_ISSUETRACKER_FIELD_IDENTIFIED_DATE' );
           }
        if ($show_close_date == 1 ) {
           $array['a.actual_resolution_date']   = JText::_( 'COM_ISSUETRACKER_FIELD_CLOSE_DATE' );
        }

         //        'a.language' => JText::_('JGRID_HEADING_LANGUAGE');
        $array['a.id']              = JText::_('JGRID_HEADING_ID');
        if ($show_created_on == 1 ) {
           $array['a.created_on']   = JText::_('COM_ISSUETRACKER_CREATED_ON');
        }
        if ($show_created_by == 1 ) {
           $array['a.created_by']  = JText::_('COM_ISSUETRACKER_CREATED_BY');
        }
        if ($show_modified_by == 1 ) {
           $array['a.modified_on'] = JText::_('COM_ISSUETRACKER_FIELD_MODIFIED_ON_LABEL');
        }
        if ($show_modified_on == 1 ) {
           $array['a.modified_by']    = JText::_('COM_ISSUETRACKER_FIELD_MODIFIED_BY_LABEL');
        }

      return $array;
   }
}