<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.5.0
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

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
   <div class="row-fluid">
      <div class="span10 form-horizontal">
         <fieldset class="adminform">

            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('table_name'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('table_name'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('trigger_name'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('trigger_name'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('trigger_type'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('trigger_type'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('trigger_event'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('trigger_event'); ?></div>
            </div>

             <?php if (empty($this->item->id)) echo JText::_('COM_ISSUETRACKER_COLUMNS_MSG').'<br />'; ?>

             <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('columns'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('columns'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('applied'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('applied'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('action_orientation'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('action_orientation'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('trigger_text'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('trigger_text'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('created_on'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('created_on'); ?></div>
            </div>

         </fieldset>
      </div>

      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>

      <!-- Begin Sidebar -->
      <!-- ?php echo JLayoutHelper::render('joomla.edit.details', $this); ? -->
      <!-- End Sidebar -->

   </div>
</form>