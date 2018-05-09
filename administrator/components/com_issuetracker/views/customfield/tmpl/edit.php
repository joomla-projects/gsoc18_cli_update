<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.5
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/media/com_issuetracker/css/issuetracker.css');
$document->addScript(JURI::root(true).'/media/com_issuetracker/js/issuetracker_cf_min.js');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'customfield.cancel') {
         Joomla.submitform(task, document.getElementById('customfield-form'));
         return;
      }
      if ( document.getElementById('jformtype').value == '0' ) {
        alert('<?php echo $this->escape(JText::_('COM_ISSUETRACKER_PLEASE_SUPPLY_THE_TYPE_OF_THE_CUSTOM_FIELD', true));?>' );
      } else if ( document.getElementById('jform_name').value == '' ) {
         alert( '<?php echo $this->escape(JText::_('COM_ISSUETRACKER_PLEASE_SUPPLY_A_VALID_FIELD_NAME', true));?>' );
      } else if ( document.getElementById('jformgroup').value == '0' ) {
         alert( '<?php echo $this->escape(JText::_('COM_ISSUETRACKER_PLEASE_SUPPLY_THE_GROUP_NAME', true));?>' );
      } else if ( document.formvalidator.isValid(document.id('customfield-form'))) {
         Joomla.submitform(task, document.getElementById('customfield-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="customfield-form" class="form-validate">
   <div class="span12 row-fluid form-horizontal">
      <?php echo JHtml::_('bootstrap.startTabSet', 'issueTab', array('active' => 'general')); ?>
         <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'general', JText::_('COM_ISSUETRACKER_LEGEND_CUSTOMFIELD', true)); ?>
            <div class="span9 row-fluid form-horizontal">
               <?php echo $this->form->renderField('name'); ?>
               <?php echo $this->form->renderField('group'); ?>
               <?php echo $this->form->renderField('type'); ?>

               <div class="control-group form-inline" id="req_field">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('required'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('required'); ?>
                  </div>
               </div>

               <div class="control-group form-inline" id="shownull_field">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('shownullflag'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('shownullflag'); ?>
                  </div>
               </div>

               <div class="control-group form-inline" id="displayfe_field">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('displayinfe'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('displayinfe'); ?>
                  </div>
               </div>

               <div class="control-group form-inline" id="defaultval_field">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('default_values'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('default_values'); ?>
                  </div>
               </div>
               <div class="control-group form-inline" id="defaultval_field_text">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('default_values_text'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('default_values_text'); ?>
                  </div>
               </div>
               <div class="control-group form-inline" id="defaultval_field_textarea">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('default_values_textarea'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('default_values_textarea'); ?>
                  </div>
               </div>
               <div class="control-group form-inline" id="defaultval_field_date">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('default_values_date'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('default_values_date'); ?>
                  </div>
               </div>

               <div id="textarea_fields">
                  <div class="control-group form-inline">
                     <div class="control-label">
                        <?php echo $this->form->getLabel('textarea_rows'); ?>
                     </div>
                     <div class="controls">
                        <?php echo $this->form->getInput('textarea_rows'); ?>
                     </div>
                  </div>

                  <div class="control-group form-inline">
                     <div class="control-label">
                        <?php echo $this->form->getLabel('textarea_cols'); ?>
                     </div>
                     <div class="controls">
                        <?php echo $this->form->getInput('textarea_cols'); ?>
                     </div>
                  </div>

                  <div class="control-group form-inline">
                     <div class="control-label">
                        <?php echo $this->form->getLabel('textarea_editor'); ?>
                     </div>
                     <div class="controls">
                        <?php echo $this->form->getInput('textarea_editor'); ?>
                     </div>
                  </div>
               </div>

               <div class="control-group form-inline" id="valid_field">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('validation'); ?>
                  </div>
                  <div class="controls">
                     <?php echo $this->form->getInput('validation'); ?>
                  </div>
               </div>

               <div class="control-group form-inline" id="tooltip_field">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('tooltip'); ?>
                   </div>
                   <div class="controls">
                      <?php echo $this->form->getInput('tooltip'); ?>
                  </div>
               </div>

               <div class="control-group form-inline" id="defaultval_field_other">
                  <div class="control-label">
                     <?php echo $this->form->getLabel('default_values'); ?>
                   </div>
                   <div class="controls">
                      <div id="CustomFieldsTypesDiv"></div>
                  </div>
               </div>

            </div>
            <div class="span3 form-vertical">
               <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
            <div class="row-fluid form-horizontal">
               <?php echo $this->loadTemplate('audit_details'); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.endTabSet'); ?>

      <input type="hidden" name="task" value="" />
      <input type="hidden" id="id" name="id" value="<?php echo $this->item->id; ?>" />
      <input type="hidden" id="value" name="value" value="<?php echo htmlentities($this->item->value); ?>" />
      <input type="hidden" name="isNew" id="isNew" value="<?php echo ($this->item->group)?'0':'1'; ?>" />
      <input type="hidden" id="view" name="view" value="<?php echo $input->get('view'); ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>