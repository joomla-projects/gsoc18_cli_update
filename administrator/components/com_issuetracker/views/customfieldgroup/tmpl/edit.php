<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.6
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/media/com_issuetracker/css/administrator.css');
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'customfieldgroup.cancel' || document.formvalidator.isValid(document.id('customfieldgroup-form'))) {
         Joomla.submitform(task, document.getElementById('customfieldgroup-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="customfieldgroup-form" class="form-validate">
   <div class="span12 row-fluid form-horizontal">
      <?php echo JHtml::_('bootstrap.startTabSet', 'issueTab', array('active' => 'general')); ?>
         <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'general', JText::_('COM_ISSUETRACKER_LEGEND_CUSTOMFIELDGROUP', true)); ?>
            <div class="span9 row-fluid form-horizontal">
               <?php echo $this->form->renderField('name'); ?>
               <!-- ?php echo $this->form->renderField('description'); ? -->
            </div>
            <div class="span3 form-vertical">
               <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
            <div class="row-fluid form-horizontal">
               <?php echo $this->loadTemplate('audit_details'); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.endTabSet'); ?>

      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>