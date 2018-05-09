<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.6
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access' );

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'itpriority.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="row-fluid">
      <div class="span12 form-horizontal">
         <?php echo JHtml::_('bootstrap.startTabSet', 'priorityTab', array('active' => 'general')); ?>
            <?php echo JHtml::_('bootstrap.addTab', 'priorityTab', 'general', JText::_('COM_ISSUETRACKER_FIELD_PRIORITY_LABEL', true)); ?>
               <div class="span9">
                  <div class="row-fluid form-horizontal">
                     <div class="span6">
                        <?php echo $this->form->renderField('priority_name'); ?>
                        <?php echo $this->form->renderField('ranking'); ?>
                     </div>
                     <div class="span6">
                        <?php echo $this->form->renderField('response_time'); ?>
                        <?php echo $this->form->renderField('resolution_time'); ?>
                     </div>
                  </div>

                  <div class="row-fluid form-horizontal">
                     <?php echo $this->form->renderField('description'); ?>
                  </div>
               </div>
               <div class="span3 form-vertical">
                  <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
               </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'priorityTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
               <div class="row-fluid form-horizontal">
                  <?php echo $this->loadTemplate('audit_details'); ?>
               </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
         <?php echo JHtml::_('bootstrap.endTabSet'); ?>

         <input type="hidden" name="task" value="" />
         <?php echo JHtml::_('form.token'); ?>
      </div>
   </div>
</form>