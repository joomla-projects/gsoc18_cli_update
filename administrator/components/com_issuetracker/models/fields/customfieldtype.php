<?php
/*
 *
 * @Version       $Id: customfieldtype.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('JPATH_BASE') or die('Restricted access');

JLoader::import('joomla.html.html');
JLoader::import('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldCustomfieldtype extends JFormField
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'Customfieldtype';

   /**
    * Method to get the field input markup.
    *
    * @return  string   The field input markup.
    * @since   1.6
    */
   protected function getInput()
   {
      // Initialize variables.
      $typeOptions = array();

      $typeOptions[] = JHTML::_('select.option', 0, JText::_('COM_ISSUETRACKER_SELECT_TYPE'));
      $typeOptions[] = JHTML::_('select.option', 'textfield', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_TEXTFIELD'));
      $typeOptions[] = JHTML::_('select.option', 'textarea', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_TEXTAREA'));
      $typeOptions[] = JHTML::_('select.option', 'select', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_DROPDOWN_SELECTION'));
      $typeOptions[] = JHTML::_('select.option', 'multipleSelect', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_MULTIPLESELECT'));
      $typeOptions[] = JHTML::_('select.option', 'radio', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_RADIO'));
      $typeOptions[] = JHTML::_('select.option', 'multipleCheckbox', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_CHECKBOX'));
      $typeOptions[] = JHTML::_('select.option', 'date', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_DATE'));
      $typeOptions[] = JHTML::_('select.option', 'header', JText::_('COM_ISSUETRACKER_CUSTOM_FIELD_HEADER'));

      return JHTML::_('select.genericlist',  $typeOptions,  $this->name, 'OnChange="setDisplay(this.form);" class="inputbox"', 'value', 'text', $this->value);

   }
}
