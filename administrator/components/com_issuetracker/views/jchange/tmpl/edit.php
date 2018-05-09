<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted Access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');

// Get the dates in local timezone. Need to add the input tags manually else JHtml complians with the message
// Notice: Object of class JDate could not be converted to int in /share/MD0_DATA/Web/DEV/libraries/joomla/html/html.php on line 901.
if ( $this->item->change_date == '0000-00-00 00:00:00' || empty($this->item->change_date) || is_null($this->item->change_date) ) {
   $d0 = "";
} else {
   $d0 = IssueTrackerHelperDate::getDate($this->item->change_date);
}

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
   <div class="row-fluid">
      <div class="span10 form-horizontal">
         <fieldset class="adminform">
            <div class="span6">
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('table_name'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('table_name'); ?></div>
               </div>
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('component'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('component'); ?></div>
               </div>
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('column_name'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('column_name'); ?></div>
               </div>
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('column_type'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('column_type'); ?></div>
               </div>
            </div>

            <div class="span6">
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('row_key'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('row_key'); ?></div>
               </div>
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('row_key_link'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('row_key_link'); ?></div>
               </div>
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('action'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('action'); ?></div>
               </div>
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
               </div>
           </div>

            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('old_value'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('old_value'); ?></div>
            </div>
            <div class="control-group">
               <div class="control-label"><?php echo $this->form->getLabel('new_value'); ?></div>
               <div class="controls"><?php echo $this->form->getInput('new_value'); ?></div>
            </div>

            <div class="span6">
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('change_by'); ?></div>
                  <div class="controls"><?php echo $this->form->getInput('change_by'); ?></div>
               </div>
            </div>
            <div class="span6">
               <div class="control-group">
                  <div class="control-label"><?php echo $this->form->getLabel('change_date'); ?></div>
                  <div class="controls">
                     <input type="text" name="jform[change_date]" id="jform_change_date" value="<?php echo $d0; ?>" size="40" disabled="disabled" readonly="readonly"/>
                     <!-- ?php echo $this->form->getInput('change_date'); ? -->
                  </div>
               </div>
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