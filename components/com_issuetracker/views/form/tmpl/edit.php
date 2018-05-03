<?php
/*
 *
 * @Version       $Id: edit.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.10
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */
defined('_JEXEC') or die;

// JHtml::_('behavior.framework', true);    // Load Mootools even on J3.4
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

// Load administrator language to avoid duplicate translations
JFactory::getLanguage()->load('com_issuetracker', JPATH_ADMINISTRATOR.'/components/com_issuetracker');

$app = JFactory::getApplication();
$input = $app->input;

$user = JFactory::getUser();

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

IssueTrackerHelper::addCSS('media://com_issuetracker/css/issuetracker.css');

// Create shortcut to parameters.
$parameters = $this->state->get('params');

$allow_attachment = $parameters->get('enable_attachments', 0);
$allow_private    = $parameters->get('allow_private_issues');
$isadmin          = $parameters->get('issues_admin',0);
$isstaff          = $parameters->get('issues_staff',0);
$def_type         = $parameters->get('def_type',0);

$document = JFactory::getDocument();
$version = new JVersion();
if (version_compare( $version->RELEASE, '2.5', '<=')) {
   if(JFactory::getApplication()->get('jquery') !== true) {
       $jquery_cdn = $parameters->get( 'jquery_cdn_link', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
       $document->addScript($jquery_cdn);
       // $doc->addScriptDeclaration('jQuery.noConflict()');
       JFactory::getApplication()->set('jquery', true);
   }
   $clrstng = '<div class="clr"></div>';
} else {
   JHtml::_('jquery.framework');
   $clrstng = '<div class="clearfix"></div>';
}

$document->addScript(JURI::root(true).'/media/com_issuetracker/js/issuetracker_cf_min.js');

// Set up the details of the calling page in case they cancel out.
if ( isset($_SERVER['HTTP_REFERER']) ) {
   $caller = $_SERVER['HTTP_REFERER'];
} else {
   $caller = JURI::base();
}

$wysiwyg = $parameters->get('wysiwyg');   // Get our modified wysiwyg setting.

// Uncomment out to view what form fields are available
//echo '<pre>';var_dump($this->form);'</pre>';
?>

<script type="text/javascript">
   Joomla.submitbutton = function(task) {
      if (task == 'itissues.cancel') {
         Joomla.submitform(task, document.getElementById('issue-form'));
      } else {
         // syncCustomFieldsEditor();
         var validation = validateCustomFields();
         if(validation === true && document.formvalidator.isValid(document.getElementById('issue-form')) ) {
            $IT('#selectedTags option').attr('selected', 'selected');
            Joomla.submitform(task, document.getElementById('issue-form'));
         } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
         }
      }
   };
   function getProjectTypes() {
      var sel_proj = document.getElementById('jformrelated_project_id').value;
      var url = IT_BasePath + 'index.php?option=com_issuetracker&task=itissues.projectTypes&pid=' + sel_proj;
      $IT.ajax({
         url : url,
         type : 'POST',
         dataType : "json",
         success : function(response) {
            if ( response == null || response == '') {
               // alert('Empty response back from ajax');
            } else {
               // Before adding new we must remove previously loaded elements
               var myselect = document.getElementById('jformissue_type');
               while (myselect.options.length != 0) {
                  myselect.options.remove(myselect.options.length - 1);
               }
               var mytypes = response;
               for (var i = 0; i < mytypes.length; i++) {
                  var optn    = document.createElement("OPTION");
                  optn.value  = mytypes[i].id;
                  optn.text   = mytypes[i].type_name;
                  myselect.options.add(optn);
                  if ( optn.value == <?php echo $def_type; ?> ) myselect.options[i].setAttribute('selected','selected');
               }
            }
         }
      });
   }
</script>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($parameters->get('show_page_heading', 1)) : ?>
<h1>
   <?php echo $this->escape($parameters->get('page_heading')); ?>
</h1>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itissues&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="issue-form" enctype="multipart/form-data" class="form-validate">

   <div class="formelm-buttons">
      <button type="button" onclick="Joomla.submitbutton('itissues.save')">
         <?php echo JText::_('JSAVE') ?>
      </button>
      <button type="button" onclick="Joomla.submitbutton('itissues.cancel')">
         <?php echo JText::_('JCANCEL') ?>
      </button>
   </div>

   <!-- Required custom field warning -->
   <div id="itCustomFieldsValidationResults">
      <h3><?php echo JText::_('COM_ISSUETRACKER_FOLLOWING_FIELDS_ARE_REQUIRED'); ?></h3>
      <ul id="itCustomFieldsMissing">
         <li><?php echo JText::_('COM_ISSUETRACKER_MISSING_FIELDS'); ?></li>
      </ul>
   </div>

   <?php $intro = $parameters->get('create_intro',''); if ( !empty($intro) && empty($this->item->id) ) { echo '<br />'.$intro.'<br /><br />'; } ?>

   <?php if ( $allow_private && !empty($this->item->id) ) : ?>
      <fieldset>
         <?php if ($this->item->public) echo '<br/>'.JText::_('COM_ISSUETRACKER_PUBNOTE_PUBLIC_MSG').'<br/>'; else echo '<br/>'.JText::_('COM_ISSUETRACKER_PUBNOTE_PRIVATE_MSG').'<br/>'; ?>
      </fieldset>
   <?php endif; ?>

   <fieldset>
      <legend><?php if (empty($this->item->id)) echo JText::_('COM_ISSUETRACKER_FORM_CREATE_ISSUE');  else  echo JText::_('COM_ISSUETRACKER_FORM_EDIT_ISSUE').' '.$this->item->alias;  ?></legend>

      <div class="formelm">
          <?php echo $this->form->getLabel('alias'); ?>
          <?php echo $this->form->getInput('alias'); ?>
      </div>

      <div>
         <?php if ($parameters->get('admin_edit','0') || $parameters->get('new_record','0') ) : ?>
            <?php if (! $wysiwyg ) : ?>
               <dt>
                  <?php echo $this->form->getLabel('issue_summary'); ?>
               </dt>
               <dd>
                  <?php echo $this->form->getInput('issue_summary'); ?>
               </dd>
            <?php else : ?>
               <?php echo $this->form->getLabel('issue_summary'); ?>
               <?php echo $this->form->getInput('issue_summary'); ?>
            <?php endif; ?>
         <?php else : ?>
            <dt>
               <?php echo $this->form->getLabel('issue_summary'); ?>
            </dt>
            <dd>
               <?php echo $this->item->issue_summary; ?>
            </dd>
         <?php endif; ?>
      </div>
      <?php echo $clrstng; ?>

      <div>
         <?php if ($parameters->get('admin_edit','0') || $parameters->get('new_record','0') ) : ?>
            <?php if (! $wysiwyg ) : ?>
               <dt>
                  <?php echo $this->form->getLabel('issue_description'); ?>
               </dt>
               <dd>
                  <?php echo $this->form->getInput('issue_description'); ?>
               </dd>
            <?php else : ?>
               <?php echo $this->form->getLabel('issue_description'); ?>
               <?php echo $this->form->getInput('issue_description'); ?>
            <?php endif; ?>
         <?php else : ?>
            <dt>
               <?php echo $this->form->getLabel('issue_description'); ?>
            </dt>
            <dd>
               <?php echo $this->item->issue_description; ?>
            </dd>
         <?php endif; ?>
      </div>
      <?php echo $clrstng; ?>
   </fieldset>

   <?php if ( !(empty($this->item->id)) && ($parameters->get('issues_admin', 0) == 0 ) ) : ?>
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ADDITIONAL_INFORMATION_LEGEND'); ?></legend>
         <div class="formelm">
             <?php echo $this->form->getLabel('additional_info'); ?>
             <?php echo $this->form->getInput('additional_info'); ?>
         </div>
      </fieldset>
   <?php endif; ?>

   <?php if ($parameters->get('show_details_section',0)) : ?>
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_DETAILS_LEGEND'); ?></legend>

         <?php if ($allow_private && $parameters->get('show_visibility', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel('public'); ?>
               <?php echo $this->form->getInput('public'); ?>
            </div>
         <?php endif; ?>

         <?php if ($parameters->get('show_identified_by', 0) && !(empty($this->item->id) && !($isadmin || $isstaff)) ) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel('identified_by_person_id'); ?>
               <?php echo $this->form->getInput('identified_by_person_id'); ?>
            </div>
         <?php endif; ?>

         <?php if ($isadmin || $isstaff) echo $this->loadTemplate('notifychk'); ?>

         <?php echo $clrstng; ?>

         <div class="formelm">
            <?php echo $this->form->getLabel('identified_date'); ?>
            <?php echo $this->form->getInput('identified_date'); ?>
         </div>

         <?php echo $clrstng; ?>

         <?php if ($parameters->get('show_project_name', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel('related_project_id'); ?>
               <?php echo $this->form->getInput('related_project_id'); ?>
            </div>
         <?php endif; ?>

         <?php echo $clrstng; ?>

         <div class="formelm">
             <?php echo $this->form->getLabel('issue_type'); ?>
             <?php echo $this->form->getInput('issue_type'); ?>
         </div>

         <?php echo $clrstng; ?>

         <div class="formelm">
             <?php echo $this->form->getLabel('priority'); ?>
             <?php echo $this->form->getInput('priority'); ?>
         </div>

         <?php echo $clrstng; ?>

         <div class="formelm">
             <?php echo $this->form->getLabel('notify'); ?>
             <?php echo $this->form->getInput('notify'); ?>
         </div>
      </fieldset>
   <?php endif; ?>

   <div id="ITCustomDiv">
      <?php echo $this->loadTemplate('custom'); ?>
   </div>

   <?php if ( !empty($this->progress) ) echo $this->loadTemplate('progress'); ?>

   <?php if ( !(empty($this->item->id)) || ($parameters->get('issues_admin', 0) == 1 ) ) : ?>
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_STATUS_LEGEND'); ?></legend>
         <?php if ($parameters->get('show_staff_details', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel( 'assigned_to_person_id' ); ?>
               <?php echo $this->form->getInput( 'assigned_to_person_id' ); ?>
            </div>
            <?php echo $clrstng; ?>
         <?php endif; ?>

         <?php if ($parameters->get('show_issue_status', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel( 'status' ); ?>
               <?php echo $this->form->getInput( 'status' ); ?>
            </div>
         <?php endif; ?>
         <?php echo $clrstng; ?>

         <!-- ?php if ($parameters->get('show_issue_state', 0)) : ? -->
            <div class="formelm">
              <?php echo $this->form->getLabel('state'); ?>
              <?php echo $this->form->getInput('state'); ?>
            </div>
         <?php echo $clrstng; ?>
         <!-- ?php endif; ? -->

         <?php if ($parameters->get('show_tags', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel('tags'); ?>
               <?php echo $this->form->getInput('tags'); ?>
            </div>
         <?php echo $clrstng; ?>
         <?php endif; ?>

         <?php if ($parameters->get('show_target_date_field', 0)) : ?>
            <div class="formelm">
               <?php echo $this->form->getLabel( 'target_resolution_date' ); ?>
               <?php echo $this->form->getInput( 'target_resolution_date' ); ?>
            </div>
         <?php endif; ?>

         <?php if ($parameters->get('show_progress_field', 0)) : ?>
            <?php if ($parameters->get('admin_edit','0')) : ?>
               <div>
                  <?php echo $this->form->getLabel('progress'); ?>
                  <?php echo $this->form->getInput('progress'); ?>
               </div>
               <?php echo $clrstng; ?>
               <div class="formelm">
                  <?php echo $this->form->getLabel('pstate'); ?>
                  <?php echo $this->form->getInput('pstate'); ?>
               </div>
               <?php echo $clrstng; ?>
               <div class="formelm">
                  <?php echo $this->form->getLabel('paccess'); ?>
                  <?php echo $this->form->getInput('paccess'); ?>
               </div>
               <?php echo $clrstng; ?>
               <div class="formelm">
                  <?php echo $this->form->getLabel('progresspublic'); ?>
                  <?php echo $this->form->getInput('progresspublic'); ?>
               </div>
            <?php endif; ?>
         <?php endif; ?>
         <?php echo $clrstng; ?>
      </fieldset>

      <?php if (!empty($this->item->resolution_summary) || $parameters->get('admin_edit', 0) ) : ?>
         <fieldset>
            <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_RESOLUTION_LEGEND'); ?></legend>
            <div class="formelm">
               <?php if ($parameters->get('show_actual_res_date', 0)) : ?>
                  <?php echo $this->form->getLabel( 'actual_resolution_date' ); ?>
                  <?php echo $this->form->getInput( 'actual_resolution_date' ); ?>
               <?php endif; ?>
            </div>

            <div>
               <?php if ($parameters->get('show_resolution_field', 0)) : ?>
                  <?php if ($parameters->get('admin_edit','0')) : ?>
                     <?php echo $this->form->getLabel('resolution_summary'); ?>
                     <?php echo $this->form->getInput('resolution_summary'); ?>
                  <?php else : ?>
                     <dt>
                        <?php echo $this->form->getLabel('resolution_summary'); ?>
                     </dt>
                     <dd>
                        <?php echo $this->item->resolution_summary; ?>
                     </dd>
                  <?php endif; ?>
               <?php endif; ?>
            </div>
            <?php echo $clrstng; ?>
         </fieldset>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($allow_attachment) {
       if (!empty($this->item->id) && !empty($this->attachment) )
          echo $this->loadTemplate('attachments');
      echo $this->loadTemplate('attachment');
   } ?>

   <?php if ($user->guest) echo $this->loadTemplate('user_details'); ?>

   <div class="formelm-buttons">
      <button type="button" onclick="Joomla.submitbutton('itissues.save')">
         <?php echo JText::_('JSAVE') ?>
      </button>
      <button type="button" onclick="Joomla.submitbutton('itissues.cancel')">
         <?php echo JText::_('JCANCEL') ?>
      </button>
   </div>

   <input type="hidden" name="task" value="" />
   <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
   <input type="hidden" name="return_edit" value="<?php echo base64_encode($this->return_edit);?>" />
   <input type="hidden" name="caller" value="<?php echo base64_encode($caller);?>" />
   <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
   <input type="hidden" name="issue_id" value="<?php echo $this->item->id; ?>" />
   <input type="hidden" name="project_id" value="<?php echo $this->item->related_project_id; ?>" />
   <input type="hidden" name="project_value" value="<?php echo $this->pid; ?>" />
   <input type="hidden" id="view" name="view" value="<?php echo $input->get('view'); ?>" />
   <?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>