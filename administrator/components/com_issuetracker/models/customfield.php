<?php
/*
 *
 * @Version       $Id: customfield.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// No direct access.
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modeladmin');

/**
 * Issuetracker model.
 */
class IssuetrackerModelcustomfield extends JModelAdmin
{
   /**
    * @var     string   The prefix to use with controller messages.
    * @since   1.6
    */
   protected $text_prefix = 'COM_ISSUETRACKER';

   /**
    * Returns a reference to the a Table object, always creating it.
    *
    * @param string $type The object type
    * @param string $prefix A prefix for the table class name. Optional.
    * @param array $config Configuration array for model. Optional.
    * @internal param \The $type table type to instantiate
    * @return  JTable   A database object
    * @since   1.6
    */
   public function getTable($type = 'Customfield', $prefix = 'IssuetrackerTable', $config = array())
   {
      return JTable::getInstance($type, $prefix, $config);
   }

   /**
    * Method to get the record form.
    *
    * @param   array $data    An optional array of data for the form to interogate.
    * @param   boolean  $loadData   True if the form is to load its own data (default case), false if not.
    * @return  JForm A JForm object on success, false on failure
    * @since   1.6
    */
   public function getForm($data = array(), $loadData = true)
   {
      // Get the form.
      $form = $this->loadForm('com_issuetracker.customfield', 'customfield', array('control' => 'jform', 'load_data' => $loadData));
      if (empty($form)) {
         return false;
      }

      return $form;
   }

   /**
    * Method to get the data that should be injected in the form.
    *
    * @return  mixed The data for the form.
    * @since   1.6
    */
   protected function loadFormData()
   {
      // Check the session for previously entered form data.
      $data = JFactory::getApplication()->getUserState('com_issuetracker.edit.customfield.data', array());

      if (empty($data)) {
         $data = $this->getItem();
      }

      return $data;
   }

   /**
    * Method to get a single record.
    *
    * @param   $pk integer  The id of the primary key.
    *
    * @return  mixed Object on success, false on failure.
    * @since   1.6
    */
   public function getItem($pk = null)
   {
      if ($item = parent::getItem($pk)) {

         //Do any procesing on fields here if needed

      }
      return $item;
   }

   /**
    * Prepare and sanitise the table prior to saving.
    *
    * @since   1.6
    * @param JTable $table
    */
   protected function prepareTable($table)
   {
      jimport('joomla.filter.output');

      if (empty($table->id)) {
         // Set ordering to the last item if not set
         if (@$table->ordering === '') {
            $db = JFactory::getDbo();
            $db->setQuery('SELECT MAX(ordering) FROM #__it_custom_field');
            $max = $db->loadResult();
            $table->ordering = $max+1;
         }
      }
   }

   /**
    * @return JTable
    */
   function getData()
   {

      $cid = JFactory::getApplication()->input->get('cid');
      $row = self::getTable();
      $row->load($cid);
      return $row;
   }

   /**
    * @param array $data
    * @return bool
    */
   function save($data)
   {
      // $app     = JFactory::getApplication();
      $row     = self::getTable();
      $input   = JFactory::getApplication()->input;

      // print("In custom field save routine<p>");
      // echo "<pre>";var_dump($data);echo "</pre>";

      if (!$data['id']) {
         $row->ordering = $row->getNextOrder("`group` = {$data['group']}");
      }

      $objects = array();

      // Need to fetch these explicitly since they are not in jform data array.
      $values  = $input->get('option_value', null, 'default', 'none', 4);
      $names   = $input->get('option_name', null, 'default', 'none', 4);
      // $targets = $input->get('option_target', null, 'default', 'none', 4);

      $editor    = $data['textarea_editor'];
      $rows      = $data['textarea_rows'];
      $cols      = $data['textarea_cols'];
      if (array_key_exists('alias', $data) ) {
         $alias  = $data['alias'];
      } else {
         $alias = '';
      }
      $required  = $data['required'];
      $showNull  = $data['shownullflag'];
      $displayInFrontEnd = $data['displayinfe'];

      if (JString::strtolower($alias) == 'this') {
         $alias = '';
      }

      // print("Before for loop<p>");
      if ( sizeof($values) == 0) $values = array("");

      for ($i = 0; $i < sizeof($values); $i++) {
         $object = new JObject;
         $object->set('name', $names[$i]);

         switch ($data['type']) {
         case 'select':
            $object->set('value', $i + 1);
            break;
         case 'multipleSelect':
            $object->set('value', $i + 1);
            break;
         case 'radio':
            $object->set('value', $i + 1);
            break;
         case 'multipleCheckbox':
            $object->set('value', $i + 1);
            break;
         case 'textarea':
            $object->set('value', $data['default_values_textarea']);
            $object->set('editor', $data['textarea_editor']);
            $object->set('rows', $data['textarea_rows']);
            $object->set('cols', $data['textarea_cols']);
            break;
         case 'textfield':
            $object->set('value', $data['default_values_text']);
            break;
         case 'date':
            $object->set('value', $data['default_values_date']);
            break;
         case 'header':
            $object->set('value', $data['name']);
            $object->set('displayInFrontEnd', $displayInFrontEnd);
            break;
         default :
            $object->set('value', $values[$i]);
         }

         $object->set('alias', $alias);
         $object->set('required', $required);
         $object->set('showNull', $showNull);
         unset($object->_errors);
         $objects[] = $object;
      }

      $data['value'] = json_encode($objects);

      if ($input->get('task') == 'save2copy')  {
         $data['name'] .= ' (2)';
      }

      if (parent::save($data)) {
         return true;
      }

      return false;
   }

   /**
    *  getCustomFieldByGroup
    *  Retrieves the custom fields for a given group.
    *
    * @param $group
    * @param int $pstate
    * @param int $astate
    * @return mixed
   */
   function getCustomFieldByGroup($group, $pstate = 1, $astate = 0)
   {
      $db = JFactory::getDBO();
      $group  = (int)$group;
      $query  = "SELECT * FROM #__it_custom_field ";
      $query .= "WHERE `group`={$group} ";
      if ( $pstate == 1 )
         $query .= "AND state=1 ";
      if ( $astate != 0) {
         $user = JFactory::getUser();
         $groups  = implode(',', $user->getAuthorisedViewLevels());
         $query .= ' AND access IN ('.$groups.') ';
      }

      $query .= "ORDER BY ordering";
      $db->setQuery($query);
      $rows  = $db->loadObjectList();
      return $rows;
   }


   /**
    * Check if the required field is set for this element.
    * @param $value
    * @return bool
    */
    function check_requiredfield($value)
   {
      $required = false;
      // Expand out the field and check the elements.
      $defs = json_decode($value);
      foreach ($defs as $val) {
         if ( isset($val->required) && $val->required == 1 ) {
            $required = true;
         }
      }
      return $required;
   }


   /**
    * Check if any custom fields exist for the given project.
    * @param $pid
    * @param null $item_id
    * @param int $pstate
    * @param int $astate
    * @param int $dmode
    * @return mixed|null
    */

   function check_customfields($pid, $item_id = NULL, $pstate = 1, $astate = 0, $dmode = 0)
   {
      $db = JFactory::getDbo();
      $query = "SELECT customfieldsgroup FROM `#__it_projects` ";
      $query .= " WHERE id = '".$pid."'";
      if ( $pstate == 1)
         $query .= " AND state = 1 ";

      $db->setQuery($query);
      $grp = $db->loadResult();

      if ($grp) {
         $customfields   = self::getCustomFieldByGroup($grp, $pstate, $astate);
      } else {
         $customfields = NULL;
      }

      for ($i = 0; $i < sizeof($customfields); $i++) {
         $customfields[$i]->element = self::renderCustomField($customfields[$i], $item_id, $dmode);
      }
      return $customfields;
   }


   /**
    * Render Custom Field Display
    * Renders the custom field information in a form suitable for the input of information.
    * @param $customField
    * @param null $itemID
    * @param int $displayonly
    * @return null|string
    */
   function renderCustomField($customField, $itemID = NULL, $displayonly = 0)
   {
      // $app = JFactory::getApplication();

      if (!is_null($itemID)) {
         $item = JTable::getInstance('Itissues', 'IssuetrackerTable');
         $item->load($itemID);
      }

      $defaultValues = json_decode($customField->value);
      $required = NULL;
      $showNull = NULL;

      foreach ($defaultValues as $value) {
         // $required = isset($value->required) ? $value->required : 0;
         $showNull = isset($value->showNull) ? $value->showNull : 0;

         if ($customField->type == 'textfield' || $customField->type == 'date'){
            $active = $value->value;
         }  elseif ($customField->type == 'textarea') {
            $active[0] = $value->value;
            $active[1] = $value->editor;
            $active[2] = (int)$value->rows ? (int)$value->rows : 10;
            $active[3] = (int)$value->cols ? (int)$value->cols : 40;
           } else {
            $active = '';
         }
      }

      if (!isset($active)) {
         $active = '';
      }

      if (isset($item)) {
         $currentValues = json_decode($item->custom_fields);
         if (count($currentValues)) {
            foreach ($currentValues as $value) {
               if ($value->id == $customField->id) {
                  if ($customField->type == 'textarea') {
                     $active[0] = $value->value;
                  }  else if ($customField->type == 'date') {
                     $active = (is_array($value->value)) ? $value->value[0] : $value->value;
                  } else if ($customField->type == 'header') {
                     continue;
                  } else {
                     $active = $value->value;
                  }
               }
            }
         }
      }

      $attributes = '';
      if ($required) {
         $attributes .= 'class="required"';
      }

      if ($showNull && in_array($customField->type, array(
         'select',
         'multipleSelect'
      )))  {
         $nullOption = new stdClass;
         $nullOption->name = JText::_('COM_ISSUETRACKER_PLEASE_SELECT');
         $nullOption->value = '';
         array_unshift($defaultValues, $nullOption);
      }

      if (in_array($customField->type, array(
         'textfield',
         'date',
      ))) {
         $active = htmlspecialchars($active, ENT_QUOTES, 'UTF-8');
      }

      $jversion = new JVersion();

      $output = NULL;
      switch ($customField->type) {
         case 'textfield' :
            if ($displayonly) {
               // $output = ' <dd class="dl-horizontal">'.$active.'</dd>';
               $output = $active;
            } else {
               $output = '<input type="text" name="ITCustomField_'.$customField->id.'" id="ITCustomField_'.$customField->id.'" value="'.$active.'" '.$attributes.' />';
            }
            break;
         case 'textarea' :
            if ($active[1]) {
               if ($required) {
                  $attributes = 'class="itCustomFieldEditor required"';
               } else {
                  $attributes = 'class="itCustomFieldEditor"';
               }
            }
            if ($displayonly) {
               // $output = ' <dd class="dl-horizontal">'.htmlspecialchars($active[0], ENT_QUOTES, 'UTF-8').'</dd>';
               $output = htmlspecialchars($active[0], ENT_QUOTES, 'UTF-8');
            } else {
               $output = '<textarea name="ITCustomField_'.$customField->id.'" id="ITCustomField_'.$customField->id.'" rows="'.$active[2].'" cols="'.$active[3].'" '.$attributes.'>'.htmlspecialchars($active[0], ENT_QUOTES, 'UTF-8').'</textarea>';
            }
           break;
         case 'select' :
            $attributes .= ' id="ITCustomField_'.$customField->id.'"';
            $arrayAttributes = array();
            //   $arrayAttributes['id'] = 'ITCustomField_'.$customField->id;
            $attrs = version_compare(JVERSION, '3.2', 'ge') ? $arrayAttributes : $attributes;
            if ($displayonly) {
               if ( $active > 0 ) {
                  // $output = ' <dd class="dl-horizontal">'.$defaultValues[$active-1]->name.'</dd>';
                  // $output = $defaultValues[$active-1]->name;
                  if ( $showNull ) {
                     $output = $defaultValues[$active]->name;
                  } else {
                     $output = $defaultValues[$active-1]->name;
                  }
               } else {
                  // $output = ' <dd class="dl-horizontal">-</dd>';
                  $output = '-';
               }
            } else {
               $output = JHTML::_('select.genericlist', $defaultValues, 'ITCustomField_'.$customField->id, $attrs, 'value', 'name', $active);
            }
            break;
         case 'multipleSelect' :
            $attributes .= ' id="ITCustomField_'.$customField->id.'" multiple="multiple"';
            // $arrayAttributes['id'] = 'ITCustomField_'.$customField->id;
            $arrayAttributes['multiple'] = "multiple";
            $attrs = version_compare(JVERSION, '3.2', 'ge') ? $arrayAttributes : $attributes;
            if ($displayonly) {
               // $output = ' <dd class="dl-horizontal">'; // .$active.'</dd>';
               $output = null;
               if (empty($active)) {
                  $output .= '-';
               } else {
                  for ($i =0; $i < sizeof($active); $i++) {
                    if ( $showNull ) {
                        $j = $active[$i];
                     } else {
                        $j = $active[$i]-1;
                     }
                     $output .= $defaultValues[$j]->name.'<br/>';
                  }
               }
               // $output .= '</dd>';
            } else {
               $output = JHTML::_('select.genericlist', $defaultValues, 'ITCustomField_'.$customField->id.'[]', $attrs, 'value', 'name', $active);
            }
            break;
         case 'radio' :
            if (!$active && isset($defaultValues[0])) {
               $active = $defaultValues[0]->value;
            }
            if ($displayonly) {
               if ( $active > 0 ) {
                  // $output = ' <dd class="dl-horizontal">'.$defaultValues[$active-1]->name.'</dd>';
                  $output = $defaultValues[$active-1]->name;
               } else {
                  // $output = ' <dd class="dl-horizontal">-</dd>';
                  $output = '-';
               }
            } else {
               $output = JHTML::_('select.radiolist', $defaultValues, 'ITCustomField_'.$customField->id, $attributes, 'value', 'name', $active);
               // Possibly need for J3 only?
               if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
                  $output = str_replace('controls','control-group form-inline', $output);
               }
             }
            break;
         case 'multipleCheckbox' :
            // $attributes .= ' id="ITCustomField_'.$customField->id.'" multiple="multiple"';
            // $arrayAttributes['multiple'] = "multiple";
            // $attrs = version_compare(JVERSION, '3.2', 'ge') ? $arrayAttributes : $attributes;
            if ($displayonly) {
               $output = null;
               if (empty($active)) {
                  $output .= '-';
               } else {
                  for ($i =0; $i < sizeof($active); $i++) {
                    if ( $showNull ) {
                        $j = $active[$i];
                     } else {
                        $j = $active[$i]-1;
                     }
                     $output .= $defaultValues[$j]->name.'<br/>';
                  }
               }
            } else {
               // $output =JHTML::_('select.checkboxlist', $defaultValues, 'ITCustomField_'.$customField->id.'[]', '', 'value', 'name', $active);
               $fid  = $customField->id;
               $field_id = 'ITCustomField_'.$fid;
               $output  ='<div style="display: none;">';
               $output .="<script type='text/javascript'> function select_multilist_$fid(t,i) { var myselect = document.getElementById('$field_id'); var status = t.checked; myselect[i-1].selected=status; } </script>";
               $output .=JHTML::_('select.genericlist', $defaultValues, 'ITCustomField_'.$fid.'[]', 'multiple="multiple"', 'value', 'name',$active);
               $output.='</div>';
               if ( empty($active) ) $active = array();
               foreach ($defaultValues as $item) {
                  $checked = (in_array($item->value, $active)) ? 'checked' : '';
                  if ($checked) {
                     $output .= '<input type="checkbox" checked="checked" onclick="select_multilist_' . $fid . '(this,' . $item->value . ')" > ';
                  } else {
                     $output .= '<input type="checkbox" onclick="select_multilist_' . $fid . '(this,' . $item->value . ')" > ';
                  }
                  $output .= $item->name;
                  $output .= '<br>';
               }
            }
            break;
         case 'date' :
            if ($displayonly) {
               // $output = ' <dd class="dl-horizontal">'.$active.'</dd>';
               $output = $active;
            } else {
               $output = JHTML::_('calendar', $active, 'ITCustomField_'.$customField->id, 'ITCustomField_'.$customField->id, '%Y-%m-%d', $attributes);
            }
            break;
         case 'header' :
            $output = '';
            break;
      }
      return $output;
   }

   /**
    * Get the custom fields group name for any project.
    * @param $pid
    * @return mixed|string */
   function getCustomGroupName($pid)
   {
      if (empty($pid)) {
         return JText::_( 'COM_ISSUETRACKER_CUSTOMFIELDS' );
      }

      $db = JFactory::getDBO();
      $query  = 'SELECT name FROM `#__it_custom_field_group` AS cfg ';
      $query .= ' LEFT JOIN `#__it_projects` AS p ';
      $query .= ' ON p.customfieldsgroup = cfg.id ';
      $query .= ' WHERE p.id = '.$pid;
      $db->setQuery( $query );
      $gname = $db->loadResult();

      if (empty($gname) || $gname == '')
         $gname = JText::_( 'COM_ISSUETRACKER_CUSTOMFIELDS' );
      return $gname;
   }

   /**
    * getCustomFieldInfo - method to get the Custom Field information.
    * @param $fieldID
    * @return mixed
    */
   function getCustomFieldInfo($fieldID)
   {

      $db = JFactory::getDBO();
      $fieldID = (int)$fieldID;
      $query = "SELECT * FROM #__it_custom_field WHERE state=1 AND id = ".$fieldID;
      $db->setQuery($query, 0, 1);
      $row = $db->loadObject();
      return $row;
   }

   /**
    * @param $id
    * @param $currentValue
    * @return string
    */
   function getSearchValue($id, $currentValue)
   {
      $row = self::getTable();
      $row->load($id);

      $jsonObject = json_decode($row->value);

      $value = '';
      if ($row->type == 'textfield' || $row->type == 'textarea') {
         $value = $currentValue;
      } else if ($row->type == 'multipleSelect') {
         foreach ($jsonObject as $option)  {
            if (in_array($option->value, $currentValue))
               $value .= $option->name.' ';
         }
      } else {
         foreach ($jsonObject as $option) {
            if ($option->value == $currentValue)
               $value .= $option->name;
         }
      }
      return $value;
   }
}