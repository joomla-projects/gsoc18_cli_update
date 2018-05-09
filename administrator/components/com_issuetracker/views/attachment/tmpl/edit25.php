<?php
/*
 *
 * @Version       $Id: edit25.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.3.0
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
// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet(JURI::root(true).'/media/com_issuetracker/css/administrator.css');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'attachment.cancel' || document.formvalidator.isValid(document.id('attachment-form'))) {
         Joomla.submitform(task, document.getElementById('attachment-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="attachment-form" class="form-validate" enctype="multipart/form-data">
   <div class="width-60 fltlft">
      <fieldset class="adminform">
         <legend><?php echo JText::_('COM_ISSUETRACKER_LEGEND_ATTACHMENT'); ?></legend>
         <ul class="adminformlist">

         <!--
         <li><?php echo $this->form->getLabel('id'); ?>
         <?php echo $this->form->getInput('id'); ?></li>
         -->

         <li><?php echo $this->form->getLabel('issue_id'); ?>
         <?php echo $this->form->getInput('issue_id'); ?></li>

         <li><?php echo $this->form->getLabel('title'); ?>
         <?php echo $this->form->getInput('title'); ?></li>

         <li><?php echo $this->form->getLabel('description'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('description'); ?></li>

         <?php if ( $this->state->params->get('show_upload_req','0') ) : ?>
            <br /><br /><br />
            <div class="clr"></div>
            <?php echo $this->loadTemplate('attachment'); ?>
            <div class="clr"></div>
         <?php endif; ?>

         <li><?php echo $this->form->getLabel('state'); ?>
         <?php echo $this->form->getInput('state'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out'); ?>
         <?php echo $this->form->getInput('checked_out'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out_time'); ?>
         <?php echo $this->form->getInput('checked_out_time'); ?></li>

         <li><?php echo $this->form->getLabel('hashname'); ?>
         <?php echo $this->form->getInput('hashname'); ?></li>

         <li><?php echo $this->form->getLabel('size'); ?>
         <?php echo $this->form->getInput('size'); ?></li>

         <li><?php echo $this->form->getLabel('filename'); ?>
         <?php echo $this->form->getInput('filename'); ?></li>

         <li><?php echo $this->form->getLabel('filepath'); ?>
         <?php echo $this->form->getInput('filepath'); ?></li>

         <li><?php echo $this->form->getLabel('filetype'); ?>
         <?php echo $this->form->getInput('filetype'); ?></li>

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