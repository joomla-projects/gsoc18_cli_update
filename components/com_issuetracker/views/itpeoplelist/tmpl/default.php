<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('joomla.html.pagination');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$numCols = 0;

/** custom css **/
$document = JFactory::getDocument();

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

if (! class_exists('IssueTrackerHelperSite')) {
   require_once( JPATH_SITE.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'helper.php');
}

IssueTrackerHelper::addCSS('media://com_issuetracker/css/issuetracker.css');

$user    = JFactory::getUser();
$isadmin = IssueTrackerHelperSite::isIssueAdmin($user->id);
$isstaff  = IssueTrackerHelperSite::isIssueStaff($user->id);
$admin = 0;
if ( $isadmin || $isstaff ) {
   $admin = 1;
}

$canEdit = $this->params->get('access-edit');
$showprojfilt = 0;

if ($this->params->get('show_project_field', 0)) {
   // Determine whether to display project filter
   $db = JFactory::getDBO();
   $query = 'SELECT count(DISTINCT assigned_project) FROM `#__it_people` WHERE published = 1 ';
   $db->setQuery( $query );
   if ($db->loadResult() >= 2) $showprojfilt = 1;
}

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
         <li class="print-icon">
         <?php echo JHtml::_('icon.print_popup',  $this->data, $this->params); ?>
         </li>
      <?php endif; ?>

      <?php if ($this->params->get('show_email_icon')) : ?>
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

<script language="javascript" type="text/javascript">
function tableOrdering( order, dir, task )
{
        var form = document.adminForm;

        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit( task );
}
</script>
<style>
div.filter-select input,
div.filter-select select {
  margin-left: 5px;
  margin-right: 5px;
}
fieldset#filter-bar
{
  min-height: 35px;
    /* border-right-width-value: 0px;
  border-left-width-value: 0px;
    border-right-style-value: none;
  border-left-style-value: none; */
  border-image-source: none;
  /* border-image-slice: 100% 100% 100% 100%;
  border-image-width: 1 1 1 1;
  border-image-outset: 0 0 0 0; */
  border-image-repeat: stretch stretch;
  border-top: 0 none;
  border-bottom: 1px solid #d5d5d5;
}
.fltlft  {float: left;}
.fltrt   {float: right;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itpeoplelist');?>" method="post" name="adminForm" id="adminForm">
   <?php if (($showprojfilt == 1) || $this->params->get('showl_role_filter', 0)) : ?>
      <fieldset id="filter-bar">
         <div class="filter-select fltrt">
            <?php if ($showprojfilt == 1) : ?>
               <label for="sel1"></label><select name="filter_project_id" id="sel1" class="inputbox" onchange="this.form.submit()">
               <?php if ( $admin ) {
                  echo JHtml::_('select.options', IssueTrackerHelper::getProject_name(), 'value', 'text', $this->state->get('filter.project_id'));
               } else {
                  echo JHtml::_('select.options', IssueTrackerHelperSite::getProject_name(1), 'value', 'text', $this->state->get('filter.project_id'));
               } ?>
               </select>
            <?php endif; ?>

            <?php if ($this->params->get('showl_role_filter', 0)) : ?>
               <label for="sel2"></label><select name="filter_role_id" id="sel2" class="inputbox" onchange="this.form.submit()">
                  <?php echo JHtml::_('select.options', IssueTrackerHelper::getRoles(), 'value', 'text', $this->state->get('filter.role_id'));?>
               </select>
            <?php endif; ?>
         </div>
      </fieldset>
      <div class="clr"> </div>
   <?php endif; ?>

   <table style="width:100%" class="<?php echo $this->params->get('tableclass_sfx','adminlist'); ?>">
      <thead>
         <tr>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PERSON_NAME'), 'person_name', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <?php if ($this->params->get('show_email_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PERSON_EMAIL'), 'person_email', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PERSON_ROLE'), 'person_role', $this->sortDirection, $this->sortColumn); ?>
            </th>

            <?php if ($this->params->get('show_username_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_USERNAME'), 'username', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>

            <?php if ($this->params->get('show_project_field', 0)) : ?>
                <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                   <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ASSIGNED_PROJECT'), 'assigned_project', $this->sortDirection, $this->sortColumn); ?>
                </th>
            <?php endif; ?>

            <?php if ($this->params->get('show_audit_fields', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CREATED_ON'), 'created_on', $this->sortDirection, $this->sortColumn); ?>
               </th>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_CREATED_BY'), 'created_by', $this->sortDirection, $this->sortColumn); ?>
               </th>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_ON'), 'modified_on', $this->sortDirection, $this->sortColumn); ?>
               </th>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_MODIFIED_BY'), 'modified_by', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <?php if ($this->params->get('show_people_id', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PERSON_ID'), 'id', $this->sortDirection, $this->sortColumn); ?>
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
         <?php if (count($this->data) ) { foreach($this->data as $i => $dataItem): ?>
            <?php $link = JRoute::_( "index.php?option=com_issuetracker&view=itpeople&id={$dataItem->id}" ); ?>
            <tr class="row<?php echo $i % 2; ?>" >
               <td class="fieldDiv fieldValue">
                  <?php if ($this->params->get('show_linked_child_detail', 0)) : ?>
                     <span title="<?php echo JText::_( 'COM_ISSUETRACKER_VIEW_PERSON' );?>::<?php echo $this->escape($dataItem->person_name); ?>">
                        <a href="<?php echo $link; ?>"><?php echo $dataItem->person_name; ?></a>
                     </span>
                  <?php else: echo $dataItem->person_name; endif; ?>
               </td>

               <?php if ($this->params->get('show_email_field', 0)) : ?>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->person_email; ?>
                  </td>
               <?php endif; ?>

               <td class="fieldDiv fieldValue">
                  <?php echo $dataItem->role_name; ?>
               </td>

               <?php if ($this->params->get('show_username_field', 0)) : ?>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->username; ?>
                  </td>
               <?php endif; ?>

               <?php if ($this->params->get('show_project_field', 0)) : ?>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->project_name; ?>
                  </td>
               <?php endif; ?>

               <?php if ($this->params->get('show_audit_fields', 0)) : ?>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->created_on; ?>
                  </td>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->created_by; ?>
                  </td>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->modified_on; ?>
                  </td>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->modified_by; ?>
                  </td>
               <?php endif; ?>
               <?php if ($this->params->get('show_people_id', 0)) : ?>
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
   <input type="hidden" name="task" value="" />
</form>