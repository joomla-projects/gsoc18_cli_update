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
defined('_JEXEC') or die('Restricted access' );

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

// Create shortcut to parameters.
$parameters = $this->state->get('params');
$allow_private    = $parameters->get('allow_private_issues');

$app = JFactory::getApplication();
$input = $app->input;

// Save where we are called from.
// Get the calling page address.
$return_page = $_SERVER['HTTP_REFERER'];
$encoded_page = base64_encode($return_page);

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task)
   {
      if (task == 'paction.cancel' || document.formvalidator.isValid(document.id('paction-form'))) {
         Joomla.submitform(task, document.getElementById('paction-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="paction-form" class="form-validate" enctype="multipart/form-data">
   <div class="row-fluid">
      <div class="span10 form-horizontal">
         <ul class="nav nav-tabs">
         <?php echo JHtml::_('bootstrap.startTabSet', 'progressTab', array('active' => 'general')); ?>

             <?php echo JHtml::_('bootstrap.addTab', 'progressTab', 'general', JText::_('COM_ISSUETRACKER_LEGEND_PROGRESS', true)); ?>
               <fieldset class="adminform">
                  <div class="control-group form-inline">
                     <div class="control-label">
                        <?php echo $this->form->getLabel('issue_id'); ?>
                     </div>
                     <div class="controls">
                       <?php echo $this->form->getInput('issue_id'); ?>
                     </div>
                  </div>
                  <div class="control-group form-inline">
                      <div class="control-label">
                         <?php echo $this->form->getLabel('alias'); ?>
                      </div>
                      <div class="controls">
                        <?php echo $this->form->getInput('alias'); ?>
                     </div>
                  </div>
                  <div class="control-group form-inline">
                     <div class="control-label">
                        <?php echo $this->form->getLabel('lineno'); ?>
                     </div>
                     <div class="controls">
                        <?php echo $this->form->getInput('lineno'); ?>
                     </div>
                  </div>
                  <div class="control-group form-inline">
                     <div class="control-label">
                        <?php echo $this->form->getLabel('public'); ?>
                     </div>
                     <div class="controls">
                        <?php echo $this->form->getInput('public'); ?>
                     </div>
                  </div>
                  <div class="control-group form-inline">
                     <div class="control-label">
                        <?php echo $this->form->getLabel('progress'); ?>
                     </div>
                     <div class="controls">
                        <?php echo $this->form->getInput('progress'); ?>
                     </div>
                  </div>
               </fieldset>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'progressTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
               <fieldset>
                  <?php echo $this->loadTemplate('audit_details'); ?>
               </fieldset>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            <!-- End Tabs -->
         <?php echo JHtml::_('bootstrap.endTabSet'); ?>

         <input type="hidden" name="task" value="" />
         <input type="hidden" name="return" value="<?php echo $encoded_page;?>" />
         <?php echo JHtml::_('form.token'); ?>
      </div>
      <!-- End Content -->

      <!-- Begin Sidebar -->
      <div class="span2">
         <h4><?php echo JText::_('JDETAILS');?></h4>
         <hr />
         <fieldset class="form-vertical">
            <div class="control-group">
               <?php echo $this->form->getLabel('state'); ?>
               <div class="controls">
                  <?php echo $this->form->getInput('state'); ?>
               </div>
            </div>

            <div class="control-group">
               <?php echo $this->form->getLabel('access'); ?>
               <div class="controls">
                  <?php echo $this->form->getInput('access'); ?>
               </div>
            </div>

            <?php if (isset($fieldSet->language)) { ?>
               <div class="control-group">
                  <?php echo $this->form->getLabel('language'); ?>
                  <div class="controls">
                     <?php echo $this->form->getInput('language'); ?>
                  </div>
               </div>
            <?php } ?>
         </fieldset>
      </div>
      <!-- End Sidebar -->
   </div>
</form>