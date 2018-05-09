<?php
/*
 *
 * @Version       $Id: itissues.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.6
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Issue Tracker Controller
 *
 * @package       Joomla.Components
 * @subpackage    Issue Tracker
 */
JLoader::import('joomla.application.component.controllerform');

/**
 * Issue Tracker controller class.
 */
class IssueTrackerControllerItissues extends JControllerForm
{
   /**
    *
    */
   function __construct() {
        $this->view_list = 'itissueslist';
        parent::__construct();
    }

   /**
    * Function that allows child controller access to model data
    * after the item has been deleted.
    *
    * @param   JModelLegacy  $model  The data model object.
    * @param   integer       $ids    The array of ids for items being deleted.
    *
    * @return  void
    *
    * @since   12.2
    */
   protected function postDeleteHook(JModelLegacy $model, $ids = null)
   {
   }

   /**
    * Method to return the custom fields for a project to the Ajax call.
    *
    *
    */
   function customFields()
   {
      $app        = JFactory::getApplication();
      $language   = JFactory::getLanguage();
      $language->load('com_issuetracker', JPATH_ADMINISTRATOR);
      $itemID     = $app->input->get('id', NULL);


      $params        = JComponentHelper::getParams('com_issuetracker');
      $def_project   = $params->get('def_project', '10');

      JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
      $pid     = $app->input->get('pid');
      if ( empty($pid) || $pid == 0 ) $pid = $def_project;

      $project  = JTable::getInstance('itprojects', 'IssuetrackerTable');
      $project->load($pid);

      require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'customfield.php');
      $cfModel = new IssuetrackerModelCustomField;

      $pstate = 1; // Back end so no need to restrict published state
      $astate = 0; // Back end so no need to control via access groups.
      $customFields = $cfModel->getCustomFieldByGroup($project->customfieldsgroup, $pstate, $astate);

      $displayopt = 0;
      $output = '<div id="customFields">';
      $counter = 0;

      $jversion = new JVersion();

      if (count($customFields)) {
         if( version_compare( $jversion->getShortVersion(), '3.1', 'ge' ) ) {
            foreach ($customFields as $extraField) {

               $output .= '<div class="control-group form-inline">';
               if ($extraField->type == 'header') {
                  $output .= '<div class="control-label"><h4 class="ITCustomFieldHeader">'.$extraField->name.'</h4></div>';
                  $output .= '<div class="clearfix"></div>';
               } else {
                  // Determine whether we are a required field.
                  $required = false;
                  $defs = json_decode($extraField->value);
                  foreach ($defs as $val) {
                     if (isset($val->required) && $val->required == 1) {
                        $required = true;
                     }
                     break;
                  }

                  if (!empty($extraField->tooltip)) {
                     if ($required) {
                        $attributes = 'class="hasTooltip required"';
                     } else {
                        $attributes = 'class="hasTooltip"';
                     }
                     $output .= '<div class="control-label"><label data-original-title="<strong>'.$extraField->name.'</strong><br />'.$extraField->tooltip.'" for="CustomField_'.$extraField->id.'" id="ITCustomField_'.$extraField->id.'-id" '.$attributes.' title="'.$extraField->tooltip.'">'.$extraField->name;
                  } else {
                     if ($required) {
                        $attributes = 'class="required"';
                     } else {
                        $attributes = '';
                     }
                     $output .= '<div class="control-label"><label for="CustomField_'.$extraField->id.'" id="ITCustomField_'.$extraField->id.'-id" '.$attributes.' >'.$extraField->name;
                  }

                  if ( $required )
                     $output .= '<span class="star">&#160;*</span>';
                  $output .= '</label></div>';
                  if ($extraField->type == 'radio') {  // Vertically align the radio buttons.
                     $output .= '<div class="controls" style="display: block; width: 150px; ">'.$cfModel->renderCustomField($extraField, $itemID, $displayopt).'</div>';
                  } else {
                     $output .= '<div class="controls">'.$cfModel->renderCustomField($extraField, $itemID, $displayopt).'</div>';
                  }
               }
               $output .= '</div>';
               $counter++;
            }
            $output .= '</div>';
         } else {
            // Joomla 2.5
            $output = '<ul>';
            foreach ($customFields as $extraField) {
               if ($extraField->type == 'header') {
                  $output .= '<li><h4 class="CustomFieldHeader">'.$extraField->name.'</h4></li>';
                  $output .= '<div class="clr"></div>';
               } else {
                  // Determine whether we are a required field.
                  $required = false;
                  $defs = json_decode($extraField->value);
                  foreach ($defs as $val) {
                     if (isset($val->required) && $val->required == 1) {
                        $required = true;
                     }
                     break;
                  }

                  if (!empty($extraField->tooltip)) {
                     if ($required) {
                        $attributes = 'class="hasTooltip required"';
                     } else {
                        $attributes = 'class="hasTooltip"';
                     }
                     $output .= '<li><label data-original-title="<strong>'.$extraField->name.'</strong><br />'.$extraField->tooltip.'" for="CustomField_'.$extraField->id.'" id="ITCustomField_'.$extraField->id.'-id" '.$attributes.' title="'.$extraField->tooltip.'">'.$extraField->name;
                  } else {
                     if ($required) {
                        $attributes = 'class="required"';
                     } else {
                        $attributes = '';
                     }
                     $output .= '<li><label for="CustomField_'.$extraField->id.'" id="ITCustomField_'.$extraField->id.'-id" '.$attributes.' >'.$extraField->name;
                  }

                  if ( $required )
                     $output .= '<span class="star">&#160;*</span>';

                  $output .= '</label>';
                  if ($extraField->type == 'radio') {  // Need to add an extra carriage return for radio buttons.
                     // Was align left typo?
                     $output .= '<fieldset class="radio" style="vertical-align: middle">'.str_replace('</label>','</label><br />',$cfModel->renderCustomField($extraField, $itemID, $displayopt)).'</fieldset>';
                     // $output .= str_replace('</label>','</label><br />',$cfModel->renderCustomField($extraField, $itemID, $displayopt));
                  } else {
                     $output .= $cfModel->renderCustomField($extraField, $itemID, $displayopt);
                  }
                  $output .= '</li>';
                  $output .= '<div class="clr"></div>';
               }
               $counter++;
            }
            $output .= '</ul>';
         }
      }

      if ($counter == 0)
         $output = NULL;
         // $output = JText::_('COM_ISSUETRACKER_THIS_PROJECT_DOESNT_HAVE_ASSIGNED_CUSTOM_FIELDS');

      echo $output;
      $app->close();
   }
}