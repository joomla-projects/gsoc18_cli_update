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
        if(task == 'jchange.cancel'){
            Joomla.submitform(task, document.getElementById('jchange-form'));
        }
        else {
            if (task != 'jchange.cancel' && document.formvalidator.isValid(document.id('jchange-form'))) {
                Joomla.submitform(task, document.getElementById('jchange-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="jchange-form" class="form-validate">
   <div class="width-100 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS'); ?></legend>
         <ul class="adminformlist">

            <li><?php echo $this->form->getLabel('table_name'); ?>
            <?php echo $this->form->getInput('table_name'); ?></li>

            <li><?php echo $this->form->getLabel('component'); ?>
            <?php echo $this->form->getInput('component'); ?></li>

            <li><?php echo $this->form->getLabel('column_name'); ?>
            <?php echo $this->form->getInput('column_name'); ?></li>

            <li><?php echo $this->form->getLabel('column_type'); ?>
            <?php echo $this->form->getInput('column_type'); ?></li>

            <li><?php echo $this->form->getLabel('row_key'); ?>
            <?php echo $this->form->getInput('row_key'); ?></li>

            <li><?php echo $this->form->getLabel('row_key_link'); ?>
            <?php echo $this->form->getInput('row_key_link'); ?></li>

            <li><?php echo $this->form->getLabel('action'); ?>
            <?php echo $this->form->getInput('action'); ?></li>

            <li><?php echo $this->form->getLabel('id'); ?>
            <?php echo $this->form->getInput('id'); ?></li>

            <li><?php echo $this->form->getLabel('old_value'); ?>
            <?php echo $this->form->getInput('old_value'); ?></li>

            <li><?php echo $this->form->getLabel('new_value'); ?>
            <?php echo $this->form->getInput('new_value'); ?></li>

         </ul>
            <div class="clr"></div>
         <ul>
            <li><?php echo $this->form->getLabel('change_by'); ?>
            <?php echo $this->form->getInput('change_by'); ?></li>

            <li><?php echo $this->form->getLabel('change_date'); ?>
            <?php echo $this->form->getInput('change_date'); ?></li>
         </ul>
      </fieldset>
      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>