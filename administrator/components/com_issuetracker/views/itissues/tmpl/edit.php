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

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;

// Get custom group name for the display.
$gname = $this->GetCustomGroupName($this->item->related_project_id);

// Create shortcut to parameters.
$parameters = $this->state->get('params');

$allow_attachment = $parameters->get('enable_attachments', 0);
$allow_private    = $parameters->get('allow_private_issues');

$document = JFactory::getDocument();
$document->addScript(JURI::root(true).'/media/com_issuetracker/js/issuetracker_cf_min.js');

$js = "var IT_BasePath = '".JURI::base(true)."/';";
$document->addScriptDeclaration($js);

// echo "<pre>";var_dump($this->item);echo "</pre>";
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'itissues.cancel') {
         Joomla.submitform(task, document.getElementById('issue-form'));
      } else {
         // syncCustomFieldsEditor();
         var validation = validateCustomFields();
         if(validation === true && document.formvalidator.isValid(document.id('issue-form'))) {
            $IT('#selectedTags option').attr('selected', 'selected');
            Joomla.submitform(task, document.getElementById('issue-form'));
         } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
         }
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="issue-form" class="form-validate">
   <div class="row-fluid">
      <?php echo $this->loadTemplate('header_alias'); ?>
      <div class="span12 form-horizontal">
         <?php echo JHtml::_('bootstrap.startTabSet', 'issueTab', array('active' => 'general')); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'general', JText::_('COM_ISSUETRACKER_ISSUE_DEFAULT_LEGEND', true)); ?>
               <div class="span9 row-fluid form-horizontal">
                  <?php if ( ! $allow_private && $this->item->public == 0 ) : ?>
                     <fieldset>
                        <?php if ($this->item->public) echo '<br/>'.JText::_('COM_ISSUETRACKER_PUBNOTE_WARNING_MSG').'<br/>'; ?>
                     </fieldset>
                  <?php endif; ?>

                  <?php echo $this->form->renderField('identified_by_person_id'); ?>
                  <?php echo $this->form->renderField('identified_date'); ?>
                  <?php echo $this->form->renderField('related_project_id'); ?>

                  <?php if ( $allow_private ) : ?>
                     <?php echo $this->form->renderField('public'); ?>
                  <?php endif; ?>

                  <?php echo $this->form->renderField('issue_summary'); ?>
                  <?php echo $this->form->renderField('issue_description'); ?>
               </div>

               <div class="span3 form-vertical">
                  <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
               </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php if ( !empty($this->progress) ) { ?>
               <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'oprogress', JText::_('COM_ISSUETRACKER_PROGRESS_HISTORY', true)); ?>
                  <fieldset>
                     <?php echo $this->loadTemplate('progress'); ?>
                  </fieldset>
               <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php } ?>

            <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'progress', JText::_('COM_ISSUETRACKER_PROGRESS_INFORMATION', true)); ?>
               <div class="row-fluid">
                  <div class="span4">
                     <?php echo $this->form->renderField('assigned_to_person_id'); ?>
                     <?php echo $this->form->renderField('issue_type'); ?>
                     <?php echo $this->form->renderField('status'); ?>
                  </div>

                  <div class="span4">
                     <?php echo $this->form->renderField('priority'); ?>
                     <?php echo $this->form->renderField('target_resolution_date'); ?>
                  </div>

                  <div class="span4">
                     <?php echo $this->form->renderField('paccess'); ?>
                     <?php echo $this->form->renderField('pstate'); ?>
                     <?php echo $this->form->renderField('progresspublic'); ?>
                  </div>

                  <div class="clearfix"></div>
                  <div class="span10 row-fluid form-horizontal">
                     <?php echo $this->form->renderField('progress'); ?>
                  </div>
               </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <!-- ?php if ( !empty($this->custom) ) { ? -->
               <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'oustom', JText::_( 'COM_ISSUETRACKER_CUSTOMFIELDS', true)); ?>
                  <div id="ITCustomDiv">
                     <fieldset>
                        <?php echo $this->loadTemplate('custom'); ?>
                     </fieldset>
                  </div>
               <?php echo JHtml::_('bootstrap.endTab'); ?>
            <!-- ?php } ? -->

            <?php if ( !empty($this->attachment) ) { ?>
               <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'attachments', JText::_('COM_ISSUETRACKER_ATTACHMENT_DETAILS', true)); ?>
                  <fieldset>
                     <?php echo $this->loadTemplate('attachments'); ?>
                  </fieldset>
               <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php } ?>

            <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'resolution', JText::_('COM_ISSUETRACKER_RESOLUTION_INFORMATION', true)); ?>
               <div class="span10 row-fluid form-horizontal">
                  <?php echo $this->form->renderField('actual_resolution_date'); ?>
                  <?php echo $this->form->renderField('resolution_summary'); ?>
               </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'audit', JText::_('COM_ISSUETRACKER_AUDIT_INFORMATION', true)); ?>
               <div class="row-fluid form-horizontal">
                  <?php echo $this->loadTemplate('audit_details'); ?>
               </div>
            <?php echo JHtml::_('bootstrap.endTab'); ?>

            <?php if ($this->canDo->get('core.admin')) : ?>
               <?php echo JHtml::_('bootstrap.addTab', 'issueTab', 'permissions', JText::_('COM_ISSUETRACKER_FIELDSET_RULES', true)); ?>
                  <fieldset>
                     <?php echo $this->form->getInput('rules'); ?>
                  </fieldset>
               <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php endif; ?>

         <?php echo JHtml::_('bootstrap.endTabSet'); ?>

         <input type="hidden" name="task" value="" />
         <!-- input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" / -->
         <input type="hidden" name="alias" value="<?php echo $this->item->alias; ?>" />
         <input type="hidden" name="return" value="<?php echo $input->get('return');?>" />
         <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
         <input type="hidden" id="view" name="view" value="<?php echo $input->get('view'); ?>" />
         <?php echo JHtml::_('form.token'); ?>
      </div>

   </div>
</form>