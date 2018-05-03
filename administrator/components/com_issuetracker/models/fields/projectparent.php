<?php
/**
 * @Version       $Id: projectparent.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

/**
 * Class JFormFieldProjectParent
 */
class JFormFieldProjectParent extends JFormFieldList
{
   /**
    * The form field type.
    *
    * @var     string
    * @since   1.6
    */
   protected $type = 'ProjectParent';

   /**
    * Method to get the field options.
    *
    * @return  array The field option objects.
    * @since   1.6
    */
   protected function getOptions()
   {
      // Initialise variables.
      // $options = array();
      $name = (string) $this->element['name'];

      // Let's get the id for the current item, either project or content item.
      $jinput = JFactory::getApplication()->input;
      // For projects the old project is the project id 0 for new project.
      if ($this->element['parent']) {
         $oldCat = $jinput->get('id',0);
         // $oldParent = $this->form->getValue($name);
      } else {
         // For items the old project is the project they are in when opened or 0 if new.
         // $thisItem = $jinput->get('id',0);
         $oldCat = $this->form->getValue($name);
      }

      $db      = JFactory::getDbo();
      $query   = $db->getQuery(true);

      $query->select('a.id AS value, a.title AS text, a.level');
      $query->from('#__it_projects AS a');
      $query->join('LEFT', $db->quoteName('#__it_projects').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');

      // Filter by the type
      $extension = $this->form->getValue('extension');
      //    if ($extension = $this->form->getValue('extension')) {
//       $query->where('(a.extension = '.$db->quote($extension).' OR a.parent_id = 0)');
//    }

      if ($this->element['parent']) {
         // Prevent parenting to children of this item.
         if ($id = $this->form->getValue('id')) {
            $query->join('LEFT', $db->quoteName('#__it_projects').' AS p ON p.id = '.(int) $id);
            $query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

            $rowQuery   = $db->getQuery(true);
            $rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
            $rowQuery->from('#__it_projects AS a');
            $rowQuery->where('a.id = ' . (int) $id);
            $db->setQuery($rowQuery);
            $row = $db->loadObject();
         }
      }
      $query->where('a.published IN (0,1)');
      $query->group('a.id, a.title, a.level, a.lft, a.rgt, a.parent_id');
      $query->order('a.lft ASC');

      // Get the options.
      $db->setQuery($query);

      $options = $db->loadObjectList();

      // Check for a database error.
      if ($db->getErrorNum()) {
         JError::raiseWarning(500, $db->getErrorMsg());
      }

      // Pad the option text with spaces using depth level as a multiplier.
      for ($i = 0, $n = count($options); $i < $n; $i++) {
         // Translate ROOT
         if ($options[$i]->level == 0) {
            $options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
         }
         $options[$i]->text = str_repeat('- ', $options[$i]->level).$options[$i]->text;
      }

      // Initialise variables.

      // Get the current user object.
      $user = JFactory::getUser();

      // For new items we want a list of projects you are allowed to create in.
      if ($oldCat == 0) {
         foreach ($options as $i => $option) {
            // To take save or create in a project you need to have create rights for that project
            // unless the item is already in that project.
            // Unset the option if the user isn't authorised for it. In this field assets are always projects.
            if ($user->authorise('core.create', $extension . '.itprojects.' . $option->value) != true ) {
               unset($options[$i]);
            }
         }
      } else {
         // If you have an existing project id things are more complex.
         //$projectOld = $this->form->getValue($name);
         foreach ($options as $i => $option) {
            // If you are only allowed to edit in this project but not edit.state, you should not get any
            // option to change the project parent for a project or the project for a content item,
            // but you should be able to save in that project.
            if ($user->authorise('core.edit.state', $extension . '.itprojects.' . $oldCat) != true) {
               if ($option->value != $oldCat) {
                  echo 'y';
                  unset($options[$i]);
               }
            }
            // However, if you can edit.state you can also move this to another project for which you have
            // create permission and you should also still be able to save in the current projecr.
               elseif (($user->authorise('core.create', $extension . '.itprojects.' . $option->value) != true)
                     && $option->value != $oldCat) {
                     echo 'x';
                     unset($options[$i]);
            }
         }
      }

      if (isset($row) && !isset($options[0])) {
         if ($row->parent_id == '1') {
            $parent = new stdClass();
            $parent->text = JText::_('JGLOBAL_ROOT_PARENT');
            array_unshift($options, $parent);
         }
      }

      // Merge any additional options in the XML definition.
      $options = array_merge(parent::getOptions(), $options);

      return $options;
   }
}
