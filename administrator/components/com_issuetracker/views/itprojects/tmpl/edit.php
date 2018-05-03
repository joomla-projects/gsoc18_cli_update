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
defined('_JEXEC') or die('Restricted access' );

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'itprojects.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="row-fluid">
      <div class="span12 form-horizontal">
      <?php echo JHtml::_('bootstrap.startTabSet', 'projectTab', array('active' => 'general')); ?>
         <?php echo JHtml::_('bootstrap.addTab', 'projectTab', 'general', JText::_('COM_ISSUETRACKER_PROJECT_DEFAULT_LEGEND', true)); ?>
            <div class="span9">
               <div class="row-fluid form-horizontal">
                  <div class="span6">
                     <?php echo $this->form->renderField('title'); ?>
                     <!-- ?php echo $this->form->renderField('parent_id'); ? -->
                  </div>
                  <div class="span6">
                     <?php echo $this->form->renderField('assignee'); ?>
                     <?php echo $this->form->renderField('customfieldsgroup'); ?>
                     <?php echo $this->form->renderField('itypes'); ?>
                  </div>
               </div>
               <?php echo $this->form->renderField('description'); ?>
            </div>
            <div class="span3 form-vertical">
               <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'projectTab', 'dates', JText::_('COM_ISSUETRACKER_PROJECT_DATES', true)); ?>
            <div class="row-fluid">
               <div class="span6">
                  <?php echo $this->form->renderField('alias'); ?>
                  <?php echo $this->form->renderField('id'); ?>
               </div>

               <div class="span6">
                  <?php echo $this->form->renderField('start_date'); ?>
                  <?php echo $this->form->renderField('target_end_date'); ?>
                  <?php echo $this->form->renderField('actual_end_date'); ?>
               </div>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'projectTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
            <div class="row-fluid form-horizontal">
               <?php echo $this->loadTemplate('audit_details'); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php if ($this->canDo->get('core.admin')) : ?>
            <?php echo JHtml::_('bootstrap.addTab', 'projectTab', 'permissions', JText::_('COM_ISSUETRACKER_FIELDSET_RULES', true)); ?>
               <fieldset>
                  <?php echo $this->form->getInput('rules'); ?>
               </fieldset>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
         <?php endif; ?>
      <?php echo JHtml::_('bootstrap.endTabSet'); ?>
      </div>
   </div>
   <input type="hidden" name="task" value="" />
   <?php echo JHtml::_('form.token'); ?>
</form>