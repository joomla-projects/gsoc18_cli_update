<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.10
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$data = $this->data;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

$app = JFactory::getApplication();
$input = $app->input;

IssueTrackerHelper::addCSS('media://com_issuetracker/css/issuetracker.css');

$link = JRoute::_( "index.php?option=com_issuetracker&view=itissues&id={$data->id}" );
// $canEdit = $this->params->get('access-edit');

$allow_attachments = $this->params->get('enable_attachments', 0);
$allow_private     = $this->params->get('allow_private_issues');

$canEdit    = $data->params->get('access-edit');

$user    = JFactory::getUser();

$canDelete = false;
$delmode = $this->params->get('delete', 0);
$delshowfe = $this->params->get('admfe_delete', 1);
$isadmin = IssueTrackerHelper::isIssueAdmin($user->id);
if ($delmode > 0 && $delshowfe == 1 ) {
   // Delete enabled in component.
   if ( $isadmin || $data->params->get('access-delete')) {
      $canDelete = true;
   }
}

$isstaff = IssueTrackerHelper::isIssueAdmin($user->id);

$document = JFactory::getDocument();
$version = new JVersion();
if (version_compare( $version->RELEASE, '2.5', '<=')) {
   if(JFactory::getApplication()->get('jquery') !== true) {
       $jquery_cdn = $this->params->get( 'jquery_cdn_link', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
       $document->addScript($jquery_cdn);
       // $doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
       // $doc->addScriptDeclaration('jQuery.noConflict()');
       JFactory::getApplication()->set('jquery', true);
    }
} else {
    JHtml::_('jquery.framework');
}

$document->addScript(JURI::root(true).'/media/com_issuetracker/js/issuetracker_cf_min.js');

// Check if we are called as a popup. i.e. From the latest issues module.
// If so do not display the edit or delete icons for the popup. This is mainly for space reasons since we do not have much room for editing.
$cururl  = JURI::getInstance()->toString();
$popup   = strpos($cururl, 'tmpl=component');

?>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<h1>
   <?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ($canEdit ||  $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
   <ul class="actions">
   <?php if (!$this->print) : ?>
      <?php if ($this->params->get('show_print_icon')) : ?>
         <li class="print-icon"><?php echo JHtml::_('icon.print_popup',  $data, $this->params); ?></li>
      <?php endif; ?>
      <?php if ($this->params->get('show_email_icon')) : ?>
         <li class="email-icon"><?php echo JHtml::_('icon.email',  $data, $this->params); ?></li>
      <?php endif; ?>
      <?php if ( ! $popup ) { ?>
         <?php if ($canEdit) : ?>
            <li class="edit-icon"><?php echo JHtml::_('icon.edit', $data, $this->params); ?></li>
         <?php endif; ?>
         <?php if ($canDelete) : ?>
            <li class="delete-icon"><?php echo JHtml::_('icon.delete', $data, $this->params); ?></li>
         <?php endif; ?>
      <?php } ?>
   <?php else : ?>
      <li><?php echo JHtml::_('icon.print_screen',  $data, $this->params); ?></li>
   <?php endif; ?>

   </ul>
<?php endif; ?>

<?php if ($this->params->get('show_tags', 1) && !empty($data->tags->itemTags)) : ?>
   <?php $data->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
   <?php echo $data->tagLayout->render($data->tags->itemTags); ?>
<?php endif; ?>

<form id="adminForm" action="<?php echo JRoute::_('index.php')?>" method="post">
   <div class="fieldDiv">
      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_DEFAULT_LEGEND'); ?></legend>
         <dl>
            <?php if ($this->params->get('show_issue_id', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_ID_LABEL' ); ?>  </dt>
               <dd class="dl-horizontal"> <?php echo $data->id; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_issue_no', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_NUMBER_LABEL' ); ?>  </dt>
               <dd class="dl-horizontal"> <?php echo $data->alias; ?> </dd>
            <?php endif; ?>

            <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_SUMMARY_LABEL' ); ?> </dt>
            <dd class="dl-horizontal" > <?php echo $data->issue_summary; ?> </dd>

            <?php if ($this->params->get('show_issue_description', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_DESCRIPTION_LABEL' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo JHtml::_('content.prepare', $data->issue_description); ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_identified_by', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_IDENTIFIED_PERSON_NAME_LABEL' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo $data->identified_person_name; ?> </dd>
            <?php endif; ?>

            <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ISSUE_TYPE_LABEL' ); ?> </dt>
            <dd class="dl-horizontal"> <?php echo $data->type_name; ?> </dd>

            <?php if ($allow_private && $this->params->get('show_visibility', 0)) : ?>
               <dt><?php echo JText::_( 'COM_ISSUETRACKER_VISIBILITY_LABEL' ); ?> </dt>
               <dd><?php if ($data->public) echo JText::_('COM_ISSUETRACKER_PUBLIC_OPTION'); else echo JText::_('COM_ISSUETRACKER_PRIVATE_OPTION'); ?></dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_identified_date', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_IDENTIFIED_DATE_LABEL' ); ?> </dt>
               <dd class="dl-horizontal">
                  <?php if ( !empty($data->identified_date) && $data->identified_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->identified_date, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_project_name', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PROJECT_NAME_LABEL' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo $data->project_name; ?> </dd>
            <?php endif; ?>
          </dl>
      </fieldset>

      <?php if ( !empty($this->custom) ) echo $this->loadTemplate('custom'); ?>

      <?php if ( $allow_attachments && !empty($this->attachment) ) echo $this->loadTemplate('attachments'); ?>

      <?php if ( ($isadmin || $isstaff) || $this->params->get('show_progress_field', 0)) : ?>
         <?php if ( !empty($this->progress) ) echo $this->loadTemplate('progress'); ?>
      <?php endif; ?>

      <?php if (version_compare( $version->RELEASE, '3.1', 'gt')) echo "<br />"; ?>

      <fieldset>
         <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_STATUS_LEGEND'); ?></legend>
         <dl>
           <?php if ($this->params->get('show_staff_details', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ASSIGNED_PERSON_NAME_LABEL' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo $data->assigned_person_name; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_issue_status', 0)) : ?>
               <dt> <?php echo JText::_( 'JSTATUS' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo $data->status_name; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_issue_priority', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_PRIORITY_LABEL' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo $data->priority_name; ?> </dd>
            <?php endif; ?>

            <?php if ($this->params->get('show_target_date_field', 0)) : ?>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_TARGET_RESOLUTION_DATE_LABEL' ); ?> </dt>
               <dd class="dl-horizontal">
                  <?php if ( !empty($data->target_resolution_date) && $data->target_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->target_resolution_date, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>
            <?php endif; ?>
         </dl>
      </fieldset>

      <?php if ( !empty($data->resolution_summary) ) : ?>
         <fieldset>
            <legend><?php echo JText::_('COM_ISSUETRACKER_ISSUE_RESOLUTION_LEGEND'); ?></legend>
            <dl>
               <?php if ($this->params->get('show_actual_res_date', 0)) : ?>
                  <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_ACTUAL_RESOLUTION_DATE_LABEL' ); ?> </dt>
                  <dd class="dl-horizontal">
                     <?php if ( !empty($data->actual_resolution_date) && $data->actual_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->actual_resolution_date, JText::_('DATE_FORMAT_LC1')); ?>
                  </dd>
               <?php endif; ?>

               <?php if ($this->params->get('show_resolution_field', 0)) : ?>
                  <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_RESOLUTION_SUMMARY_LABEL' ); ?> </dt>
                  <dd class="dl-horizontal"> <?php echo $data->resolution_summary; ?> </dd>
               <?php endif; ?>
            </dl>
         </fieldset>
      <?php endif; ?>

      <?php if ($this->params->get('show_audit_fields', 0)) : ?>
         <fieldset>
            <legend><?php echo JText::_('COM_ISSUETRACKER_AUDIT_LEGEND'); ?></legend>
            <dl>
               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_ON_LABEL' ); ?> </dt>
               <dd class="dl-horizontal">
                    <?php if ( !empty($data->created_on) && $data->created_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->created_on, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_CREATED_BY_LABEL' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo $data->created_by; ?> </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_ON_LABEL' ); ?> </dt>
               <dd class="dl-horizontal">
                  <?php if ( !empty($data->modified_on) && $data->modified_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $data->modified_on, JText::_('DATE_FORMAT_LC1')); ?>
               </dd>

               <dt> <?php echo JText::_( 'COM_ISSUETRACKER_FIELD_MODIFIED_BY_LABEL' ); ?> </dt>
               <dd class="dl-horizontal"> <?php echo $data->modified_by; ?> </dd>
            </dl>
         </fieldset>
      <?php endif; ?>
   </div>

   <input type="hidden" id="view" name="view" value="<?php echo 'fe'.$input->get('view'); ?>" />

</form>
</div>