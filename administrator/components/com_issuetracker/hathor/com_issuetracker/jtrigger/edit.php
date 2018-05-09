<?php
/*
 *
 * @Version       $Id: edit.php 1292 2014-01-12 18:57:04Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2013 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2014-01-12 18:57:04 +0000 (Sun, 12 Jan 2014) $
 *
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){

    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'jtrigger.cancel'){
           Joomla.submitform(task, document.getElementById('jtrigger-form'));
        } else {
           if (task != 'jtrigger.cancel' && document.formvalidator.isValid(document.id('jtrigger-form'))) {
              Joomla.submitform(task, document.getElementById('jtrigger-form'));
           } else {
              alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
           }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="jtrigger-form" class="form-validate">
   <div class="width-100 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS'); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('id'); ?>
         <?php echo $this->form->getInput('id'); ?></li>

         <li><?php echo $this->form->getLabel('table_name'); ?>
         <?php echo $this->form->getInput('table_name'); ?></li>

         <li><?php echo $this->form->getLabel('trigger_name'); ?>
         <?php echo $this->form->getInput('trigger_name'); ?></li>

         <li><?php echo $this->form->getLabel('trigger_type'); ?>
         <?php echo $this->form->getInput('trigger_type'); ?></li>

         <li><?php echo $this->form->getLabel('trigger_event'); ?>
         <?php echo $this->form->getInput('trigger_event'); ?></li>

         <?php if (empty($this->item->id)) echo JText::_('COM_ISSUETRACKER_COLUMNS_MSG').'<br />'; ?>

         <li><?php echo $this->form->getLabel('columns'); ?>
         <?php echo $this->form->getInput('columns'); ?></li>

         <li><?php echo $this->form->getLabel('applied'); ?>
         <?php echo $this->form->getInput('applied'); ?></li>

         <li><?php echo $this->form->getLabel('action_orientation'); ?>
         <?php echo $this->form->getInput('action_orientation'); ?></li>

         <li><?php echo $this->form->getLabel('trigger_text'); ?>
         <?php echo $this->form->getInput('trigger_text'); ?></li>

         <li><?php echo $this->form->getLabel('created_by'); ?>
         <?php echo $this->form->getInput('created_by'); ?></li>

         <li><?php echo $this->form->getLabel('created_by_alias'); ?>
         <?php echo $this->form->getInput('created_by_alias'); ?></li>

         <li><?php echo $this->form->getLabel('created_on'); ?>
         <?php echo $this->form->getInput('created_on'); ?></li>

         </ul>

      </fieldset>

      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>