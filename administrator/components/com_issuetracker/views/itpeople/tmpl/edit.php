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

require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'issuetracker.php';
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'itpeople.cancel' || document.formvalidator.isValid(document.id('type-form'))) {
         Joomla.submitform(task, document.getElementById('type-form'));
      } else {
         alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="type-form" class="form-validate">
   <div class="span12 row-fluid form-horizontal">
      <?php echo JHtml::_('bootstrap.startTabSet', 'personTab', array('active' => 'general')); ?>
         <?php echo JHtml::_('bootstrap.addTab', 'personTab', 'general', JText::_('COM_ISSUETRACKER_PEOPLE_DEFAULT_LEGEND', true)); ?>
            <div class="span9 row-fluid form-horizontal">
               <div class="span6">
                  <?php echo $this->form->renderField('user_id'); ?>
                  <?php echo $this->form->renderField('person_name'); ?>
                  <?php echo $this->form->renderField('username'); ?>
                  <?php echo $this->form->renderField('person_email'); ?>
                  <?php echo $this->form->renderField('phone_number'); ?>
                  <?php echo $this->form->renderField('person_role'); ?>
                  <?php echo $this->form->renderField('assigned_project'); ?>
               </div>
               <div class="span6">
                  <?php echo $this->form->renderField('issues_admin'); ?>
                  <?php echo $this->form->renderField('staff'); ?>
                  <?php echo $this->form->renderField('email_notifications'); ?>
                  <?php if ( IssuetrackerHelper::comp_installed('com_acysms'))
                     echo $this->form->renderField('sms_notify'); ?>
                  <?php echo $this->form->renderField('registered'); ?>
               </div>
            </div>
            <div class="span3 form-vertical">
               <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>

         <?php echo JHtml::_('bootstrap.addTab', 'personTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
            <div class="row-fluid form-horizontal">
               <?php echo $this->loadTemplate('audit_details'); ?>
            </div>
         <?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php echo JHtml::_('bootstrap.endTabSet'); ?>

      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>