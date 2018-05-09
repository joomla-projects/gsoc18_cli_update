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

$app = JFactory::getApplication();
$input = $app->input;

// Save where we are called from.
$referer = $_SERVER['HTTP_REFERER'];
$input->set('return', base64_encode($referer));

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet(JURI::root(true).'/media/com_issuetracker/css/administrator.css');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'attachment.cancel' || document.formvalidator.isValid(document.id('attachment-form'))) {
         Joomla.submitform(task, document.getElementById('attachment-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="attachment-form" class="form-validate" enctype="multipart/form-data">
   <div class="span12 row-fluid form-horizontal">
      <?php echo JHtml::_('bootstrap.startTabSet', 'attachTab', array('active' => 'general')); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'attachTab', 'general', JText::_('COM_ISSUETRACKER_LEGEND_ATTACHMENT', true)); ?>
            <div class="span9 row-fluid form-horizontal">
               <?php echo $this->form->renderField('issue_id'); ?>
               <?php echo $this->form->renderField('title'); ?>
               <?php echo $this->form->renderField('description'); ?>

               <?php if ( $this->state->params->get('show_upload_req','0') ) : ?>
                  <div class="control-group form-inline">
                     <div class="clearfix"></div>
                        <?php echo $this->loadTemplate('attachment'); ?>
                     <div class="clr"></div>
                  </div>
               <?php endif; ?>
            </div>
            <div class="span3 form-vertical">
              <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'attachTab', 'file', JText::_('COM_ISSUETRACKER_FILE_INFORMATION', true)); ?>
            <fieldset>
               <?php echo $this->form->renderField('filename'); ?>
               <?php echo $this->form->renderField('hashname'); ?>
               <?php echo $this->form->renderField('size'); ?>
               <?php echo $this->form->renderField('filepath'); ?>
               <?php echo $this->form->renderField('filetype'); ?>
            </fieldset>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'attachTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
            <div class="row-fluid form-horizontal">
               <?php echo $this->loadTemplate('audit_details'); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.endTabSet'); ?>
   </div>
   <input type="hidden" name="task" value="" />
   <!-- input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" / -->
   <input type="hidden" name="return" value="<?php echo $input->get('return');?>" />
   <?php echo JHtml::_('form.token'); ?>
</form>