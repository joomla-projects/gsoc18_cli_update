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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$app = JFactory::getApplication();
$input = $app->input;

$allow_attachment = $this->params->get('enable_attachments', 0);
$allow_private    = $this->params->get('allow_private_issues');

$document = JFactory::getDocument();
$version = new JVersion();
if (version_compare( $version->RELEASE, '2.5', '<=')) {
   if(JFactory::getApplication()->get('jquery') !== true) {
       $jquery_cdn = $this->params->get( 'jquery_cdn_link', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
       $document->addScript($jquery_cdn);
       // $doc->addScriptDeclaration('jQuery.noConflict()');
       JFactory::getApplication()->set('jquery', true);
    }
} else {
    JHtml::_('jquery.framework');
}

$document->addScript(JURI::root(true).'/media/com_issuetracker/js/issuetracker_cf_min.js');
$js = "var IT_BasePath = '".JURI::base(true)."/';";
$document->addScriptDeclaration($js);

// Get custom group name for the display.
$gname = $this->GetCustomGroupName($this->item->related_project_id);

// echo "<pre>";var_dump($this->item);echo "</pre>";
?>
<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'itissues.cancel') {
         Joomla.submitform(task, document.getElementById('issue-form'));
         // return;
      } else {
         // syncCustomFieldsEditor();
         var validation = validateCustomFields();
         if(validation === true && document.formvalidator.isValid(document.id('issue-form')) ) {
            $IT('#selectedTags option').attr('selected', 'selected');
            Joomla.submitform(task, document.getElementById('issue-form'));
         } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
         }
      }
   }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="issue-form" class="form-validate">
   <div class="width-100 fltlft">

      <!-- Required custom field warning -->
      <div id="itCustomFieldsValidationResults">
         <h3><?php echo JText::_('COM_ISSUETRACKER_FOLLOWING_FIELDS_ARE_REQUIRED'); ?></h3>
         <ul id="itCustomFieldsMissing">
            <li><?php echo JText::_('COM_ISSUETRACKER_MISSING_FIELDS'); ?></li>
         </ul>
      </div>

      <?php if ( ! $allow_private && $this->item->public == 0 ) : ?>
         <fieldset>
            <?php if ($this->item->public) echo '<br/>'.JText::_('COM_ISSUETRACKER_PUBNOTE_WARNING_MSG').'<br/>'; ?>
         </fieldset>
      <?php endif; ?>

      <fieldset class="adminform">
         <legend><?php echo JText::_('JDETAILS').' - Issue: '.$this->item->alias; ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('issue_summary'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('issue_summary'); ?></li>

         <li><?php echo $this->form->getLabel('issue_description'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('issue_description'); ?></li>

         <li><?php echo $this->form->getLabel('identified_by_person_id'); ?>
         <?php echo $this->form->getInput('identified_by_person_id'); ?></li>

         <li><?php echo $this->form->getLabel('identified_date'); ?>
         <?php echo $this->form->getInput('identified_date'); ?></li>

         <li><?php echo $this->form->getLabel('related_project_id'); ?>
         <?php echo $this->form->getInput('related_project_id'); ?></li>

         <li><?php echo $this->form->getLabel('access'); ?>
         <?php echo $this->form->getInput('access'); ?></li>

         <li><?php echo $this->form->getLabel('state'); ?>
         <?php echo $this->form->getInput('state'); ?></li>

         <?php if ( $allow_private ) : ?>
             <li><?php echo $this->form->getLabel('public'); ?>
             <?php echo $this->form->getInput('public'); ?></li>
         <?php endif; ?>

         </ul>
      </fieldset>

      <div id="ITCustomDiv">
         <!-- ?php if ( !empty($this->custom) ) echo $this->loadTemplate('custom'); ? -->
         <?php echo $this->loadTemplate('custom'); ?>
      </div>

      <?php if ( !empty($this->attachment) ) echo $this->loadTemplate('attachments'); ?>

      <?php if ( !empty($this->progress) ) echo $this->loadTemplate('progress'); ?>

      <fieldset class="adminform">
         <legend><?php echo JText::_( 'COM_ISSUETRACKER_PROGRESS_INFORMATION' ); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('assigned_to_person_id'); ?>
         <?php echo $this->form->getInput('assigned_to_person_id'); ?></li>

         <li><?php echo $this->form->getLabel('issue_type'); ?>
         <?php echo $this->form->getInput('issue_type'); ?></li>

         <li><?php echo $this->form->getLabel('status'); ?>
         <?php echo $this->form->getInput('status'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out'); ?>
         <?php echo $this->form->getInput('checked_out'); ?></li>

         <li><?php echo $this->form->getLabel('checked_out_time'); ?>
         <?php echo $this->form->getInput('checked_out_time'); ?></li>

         <li><?php echo $this->form->getLabel('priority'); ?>
         <?php echo $this->form->getInput('priority'); ?></li>

         <li><?php echo $this->form->getLabel('target_resolution_date'); ?>
         <?php echo $this->form->getInput('target_resolution_date'); ?></li>

         <li><?php echo $this->form->getLabel('progress'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('progress'); ?></li>

         <li><?php echo $this->form->getLabel('paccess'); ?>
         <?php echo $this->form->getInput('paccess'); ?></li>

         <li><?php echo $this->form->getLabel('pstate'); ?>
         <?php echo $this->form->getInput('pstate'); ?></li>

         </ul>
      </fieldset>

      <fieldset class="adminform">
         <legend><?php echo JText::_( 'COM_ISSUETRACKER_RESOLUTION_INFORMATION' ); ?></legend>
         <ul class="adminformlist">

         <li><?php echo $this->form->getLabel('actual_resolution_date'); ?>
         <?php echo $this->form->getInput('actual_resolution_date'); ?></li>

         <li><?php echo $this->form->getLabel('resolution_summary'); ?>
         <div class="clr"></div>
         <?php echo $this->form->getInput('resolution_summary'); ?></li>

          </ul>
      </fieldset>

   <?php echo $this->loadTemplate('audit_details');?>

<!-- begin ACL definition-->

   <div class="clr"></div>

   <?php if ($this->canDo->get('core.admin')): ?>
      <div class="width-100 fltlft">
         <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

            <?php echo JHtml::_('sliders.panel', JText::_('COM_ISSUETRACKER_FIELDSET_RULES'), 'access-rules'); ?>
            <fieldset class="panelform">
               <?php echo $this->form->getLabel('rules'); ?>
               <?php echo $this->form->getInput('rules'); ?>
            </fieldset>

         <?php echo JHtml::_('sliders.end'); ?>
      </div>
   <?php endif; ?>

   <!-- end ACL definition-->

   </div>

   <input type="hidden" name="task" value="" />
   <input type="hidden" name="alias" value="<?php echo $this->item->alias; ?>" />
   <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
   <input type="hidden" id="view" name="view" value="<?php echo $input->get('view'); ?>" />
   <?php echo JHtml::_('form.token'); ?>
   <div class="clr"></div>
</form>
