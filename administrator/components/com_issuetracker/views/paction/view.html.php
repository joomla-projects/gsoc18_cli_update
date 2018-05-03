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

JLoader::import('joomla.application.component.view');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
/**
 * Issue Tracker view
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
class IssueTrackerViewPaction extends JViewLegacy
{
   protected $state;
   protected $item;
   protected $form;

   /**
    * Display the view
    * @param null $tpl
    * @return mixed
    */
   public function display($tpl = null)
   {
      $this->state   = $this->get('State');
      $this->item    = $this->get('Item');
      $this->form    = $this->get('Form');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      $this->params  = JComponentHelper::getParams( 'com_issuetracker' );

      if ($this->item->id == 0 ) {
         $def_pstate = $this->params->get('def_pstate',0);
         $this->form->setFieldAttribute('state', 'default',  $def_pstate);
         $def_progresspublic = $this->params->get('def_progresspublic',1);
         $this->form->setFieldAttribute('public', 'default',  $def_progresspublic);
         /*  This is not working here for some strange reason.
         $def_paccess = $this->params->get('def_paccess',2);
         $this->form->setFieldAttribute('access', 'default',  $def_paccess);
         */
         $this->form->setFieldAttribute('lineno',     'readonly',     'true');
         $this->form->setFieldAttribute('alias',      'type',         'selectissuealias');
         $this->form->setFieldAttribute('alias',      'readonly',     'false');
         $this->form->setFieldAttribute('alias',      'required',     'true');
         $this->form->setFieldAttribute('issue_id',   'readonly',     'true');
         $this->form->setFieldAttribute('progress',   'required',     'true');
      }

      $this->return_page   = $this->get('ReturnPage');

      $this->addToolbar();
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->setLayout("edit");
      } else {
         $this->setLayout("edit25");
      }

      // Add in path to common audit templates
      $this->_addPath( 'template', JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'common' . DS . 'tmpl' );

      return parent::display($tpl);
   }

   /**
    * Add the page title and toolbar.
    */
   protected function addToolbar()
   {
      require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';

      // JRequest::setVar('hidemainmenu', true);
      JFactory::getApplication()->input->set('hidemainmenu', true);

      $user    = JFactory::getUser();
      $isNew   = ($this->item->id == 0);
      if (isset($this->item->checked_out)) {
         $checkedOut   = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
      } else {
         $checkedOut = false;
      }

      $canDo   = IssueTrackerHelper::getActions();
      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);

      // get caller details
      $referer = $_SERVER['HTTP_REFERER'];

      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER_MANAGER_PROGRESS' ).': <small>[ ' . $text.' ]</small>', 'progress' );

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))) {
         if ( strpos($referer, 'itissues') == false) {
            // If called from itissues view do not allow save alone.
            JToolBarHelper::apply('paction.apply', 'JTOOLBAR_APPLY');
         }
         JToolBarHelper::save('paction.save', 'JTOOLBAR_SAVE');
      }

      if ( strpos($referer, 'itissues') == false) {
         // If called from itissues view do not permit new progress creation.
         if (!$checkedOut && ($canDo->get('core.create'))) {
            JToolBarHelper::custom('paction.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
         }
      }

      if (empty($this->item->id)) {
         JToolBarHelper::cancel('paction.cancel', 'JTOOLBAR_CANCEL');
      } else {
         JToolBarHelper::cancel('paction.cancel', 'JTOOLBAR_CLOSE');
      }
   }
}