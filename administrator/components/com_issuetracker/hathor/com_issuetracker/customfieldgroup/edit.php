<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
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
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/media/com_issuetracker/css/administrator.css');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'customfieldgroup.cancel' || document.formvalidator.isValid(document.id('customfieldgroup-form'))) {
         Joomla.submitform(task, document.getElementById('customfieldgroup-form'));
      }
      else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="customfieldgroup-form" class="form-validate">
   <div class="width-60 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('COM_ISSUETRACKER_LEGEND_CUSTOMFIELDGROUP'); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('id'); ?>
         <?php echo $this->form->getInput('id'); ?></li>

         <li><?php echo $this->form->getLabel('name'); ?>
         <?php echo $this->form->getInput('name'); ?></li>

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

   <input type="hidden" name="task" value="" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>

   <style type="text/css">
     /* Temporary fix for drifting editor fields */
    .adminformlist li {
        clear: both;
    }
   </style>
</form>
