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

JLoader::import('joomla.html.pagination');
$numCols = 0;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

/** Add in system adminlist css **/
$document = JFactory::getDocument();
// $document->addStyleSheet('media/system/css/adminlist.css');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}
if (! class_exists('IssueTrackerHelperSite')) {
    require_once( JPATH_SITE.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php');
}
if (! class_exists('JHtmlIssueTracker')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'html'.DS.'itissues.php');
}
// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

IssueTrackerHelper::addCSS('media://com_issuetracker/css/issuetracker.css');

$rr = 'index.php?option=com_issuetracker&view=itissueslist';
if ( isset($this->pid) ) {
   $rr .=  '&pid='.$this->pid;
}
// print("Test Template: $rr<p>");

$link = JRoute::_( "index.php?option=com_issuetracker&view=itissueslist" );
$canEdit = $this->params->get('access-edit');

// Check general create permission.
$canCreate = false;
if(JFactory::getUser()->authorise('core.create', 'com_issuetracker')) {
   $canCreate = 1;
}

$admin = 0;
if ($this->params->get('show_all_issues',0) == 1 )  {
   // Check that we are indeed an issue administrator.
   $user       = JFactory::getUser();
   $is_admin   = IssueTrackerHelper::isIssueAdmin($user->id);
   $isstaff  = IssueTrackerHelperSite::isIssueStaff($user->id);
   if ( $is_admin || $isstaff ) {
      $admin = 1;
   } else {
      $admin = 0;
   }
}

// Get max number of projects in list.
$showprojfilt = 0;
$projids    = $this->params->get('project_ids', array());  // It is an array even if there is only one element!
$projcnt = count($projids);
if (in_array('0', $projids, true )) $projcnt = IssueTrackerHelperSite::noprojectstodisplay();
if ($projcnt > 1 ) {
   $showprojfilt = 1;
}

// Determine calling URL and see if a specific pid was specified. If so disable project filter.
// Look for situation where url like pid=&view=itissueslist i.e. pid= present but no value specified.
$rrr = $_SERVER['REQUEST_URI'];
$rpos = strpos ($rrr, 'pid=');
if ( $rpos ) {
   if ( is_numeric(substr($rrr,$rpos+4,1)) ) $showprojfilt = 0;
}

$assigned = 0;
if ($this->params->get('show_assigned_issues',0) == 1 )  {
   $assigned = 1;
}
?>

<style>
div.filter-select input,
div.filter-select select {
        margin-left: 5px;
        margin-right: 5px;
}
fieldset#filter-bar
{
  min-height: 35px;
  border-image-source: none;
  border-image-repeat: stretch stretch;
    border-top: 0 none;
    border-bottom: 1px solid #d5d5d5;
}
.fltlft  {float: left;}
.fltrt   {float: right;}
</style>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
   <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>

<?php if ($canEdit ||  $this->params->get('showl_create_icon') || $this->params->get('showl_print_icon') || $this->params->get('showl_email_icon')) : ?>
   <ul class="actions">
   <?php if (!$this->print) : ?>
      <?php if ($this->params->get('showl_create_icon')) : ?>
         <?php if ($canCreate) : ?>
            <li class="create-icon">
               <?php echo JHtml::_('icon.create',  $this->data, $this->params); ?>
            </li>
         <?php endif; ?>
      <?php endif; ?>

      <?php if ($this->params->get('showl_print_icon')) : ?>
         <li class="print-icon">
            <?php echo JHtml::_('icon.print_popup',  $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($this->params->get('showl_email_icon')) : ?>
         <li class="email-icon">
            <?php echo JHtml::_('icon.email',  $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($canEdit) : ?>
         <li class="edit-icon">
            <?php echo JHtml::_('icon.edit', $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

   <?php else : ?>
      <li>
         <?php echo JHtml::_('icon.print_screen',  $this->data, $this->params); ?>
      </li>
   <?php endif; ?>

   </ul>
<?php endif; ?>
</div>

<form action="<?php echo JRoute::_($rr); ?>" method="post" name="adminForm" id="adminForm">

   <?php if (($showprojfilt == 1) || $this->params->get('showl_status_filter', 0) || $this->params->get('showl_priority_filter', 0) || $this->params->get('showl_type_filter', 0)) : ?>
      <?php if ($this->params->get('showl_ident_date_filter', 0) && $this->params->get('show_identified_date', 0)) : ?>
         <fieldset >
      <?php else : ?>
         <fieldset id="filter-bar">
      <?php endif; ?>

      <div class="filter-select fltrt">
         <?php if ($showprojfilt == 1) : ?>
         <label for="sel1"></label><select name="filter_project_id" id="sel1" class="inputbox" onchange="this.form.submit()">
            <?php if ( $admin == 1 ) {
               echo JHtml::_('select.options', IssueTrackerHelper::getProject_name(), 'value', 'text', $this->state->get('filter.project_id'));
            } else {
               echo JHtml::_('select.options', IssueTrackerHelperSite::getProjects(), 'value', 'text', $this->state->get('filter.project_id'));
            } ?>
         </select>
         <?php endif; ?>

         <?php if ( ($assigned == 0) && $this->params->get('showl_staff_details', 0)) : ?>
            <label for="filt1"></label><select name="filter_assigned_id" id="filt1" class="inputbox" onchange="this.form.submit()">
               <?php echo JHtml::_('select.options', IssueTrackerHelper::getAssignedPeople(), 'value', 'text', $this->state->get('filter.assigned_id'));?>
            </select>
         <?php endif; ?>

         <?php if ($this->params->get('showl_identifier_filter', 0) && $this->params->get('showl_identified_by', 0)) : ?>
             <label for="filt6"></label><select name="filter_identifier" id="filt6" class="inputbox" onchange="this.form.submit()">
               <?php echo JHtml::_('select.options', IssueTrackerHelper::getIdentifyingPeople(), 'value', 'text', $this->state->get('filter.identifier'));?>
            </select>
         <?php endif; ?>

         <?php if ($this->params->get('showl_status_filter', 0)) : ?>
            <label for="filt2"></label>
            <select name="filter_status_id" id="filt2" class="inputbox" onchange="this.form.submit()">
               <?php echo JHtml::_('select.options', IssueTrackerHelper::getStatuses(), 'value', 'text', $this->state->get('filter.status_id'));?>
            </select>
         <?php endif; ?>

         <?php if ($this->params->get('showl_priority_filter', 0)) : ?>
            <label for="filt3"></label><select name="filter_priority_id" id="filt3" class="inputbox" onchange="this.form.submit()">
            <?php echo JHtml::_('select.options', IssueTrackerHelper::getPriorities(), 'value', 'text', $this->state->get('filter.priority_id'));?>
         </select>
         <?php endif; ?>

         <?php if ($this->params->get('showl_type_filter', 0)) : ?>
            <label for="filt4"></label><select name="filter_type_id" id="filt4" class="inputbox" onchange="this.form.submit()">
               <?php echo JHtml::_('select.options', IssueTrackerHelper::getTypes(), 'value', 'text', $this->state->get('filter.type_id'));?>
            </select>
         <?php endif; ?>
      </div>
   </fieldset>

   <?php if ($this->params->get('showl_ident_date_filter', 0) && $this->params->get('show_identified_date', 0)) : ?>
      <fieldset>
         <div class="filter-bar" >
            <div class="filter-select fltrt">
               <label for="filt6"><?php echo JText::_('COM_ISSUETRACKER_END_DATE'); ?></label>
               <?php echo JHtml::_('calendar', $this->state->get('filter.enddate'), 'filter_enddate', 'filter_enddate', '%Y-%m-%d', array('size' => 10, 'onchange' => "this.form.submit()")); ?>
            </div>
            <div class="filter-select fltrt">
               <label for="filt5"><?php echo JText::_('COM_ISSUETRACKER_START_DATE'); ?></label>
               <?php echo JHtml::_('calendar', $this->state->get('filter.begindate'), 'filter_begindate', 'filter_begindate', '%Y-%m-%d', array('size' => 10, 'onchange' => "this.form.submit()")); ?>
            </div>
         </div>
      </fieldset>
   <?php endif; ?>

   <div class="clr"> </div>
   <?php endif; ?>


<table class="<?php echo $this->params->get('tableclass_sfx','adminlist'); ?>">
   <thead>
      <tr>
         <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
            <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_SUMMARY'), 'issue_summary', $this->sortDirection, $this->sortColumn); ?>
         </th>
         <?php if ($this->params->get('showl_issue_description', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_DESCRIPTION'), 'issue_description', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_no', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_NUMBER'), 'alias', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_by', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_IDENTIFIED_BY_PERSON_ID'), 'identified_person_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_date', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_IDENTIFIED_DATE'), 'identified_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_project_name', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_NAME'), 'project_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_staff_details', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ASSIGNED_TO_PERSON_ID'), 'assigned_person_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_status', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('JSTATUS'), 'status_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_type', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_TYPE'), 'type_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_priority', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PRIORITY'), 'priority_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_target_date_field', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_TARGET_RESOLUTION_DATE'), 'target_resolution_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_actual_res_date', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ACTUAL_RESOLUTION_DATE'), 'actual_resolution_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_resolution_field', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_RESOLUTION_SUMMARY'), 'resolution_summary', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
         <?php if ($this->params->get('showl_audit_fields', 0)) : ?>
            <?php if ($this->params->get('showl_audit_createdon_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CREATED_ON'), 'created_on', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <?php if ($this->params->get('showl_audit_createdby_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CREATED_BY'), 'created_by', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <?php if ($this->params->get('showl_audit_modifiedon_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_ON'), 'modified_on', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <?php if ($this->params->get('showl_audit_modifiedby_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_BY'), 'modified_by', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_id', 0)) : ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ISSUE_ID'), 'id', $this->sortDirection, $this->sortColumn); ?>
            </th>
         <?php endif; ?>
      </tr>
   </thead>

   <tfoot>
      <tr>
         <td colspan="<?php echo $numCols; ?>">
            <?php echo $this->loadTemplate('pages'); ?>
         </td>
      </tr>
   </tfoot>

   <tbody>
      <?php if ( count($this->data) ) { foreach($this->data as $i => $dataItem): ?>
      <?php $link = JRoute::_( "index.php?option=com_issuetracker&view=itissues&id={$dataItem->id}" ); ?>
      <tr class="row<?php echo $i % 2; ?>" >
         <td class="fieldDiv fieldValue">
            <?php if ($this->params->get('show_linked_child_detail', 0)) : ?>
               <div class="fltrt">
                  <?php echo JHtml::_('issuetracker.viewIssue', $dataItem->id); ?>
               </div>
               <span title="<?php echo JText::_( 'COM_ISSUETRACKER_VIEW_ISSUE' );?>::<?php echo $this->escape($dataItem->issue_summary); ?>">
                  <a href="<?php echo $link; ?>"><?php echo $dataItem->issue_summary; ?></a>
               </span>
            <?php else: echo $dataItem->issue_summary; endif; ?>
         </td>
         <?php if ($this->params->get('showl_issue_description', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->issue_description; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_no', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->alias; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_by', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->identified_person_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_identified_date', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->identified_date) && $dataItem->identified_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->identified_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_project_name', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->project_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_staff_details', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->assigned_person_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_status', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->status_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_type', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->type_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_priority', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->priority_name; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_target_date_field', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->target_resolution_date) && $dataItem->target_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->target_resolution_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_actual_res_date', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php if ( !empty($dataItem->actual_resolution_date) && $dataItem->actual_resolution_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->actual_resolution_date, JText::_('DATE_FORMAT_LC4')); ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_resolution_field', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->resolution_summary; ?>
            </td>
         <?php endif; ?>
         <?php if ($this->params->get('showl_audit_fields', 0)) : ?>
            <?php if ($this->params->get('showl_audit_createdon_field', 0)) : ?>
               <td class="fieldDiv fieldValue">
                  <?php if ( !empty($dataItem->created_on) && $dataItem->created_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->created_on, JText::_('DATE_FORMAT_LC4')); ?>
               </td>
            <?php endif; ?>
            <?php if ($this->params->get('showl_audit_createdby_field', 0)) : ?>
               <td class="fieldDiv fieldValue">
                  <?php echo $dataItem->created_by; ?>
               </td>
            <?php endif; ?>
            <?php if ($this->params->get('showl_audit_modifiedon_field', 0)) : ?>
               <td class="fieldDiv fieldValue">
                 <?php if ( !empty($dataItem->modified_on) && $dataItem->modified_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->modified_on, JText::_('DATE_FORMAT_LC4')); ?>
               </td>
            <?php endif; ?>
            <?php if ($this->params->get('showl_audit_modifiedby_field', 0)) : ?>
               <td class="fieldDiv fieldValue">
                  <?php echo $dataItem->modified_by; ?>
               </td>
            <?php endif; ?>
         <?php endif; ?>
         <?php if ($this->params->get('showl_issue_id', 0)) : ?>
            <td class="fieldDiv fieldValue">
               <?php echo $dataItem->id; ?>
            </td>
         <?php endif; ?>
      </tr>
      <?php endforeach; ?>
      <?php } else { ?>
      <tr>
         <td>
            <?php echo JText::_('COM_ISSUETRACKER_NO_DATA_FOUND_MSG'); ?>
         </td>
      </tr>
      <?php } ?>
   <tbody>
</table>
   <input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
   <input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
   <input type="hidden" name="project_value" value="<?php echo $this->pid; ?>" />
   <input type="hidden" name="limitstart" value="" />
   <input type="hidden" name="task" value="" />
</form>