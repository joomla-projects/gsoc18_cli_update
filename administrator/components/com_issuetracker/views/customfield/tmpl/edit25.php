<?php
/*
 *
 * @Version       $Id: edit25.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$app = JFactory::getApplication();
$input = $app->input;

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/media/com_issuetracker/css/issuetracker.css');

$version = new JVersion();
if (version_compare( $version->RELEASE, '2.5', '<=')) {
   if(JFactory::getApplication()->get('jquery') !== true) {
       $jquery_cdn = $this->params->get( 'jquery_cdn_link', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
       $document->addScript($jquery_cdn);
       // $doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
       // $doc->addScriptDeclaration('jQuery.noConflict()');
       JFactory::getApplication()->set('jquery', true);
    }
} else {
    JHtml::_('jquery.framework');
}

$document->addScript(JURI::root(true).'/media/com_issuetracker/js/issuetracker_cf_min.js');

?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'customfield.cancel') {
         Joomla.submitform(task, document.getElementById('customfield-form'));
         return;
      }
      if ( document.getElementById('jformtype').value == '0' ) {
        alert('<?php echo $this->escape(JText::_('COM_ISSUETRACKER_PLEASE_SUPPLY_THE_TYPE_OF_THE_CUSTOM_FIELD', true));?>' );
      }
      else if ( document.getElementById('jform_name').value == '' ) {
         alert( '<?php echo $this->escape(JText::_('COM_ISSUETRACKER_PLEASE_SUPPLY_A_VALID_FIELD_NAME', true));?>' );
      }
      else if ( document.getElementById('jformgroup').value == '0' ) {
         alert( '<?php echo $this->escape(JText::_('COM_ISSUETRACKER_PLEASE_SUPPLY_THE_GROUP_NAME', true));?>' );
      }
      else if ( document.formvalidator.isValid(document.id('customfield-form'))) {
         Joomla.submitform(task, document.getElementById('customfield-form'));
      }
      else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="customfield-form" class="form-validate">
   <div class="width-60 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('COM_ISSUETRACKER_LEGEND_CUSTOMFIELD'); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('name'); ?>
         <?php echo $this->form->getInput('name'); ?></li>

         <li><?php echo $this->form->getLabel('alias'); ?>
         <?php echo $this->form->getInput('alias'); ?></li>

         <li><?php echo $this->form->getLabel('group'); ?>
         <?php echo $this->form->getInput('group'); ?></li>

         <li><?php echo $this->form->getLabel('type'); ?>
         <?php echo $this->form->getInput('type'); ?>
         </li>

         <li><?php echo $this->form->getLabel('access'); ?>
         <?php echo $this->form->getInput('access'); ?></li>

         <li><?php echo $this->form->getLabel('state'); ?>
         <?php echo $this->form->getInput('state'); ?></li>

         <div id="req_field">
         <li><?php echo $this->form->getLabel('required'); ?>
         <?php echo $this->form->getInput('required'); ?></li>
         </div>

         <div id="shownull_field">
         <li><?php echo $this->form->getLabel('shownullflag'); ?>
         <?php echo $this->form->getInput('shownullflag'); ?></li>
         </div>

         <div id="displayfe_field">
         <li><?php echo $this->form->getLabel('displayinfe'); ?>
         <?php echo $this->form->getInput('displayinfe'); ?></li>
         </div>

         <li><?php echo $this->form->getLabel('checked_out'); ?>
         <?php echo $this->form->getInput('checked_out'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out_time'); ?>
         <?php echo $this->form->getInput('checked_out_time'); ?></li>

         <div id="defaultval_field">
         <li><?php echo $this->form->getLabel('default_values'); ?>
         <?php echo $this->form->getInput('default_values'); ?></li>
         </div>
         <div id="defaultval_field_text">
         <li><?php echo $this->form->getLabel('default_values_text'); ?>
         <?php echo $this->form->getInput('default_values_text'); ?></li>
         </div>
         <div id="defaultval_field_textarea">
         <li><?php echo $this->form->getLabel('default_values_textarea'); ?>
         <?php echo $this->form->getInput('default_values_textarea'); ?></li>
         </div>
         <div id="defaultval_field_date">
         <li><?php echo $this->form->getLabel('default_values_date'); ?>
         <?php echo $this->form->getInput('default_values_date'); ?></li>
         </div>

         <div id="textarea_fields">
            <li><?php echo $this->form->getLabel('textarea_rows'); ?>
            <?php echo $this->form->getInput('textarea_rows'); ?></li>

            <li><?php echo $this->form->getLabel('textarea_cols'); ?>
            <?php echo $this->form->getInput('textarea_cols'); ?></li>

            <li><?php echo $this->form->getLabel('textarea_editor'); ?>
            <?php echo $this->form->getInput('textarea_editor'); ?></li>
         </div>

         <div id="valid_field">
         <li><?php echo $this->form->getLabel('validation'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('validation'); ?></li>
         </div>
         <br />

         <div id="tooltip_field">
         <li><?php echo $this->form->getLabel('tooltip'); ?>
         <div class="clr"></div><?php echo $this->form->getInput('tooltip'); ?></li>
         </div>
         <br />

         <div id="defaultval_field_other">
         <li><?php echo $this->form->getLabel('default_values'); ?>
         <div style="display: inline-block" id="CustomFieldsTypesDiv"></div>
         </li>
         </div>

         </ul>
      </fieldset>
   </div>

   <div class="width-40 fltlft">
      <?php echo $this->loadTemplate('audit_details');?>
   </div>

   <input type="hidden" name="task" value="" />
   <input type="hidden" id="id" name="id" value="<?php echo $this->item->id; ?>" />
   <input type="hidden" id="value" name="value" value="<?php echo htmlentities($this->item->value); ?>" />
   <input type="hidden" name="isNew" id="isNew" value="<?php echo ($this->item->group)?'0':'1'; ?>" />
   <input type="hidden" id="view" name="view" value="<?php echo $input->get('view'); ?>" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>

   <style type="text/css">
     /* Temporary fix for drifting editor fields */
     .adminformlist li {
     clear: both;
     }
   </style>
</form>