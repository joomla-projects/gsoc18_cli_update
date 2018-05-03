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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import( 'joomla.application.component.view' );

/**
 * Issue Tracker View
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewItpeoplelist extends JViewLegacy
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

      if (!empty($this->items)) {
         $this->items = IssueTrackerHelper::updateprojectname($this->items);
      }

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

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_MANAGER_PEOPLE'), 'users');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'itpeople';
        if (file_exists($formPath)) {
            if ($canDo->get('core.create')) {
             JToolBarHelper::addNew('itpeople.add','JTOOLBAR_NEW');
          }
          if ($canDo->get('core.edit')) {
             JToolBarHelper::editList('itpeople.edit','JTOOLBAR_EDIT');
          }
        }

      if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->published)) {
               JToolBarHelper::divider();
               JToolBarHelper::publishList('itpeoplelist.publish', 'JTOOLBAR_PUBLISH', true);
               JToolBarHelper::unpublishList('itpeoplelist.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            }

            JToolBarHelper::divider();
            JToolBarHelper::custom('itpeoplelist.administrator', 'admin.png', 'admin.png','COM_ISSUETRACKER_ADMIN', true);
            JToolBarHelper::custom('itpeoplelist.notadministrator', 'deadmin.png', 'deadmin.png', 'COM_ISSUETRACKER_NOT_ADMIN', true);
            JToolBarHelper::divider();
            JToolBarHelper::custom('itpeoplelist.staff', 'staff.png', 'staff.png','COM_ISSUETRACKER_ISSUES_STAFF', true);
            JToolBarHelper::custom('itpeoplelist.notstaff', 'notstaff.png', 'notstaff.png', 'COM_ISSUETRACKER_ISSUES_NOT_STAFF', true);
            JToolBarHelper::divider();
            JToolBarHelper::custom('itpeoplelist.notify', 'notify.png', 'notify.png','COM_ISSUETRACKER_NOTIFY', true);
            JToolBarHelper::custom('itpeoplelist.nonotify', 'denotify.png', 'denotify.png', 'COM_ISSUETRACKER_DENOTIFY', true);
            if ( IssuetrackerHelper::comp_installed('com_acysms')) {
               JToolBarHelper::custom('itpeoplelist.smsnotify', 'phone.png', 'phone.png', 'COM_ISSUETRACKER_SMS_NOTIFICATIONS', true);
               JToolBarHelper::custom('itpeoplelist.nosmsnotify', 'nophone.png', 'nophone.png', 'COM_ISSUETRACKER_NOSMS', true);
            }
            JToolBarHelper::divider();
            if (isset($this->items[0]->state)) {
               JToolBarHelper::custom('itpeoplelist.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
               JToolBarHelper::custom('itpeoplelist.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
               //If this component does not use state then show a direct delete button as we can not trash
               // JToolBarHelper::deleteList('', 'itpeoplelist.delete','JTOOLBAR_DELETE');
               JToolBarHelper::deleteList(JText::_('COM_ISSUETRACKER_PEOPLE_DELETE_WARNING'),'itpeoplelist.delete');
            }

            if (isset($this->items[0]->state)) {
             JToolBarHelper::divider();
             JToolBarHelper::archiveList('itpeoplelist.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
               JToolBarHelper::custom('itpeoplelist.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
      }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
          if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
             JToolBarHelper::deleteList('', 'itpeoplelist.delete','JTOOLBAR_EMPTY_TRASH');
             JToolBarHelper::divider();
          } else if ($canDo->get('core.edit.state')) {
             JToolBarHelper::trash('itpeoplelist.trash','JTOOLBAR_TRASH');
             JToolBarHelper::divider();
          }
        }

      if ($canDo->get('core.admin')) {
         JToolBarHelper::preferences('com_issuetracker', '600','800');
      }

      JToolBarHelper::divider();
      JToolBarHelper::help( 'screen.issuetracker', true );

      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         JHtmlSidebar::setAction('index.php?option=com_issuetracker&view=itprioritylist');

         JHtmlSidebar::addFilter(
            '- '.JText::_('COM_ISSUETRACKER_SELECT_ROLE').' -',
            'filter_roles',
            JHtml::_('select.options', IssueTrackerHelper::getRoles(1), 'value', 'text', $this->state->get('filter.roles'), true)
         );

         JHtmlSidebar::addFilter(
            '- '.JText::_('COM_ISSUETRACKER_SELECT_PROJECT').' -',
            'filter_project',
            JHtml::_('select.options', IssueTrackerHelper::getProject_name(1), 'value', 'text', $this->state->get('filter.project'), true)
         );

         JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true)
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
        'a.person_name' => JText::_('COM_ISSUETRACKER_PERSON_NAME'),
        'a.user_id' => JText::_('COM_ISSUETRACKER_USER_ID'),
        'a.username' => JText::_('COM_ISSUETRACKER_USERNAME'),
        'a.person_email' => JText::_('COM_ISSUETRACKER_PERSON_EMAIL'),
        'a.person_role' => JText::_('COM_ISSUETRACKER_PERSON_ROLE'),
        'a.assigned_project' => JText::_('COM_ISSUETRACKER_ASSIGNED_PROJECT'),
        'a.registered' => JText::_('COM_ISSUETRACKER_REGISTERED'),
        'a.issues_admin' => JText::_('COM_ISSUETRACKER_ISSUES_ADMINISTRATOR'),
        'a.staff' => JText::_('COM_ISSUETRACKER_ISSUES_STAFF'),
        'a.email_notifications' => JText::_('COM_ISSUETRACKER_EMAIL_NOTIFICATIONS'),
        'a.published' => JText::_('JPUBLISHED'),
        'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
        'a.state' => JText::_('JPUBLISHED'),
//        'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
        'a.id' => JText::_('JGRID_HEADING_ID')
       );
   }
}