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
class IssuetrackerViewJchange extends JViewLegacy
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

      // $user    = JFactory::getUser();
      // $isNew      = ($this->item->id == 0);
/*
        if (isset($this->item->checked_out)) {
          $checkedOut   = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
*/
      // $canDo      = IssueTrackerHelper::getActions();

      JHtml::stylesheet('com_issuetracker/administrator.css', array(), true, false, false);
      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_TITLE_JCHANGE'), 'history.png');

      // If not checked out, can save the item.
/*
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))) {
         JToolBarHelper::apply('jchange.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('jchange.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))){
         JToolBarHelper::custom('jchange.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
*/
/*
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('jchange.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
*/
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('jchange.cancel', 'JTOOLBAR_CANCEL');
      } else {
         JToolBarHelper::cancel('jchange.cancel', 'JTOOLBAR_CLOSE');
      }
   }
}