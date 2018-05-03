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
defined('_JEXEC') or die('Restricted Access');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class IssueTrackerViewCustomfield extends JViewLegacy
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
      $this->params  = JComponentHelper::getParams( 'com_issuetracker' );

      // Check for errors.
      if (count($errors = $this->get('Errors'))) {
         JError::raiseError(500, implode("\n", $errors));
         return false;
      }

      JHTML::_('behavior.keepalive');
      $customField = & $this->item;

      if (!$customField->id) {
         $customField->published = 1;
         $customField->alias = '';
         $customField->required = 1;
         $customField->showNull = 0;
         $customField->displayInFrontEnd = 1;
      } else {
         $values = json_decode($customField->value);
         if (isset($values[0]->alias) && !empty($values[0]->alias)) {
            $customField->alias = $values[0]->alias;
         } else {
            $customField->alias = $customField->name;
         }
         $searches = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'à', 'á', 'â', 'ã', 'ä', 'å', '?', '?', '?', '?', '?', '?', 'Ç', 'ç', '?', '?', '?', '?', '?', '?', '?', '?', 'Ð', 'ð', '?', '?', '?', '?', 'È', 'É', 'Ê', 'Ë', 'è', 'é', 'ê', 'ë', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Ì', 'Í', 'Î', 'Ï', 'ì', 'í', 'î', 'ï', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Ñ', 'ñ', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Š', 'š', '?', '?', '?', '?', '?', '?', '?', 'Ù', 'Ú', 'Û', 'Ü', 'ù', 'ú', 'û', 'ü', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Ý', 'ý', 'ÿ', '?', '?', 'Ÿ', '?', '?', '?', '?', 'Ž', 'ž', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'y', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'y', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
         $replacements = array('A', 'A', 'A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'D', 'd', 'E', 'E', 'E', 'E', 'e', 'e', 'e', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'I', 'I', 'I', 'i', 'i', 'i', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'J', 'j', 'K', 'k', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'N', 'n', 'O', 'O', 'O', 'O', 'O', 'O', 'o', 'o', 'o', 'o', 'o', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'U', 'U', 'U', 'u', 'u', 'u', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'y', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 'a', 'b', 'g', 'd', 'e', 'z', 'h', 'th', 'i', 'k', 'l', 'm', 'n', 'x', 'o', 'p', 'r', 's', 't', 'y', 'f', 'ch', 'ps', 'w', 'A', 'B', 'G', 'D', 'E', 'Z', 'H', 'Th', 'I', 'K', 'L', 'M', 'X', 'O', 'P', 'R', 'S', 'T', 'Y', 'F', 'Ch', 'Ps', 'W', 'a', 'e', 'h', 'i', 'o', 'y', 'w', 'A', 'E', 'H', 'I', 'O', 'Y', 'W', 'i', 'i', 'y', 's', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Zero', 'A', 'A', 'A', 'E', 'E', 'E', 'B', 'V', 'G', 'G', 'G', 'G', 'G', 'Y', 'D', 'E', 'E', 'YO', 'E', 'E', 'E', 'YE', 'ZH', 'DZH', 'ZH', 'DZH', 'Z', 'Z', 'DZ', 'DZ', 'DZ', 'I', 'I', 'I', 'I', 'I', 'JI', 'I', 'Y', 'Y', 'J', 'K', 'Q', 'Q', 'K', 'Q', 'K', 'L', 'L', 'L', 'M', 'M', 'N', 'N', 'N', 'N', 'N', 'N', 'O', 'O', 'O', 'O', 'O', 'P', 'PF', 'P', 'P', 'S', 'S', 'T', 'TH', 'T', 'K', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'F', 'H', 'H', 'H', 'TS', 'TS', 'CH', 'CH', 'CH', 'CH', 'CH', 'DZ', 'SH', 'SHT', 'A', 'Y', 'Y', 'Y', 'Y', 'E', 'E', 'YU', 'YA', 'a', 'a', 'a', 'e', 'e', 'e', 'b', 'v', 'g', 'g', 'g', 'g', 'g', 'y', 'd', 'e', 'e', 'yo', 'e', 'e', 'e', 'ye', 'zh', 'dzh', 'zh', 'dzh', 'z', 'z', 'dz', 'dz', 'dz', 'i', 'i', 'i', 'i', 'i', 'ji', 'i', 'y', 'y', 'j', 'k', 'q', 'q', 'k', 'q', 'k', 'l', 'l', 'l', 'm', 'm', 'n', 'n', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'p', 'pf', 'p', 'p', 's', 's', 't', 'th', 't', 'k', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'f', 'h', 'h', 'h', 'ts', 'ts', 'ch', 'ch', 'ch', 'ch', 'ch', 'dz', 'sh', 'sht', 'a', 'y', 'y', 'y', 'y', 'e', 'e', 'yu', 'ya');
         $customField->alias = str_replace($searches, $replacements, $customField->alias);
         $filter = JFilterInput::getInstance();
         $customField->alias = $filter->clean($customField->alias, 'WORD');

         if (isset($values[0]->required)) {
            $customField->required = $values[0]->required;
         } else {
            $customField->required = 0;
         }
         if (isset($values[0]->showNull)) {
            $customField->showNull = $values[0]->showNull;
         } else {
            $customField->showNull = 0;
         }
         if (isset($values[0]->displayInFrontEnd)) {
            $customField->displayInFrontEnd = $values[0]->displayInFrontEnd;
         } else {
            $customField->displayInFrontEnd = 0;
         }
      }

      $customField->name = htmlspecialchars($customField->name, ENT_QUOTES, 'UTF-8');
/*
      TODO Consider providing option to create group on the fly.
      $lists = array();
      $lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $customField->published);

      $groups[] = JHTML::_('select.option', 0, JText::_('COM_ISSUETRACKER_CREATE_NEW_GROUP'));

      $customFieldModel = JModel::getInstance('Customfields', 'IssuetrackerModel');
      $uniqueGroups = $customFieldModel->getGroups(true);
      foreach ($uniqueGroups as $group) {
         $groups[] = JHTML::_('select.option', $group->id, $group->name);
      }

      $lists['group'] = JHTML::_('select.genericlist', $groups, 'groups', '', 'value', 'text', $customField->group);
*/
      JHTML::_('behavior.calendar');

      // Need these arrays so that the language strings get passed through!
      $document = JFactory::getDocument();
      $document->addScriptDeclaration('
      var IT_BasePath = "'.JURI::base(true).'/";
      var IT_JLanguage = [
         "'.JText::_('COM_ISSUETRACKER_REMOVE', true).'",
         "'.JText::_('COM_ISSUETRACKER_OPTIONAL', true).'",
         "'.JText::_('COM_ISSUETRACKER_ADD_AN_OPTION', true).'",
         "'.JText::_('COM_ISSUETRACKER_CALENDAR', true).'",
         "'.JText::_('COM_ISSUETRACKER_PLEASE_SELECT_A_FIELD_TYPE_FROM_THE_LIST_ABOVE', true).'",
      ];');

      JHTML::_('behavior.modal');

      /* Set up default for form */
      $this->form->setFieldAttribute('default_values',     'type', 'text');
      $this->form->setFieldAttribute('default_values',     'size', '80');
      $this->form->setFieldAttribute('default_values',     'readonly', 'true');
      $this->form->setFieldAttribute('default_values',     'default', JText::_('COM_ISSUETRACKER_PLEASE_SELECT_A_FIELD_TYPE_FROM_THE_LIST_ABOVE'));

      // Fix display when we have a defined field already.
      if ( $customField->id ) {
         $this->form->setFieldAttribute('default_values',   'type',  'hidden');
         if ($customField->type == 'header') {
            $this->form->setFieldAttribute('required',      'type',  'hidden');
            $this->form->setFieldAttribute('tooltip',       'type',  'hidden');
         }
         // echo "<pre>";var_dump($customField);echo "</pre>";
      }
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

      JToolBarHelper::title(JText::_('COM_ISSUETRACKER_TITLE_CUSTOMFIELD'), 'customfield');

      // If not checked out, can save the item.
      if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create')))) {
         JToolBarHelper::apply('customfield.apply', 'JTOOLBAR_APPLY');
         JToolBarHelper::save('customfield.save', 'JTOOLBAR_SAVE');
      }
      if (!$checkedOut && ($canDo->get('core.create'))) {
         JToolBarHelper::custom('customfield.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
      // If an existing item, can save to a copy.
      if (!$isNew && $canDo->get('core.create')) {
         JToolBarHelper::custom('customfield.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
      }
      if (empty($this->item->id)) {
         JToolBarHelper::cancel('customfield.cancel', 'JTOOLBAR_CANCEL');
      } else {
         JToolBarHelper::cancel('customfield.cancel', 'JTOOLBAR_CLOSE');
      }
   }
}