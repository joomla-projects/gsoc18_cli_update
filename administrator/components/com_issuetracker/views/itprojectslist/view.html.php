<?php
/**
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

if (! class_exists('IssueTrackerHelper')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

/**
 * Issue Tracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewItprojectslist extends JViewLegacy
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

       // Preprocess the list of items to find ordering divisions.
      foreach ($this->items as &$item) {
         $this->ordering[$item->parent_id][] = $item->id;
      }

      $this->f_levels = IssueTrackerHelper::getProjectLevels(1);

      /*
       * We need to load all items because of creating tree
       * After creating tree we get info from pagination
       * and will set displaying of categories for current pagination
       * E.g. pagination is limitstart 5, limit 5 - so only categories from 5 to 10 will be displayed
       */

      JHtml::stylesheet(JPATH_COMPONENT_ADMINISTRATOR.DS.'css', array(), true, false, false);

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
      $canDo   = IssueTrackerHelper::getActions($state->get('filter.category_id'));

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_PROJECTS'), 'folder projects');

      //Check if the form exists before showing the add/edit buttons
      $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'itprojects';
      if (file_exists($formPath)) {
         if ($canDo->get('core.create')) {
            JToolBarHelper::addNew('itprojects.add','JTOOLBAR_NEW');
         }
         if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('itprojects.edit','JTOOLBAR_EDIT');
         }
      }

      if ($canDo->get('core.edit.state')) {
         if (isset($this->items[0]->state)) {
            // JToolBarHelper::divider();
            JToolBarHelper::custom('itprojectslist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
            JToolBarHelper::custom('itprojectslist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
         } else {
            //If this component does not use state then show a direct delete button as we can not trash
            JToolBarHelper::deleteList('', 'itprojectslist.delete','JTOOLBAR_DELETE');
         }

         if (isset($this->items[0]->state)) {
            // JToolBarHelper::divider();
            JToolBarHelper::archiveList('itprojectslist.archive','JTOOLBAR_ARCHIVE');
         }
         if (isset($this->items[0]->checked_out)) {
            JToolBarHelper::custom('itprojectslist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
         }
      }

      //Show trash and delete for components that uses the state field
      if (isset($this->items[0]->state)) {
         if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'itprojectslist.delete','JTOOLBAR_EMPTY_TRASH');
            // JToolBarHelper::divider();
         } else if ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('itprojectslist.trash','JTOOLBAR_TRASH');
            // JToolBarHelper::divider();
         }
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::custom('itprojectslist.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
         // JToolBarHelper::divider();
      }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker');
      }

      // JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      $jversion = new JVersion();
      if ( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=itprioritylist');

         JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
         );

         JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_MAX_LEVELS'),
           'filter_level',
           JHtml::_('select.options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'), true)
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

      $array = array();

      $array['a.title']             =  JText::_('COM_ISSUETRACKER_PROJECT_NAME');
      $array['a.description']       =  JText::_('COM_ISSUETRACKER_PROJECT_DESCRIPTION');
      if ($show_assigned == 1 ) {
         $array['a.assignee']       = JText::_( 'COM_ISSUETRACKER_ASSIGNED_PERSON' );
      }
      $array['a.start_date']        =  JText::_('COM_ISSUETRACKER_START_DATE');
      $array['a.target_end_date']   =  JText::_('COM_ISSUETRACKER_TARGET_END_DATE');
      $array['a.actual_end_date']   =  JText::_('COM_ISSUETRACKER_ACTUAL_END_DATE');
      $array['a.state']             =  JText::_('JPUBLISHED');
      $array['a.lft']               =  JText::_('JGRID_HEADING_ORDERING');
//      $array['a.language']   =  JText::_('JGRID_HEADING_LANGUAGE');
      $array['a.id']   =  JText::_('JGRID_HEADING_ID');

       return $array;
   }
}