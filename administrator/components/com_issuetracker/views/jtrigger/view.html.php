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
 * View to edit
 */
class IssuetrackerViewJtrigger extends JViewLegacy
{
   protected $state;
   protected $item;
   protected $form;

   /**
    * Display the view
    * @param null $tpl
    * @throws Exception
    * @return mixed
    */
   public function display($tpl = null)
   {
      $this->state   = $this->get('State');
      $this->item    = $this->get('Item');
      $this->form    = $this->get('Form');

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
      }

      // Modify the displayed fields for situation where we are editing or when this is a new trigger being created.
      // Similar to how we do it in Issue Tracker front end form.
      if (empty($this->item->id)) {
         // New record
         $this->form->setFieldAttribute('table_name',       'type',     'dbtables');
         $this->form->setFieldAttribute('table_name',       'readonly', 'false');
         $this->form->setFieldAttribute('table_name',       'required', 'true');
         $this->form->setFieldAttribute('trigger_type',     'readonly', 'false');
         $this->form->setFieldAttribute('trigger_type',     'required', 'true');
         $this->form->setFieldAttribute('trigger_event',    'readonly', 'false');
         $this->form->setFieldAttribute('trigger_event',    'required', 'true');
         // Hide text, name and columns fields.
         $this->form->setFieldAttribute('trigger_text',     'type',     'hidden');
         $this->form->setFieldAttribute('trigger_text',     'required', 'false');
         $this->form->setFieldAttribute('trigger_name',     'type',     'hidden');
         $this->form->setFieldAttribute('trigger_name',     'required', 'false');
         $this->form->setFieldAttribute('columns',          'type',     'hidden');
         $this->form->setFieldAttribute('columns',          'required', 'false');
      } else {
         $this->form->setFieldAttribute('table_name',       'type',     'text');
         $this->form->setFieldAttribute('table_name',       'readonly', 'true');
         $this->form->setFieldAttribute('table_name',       'required', 'true');
      }

      $this->form->setFieldAttribute('columns',          'default',  'All');

      $this->addToolbar();
      $jversion = new JVersion();
      if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
         $this->setLayout("edit");
      } else {
         $this->setLayout("edit25");
      }

      return parent::display($tpl);
   }

   /**
    * Add the page title and toolbar.
    */
   protected function addToolbar()
   {
      JFactory::getApplication()->input->set('hidemainmenu', true);

      $user    = JFactory::getUser();
      $isNew      = ($this->item->id == 0);
      if (isset($this->item->checked_out)) {
         $checkedOut   = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
      } else {
         $checkedOut = false;
      }
      $canDo      = IssuetrackerHelper::getActions();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);
      $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
      JToolBarHelper::title(   JText::_( 'COM_ISSUETRACKER_MANAGER_TRIGGERS' ).': <small>[ ' . $text.' ]</small>', 'trigger' );
      // JToolBarHelper::title(JText::_('COM_ISSUETRACKER_TITLE_JTRIGGER'), 'trigger.png');

      // If not checked out, can save the item.

      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))) {
         JToolBarHelper::apply('jtrigger.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('jtrigger.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))) {
         JToolBarHelper::custom('jtrigger.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
/*
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('jtrigger.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
*/
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('jtrigger.cancel', 'JTOOLBAR_CANCEL');
      }  else {
         JToolBarHelper::cancel('jtrigger.cancel', 'JTOOLBAR_CLOSE');
      }
   }
}