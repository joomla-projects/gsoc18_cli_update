<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access' );

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'itprojects.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      }
      else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="width-60 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS'); ?></legend>
         <ul class="adminformlist">

            <li><?php echo $this->form->getLabel('title'); ?>
            <?php echo $this->form->getInput('title'); ?></li>

            <li><?php echo $this->form->getLabel('alias'); ?>
            <?php echo $this->form->getInput('alias'); ?></li>

            <li><?php echo $this->form->getLabel('parent_id'); ?>
            <?php echo $this->form->getInput('parent_id'); ?></li>

            <li><?php echo $this->form->getLabel('access'); ?>
            <?php echo $this->form->getInput('access'); ?></li>

            <li><?php echo $this->form->getLabel('description'); ?>
            <div class="clr"></div>
            <?php echo $this->form->getInput('description'); ?></li>

            <li><?php echo $this->form->getLabel('assignee'); ?>
            <?php echo $this->form->getInput('assignee'); ?></li>

            <li><?php echo $this->form->getLabel('start_date'); ?>
            <?php echo $this->form->getInput('start_date'); ?></li>

            <li><?php echo $this->form->getLabel('target_end_date'); ?>
            <?php echo $this->form->getInput('target_end_date'); ?></li>

            <li><?php echo $this->form->getLabel('actual_end_date'); ?>
            <?php echo $this->form->getInput('actual_end_date'); ?></li>

            <li><?php echo $this->form->getLabel('state'); ?>
            <?php echo $this->form->getInput('state'); ?></li>

         </ul>
            <div class="clr"></div>
         <ul>
            <li><?php echo $this->form->getLabel('tags'); ?>
            <?php echo $this->form->getInput('tags'); ?></li>

            <li><?php echo $this->form->getLabel('checked_out'); ?>
            <?php echo $this->form->getInput('checked_out'); ?></li>

            <li><?php echo $this->form->getLabel('checked_out_time'); ?>
            <?php echo $this->form->getInput('checked_out_time'); ?></li>

         </ul>
      </fieldset>
   </div>

   <div class="width-40 fltlft">
      <?php echo $this->loadTemplate('audit_details');?>
   </div>
   <div class="clr"></div>

   <!-- begin ACL definition-->
   <?php if ($this->canDo->get('core.admin')): ?>
      <div  class="width-100 fltlft">
         <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

         <?php echo JHtml::_('sliders.panel', JText::_('COM_ISSUETRACKER_PROJECT_FIELDSET_RULES'), 'access-rules'); ?>
            <fieldset class="panelform">
               <?php echo $this->form->getLabel('rules'); ?>
               <?php echo $this->form->getInput('rules'); ?>
            </fieldset>
         <?php echo JHtml::_('sliders.end'); ?>
      </div>
   <?php endif; ?>

   <!-- end ACL definition-->

   <input type="hidden" name="task" value="" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>
</form>
