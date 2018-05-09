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
// $document->addStyleSheet('media/system/css/adminlist.css');

if (! class_exists('IssueTrackerHelper')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'issuetracker.php');
}

IssueTrackerHelper::addCSS('media://com_issuetracker/css/issuetracker.css');

$canEdit = $this->params->get('access-edit');
$canCreateIssue = false;
if(JFactory::getUser()->authorise('core.create', 'com_issuetracker')) {
   $canCreateIssue = true;
}

?>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
   <?php if ($this->params->get('show_page_heading', 1)) : ?>
      <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
   <?php endif; ?>

   <?php if ($canEdit ||  $this->params->get('show_print_icon') || $this->params->get('show_email_icon')) : ?>
      <ul class="actions">
         <?php if (!$this->print) : ?>
            <?php if ($this->params->get('show_print_icon')) : ?>
               <li class="print-icon"><?php echo JHtml::_('icon.print_popup',  $this->data, $this->params); ?></li>
            <?php endif; ?>

            <?php if ($this->params->get('show_email_icon')) : ?>
               <li class="email-icon"><?php echo JHtml::_('icon.email',  $this->data, $this->params); ?></li>
            <?php endif; ?>

            <?php if ($canEdit) : ?>
               <li class="edit-icon"><?php echo JHtml::_('icon.edit', $this->data, $this->params); ?></li>
            <?php endif; ?>
         <?php else : ?>
            <li><?php echo JHtml::_('icon.print_screen',  $this->data, $this->params); ?></li>
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

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itprojectslist');?>" method="post" name="adminForm" id="adminForm">
   <table class="<?php echo $this->params->get('tableclass_sfx','adminlist'); ?>">
      <thead>
         <tr>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_NAME'), 'title', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_DESCRIPTION'), 'description', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_START_DATE'), 'start_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <?php if ($this->params->get('show_target_date_field', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_TARGET_END_DATE'), 'target_end_date', $this->sortDirection, $this->sortColumn); ?>
               </th>
            <?php endif; ?>
            <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
               <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_ACTUAL_END_DATE'), 'actual_end_date', $this->sortDirection, $this->sortColumn); ?>
            </th>
            <th><?php $numCols++; ?>
               <!-- Temp place holder for action buttons -->
               <?php echo JText::_( 'COM_ISSUETRACKER_ISSUES_HEADER' ); ?>
            </th>
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
            <?php if ($this->params->get('show_project_id', 0)) : ?>
               <th class="fieldDiv fieldLabel"><?php $numCols++; ?>
                  <?php echo JHTML::_( 'grid.sort', JText::_('COM_ISSUETRACKER_PROJECT_ID'), 'project_id', $this->sortDirection, $this->sortColumn); ?>
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
            <?php if(JFactory::getUser()->authorise('core.create', 'com_issuetracker.itprojects.'.$dataItem->project_id)) $canCreateIssue = true;
               $link  = JRoute::_( "index.php?option=com_issuetracker&view=itprojects&id={$dataItem->project_id}" );
               $linkv = JRoute::_( "index.php?option=com_issuetracker&view=itissueslist&pid={$dataItem->project_id}" );
               if ($canCreateIssue) $linkc = JRoute::_( "index.php?option=com_issuetracker&view=form&layout=edit&pid={$dataItem->project_id}" ); else $linkc = $link;
            ?>
            <tr class="row<?php echo $i % 2; ?>" >
               <td class="fieldDiv fieldValue">
                  <?php if ($this->params->get('show_linked_pchild_detail', 0)) : ?>
                     <span title="<?php echo JText::_( 'COM_ISSUETRACKER_VIEW_PROJECT' );?>::<?php echo $this->escape($dataItem->project_name); ?>">
                        <a href="<?php echo $link; ?>"><?php echo $dataItem->project_name; ?></a>
                     </span>
                  <?php else: echo $dataItem->project_name; endif; ?>
               </td>
               <td class="fieldDiv fieldValue">
                  <?php echo $dataItem->description; ?>
               </td>
               <td class="fieldDiv fieldValue">
                  <?php if ( !empty($dataItem->start_date) && $dataItem->start_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->start_date, JText::_('DATE_FORMAT_LC4')); ?>
               </td>
               <?php if ($this->params->get('show_target_date_field', 0)) : ?>
                  <td class="fieldDiv fieldValue">
                    <?php if ( !empty($dataItem->target_end_date) && $dataItem->target_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->target_end_date, JText::_('DATE_FORMAT_LC4')); ?>
                  </td>
               <?php endif; ?>
               <td class="fieldDiv fieldValue">
                  <?php if ( !empty($dataItem->actual_end_date) && $dataItem->actual_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->actual_end_date, JText::_('DATE_FORMAT_LC4')); ?>
               </td>

               <td>
                  <a class="btn btn-success btn-small" href="<?php echo $linkv; ?>">
                     <i class="icon-folder-open icon-white"></i>
                     <?php echo JText::_('COM_ISSUETRACKER_PROJECTS_VIEWISSUES') ?>
                  </a>
                  <?php if($canCreateIssue): ?><br/>
                     <a class="btn btn-success btn-small" href="<?php echo $linkc; ?>">
                        <i class="icon-file icon-white"></i>
                        <?php echo JText::_('COM_ISSUETRACKER_PROJECTS_BUTTON_NEWISSUE') ?>
                     </a>
                  <?php endif; ?>
               </td>

               <?php if ($this->params->get('show_audit_fields', 0)) : ?>
                  <td class="fieldDiv fieldValue">
                     <?php if ( !empty($dataItem->created_on) && $dataItem->created_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->created_on, JText::_('DATE_FORMAT_LC4')); ?>
                  </td>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->created_by; ?>
                  </td>
                  <td class="fieldDiv fieldValue">
                     <?php if ( !empty($dataItem->modified_on) && $dataItem->modified_on != "0000-00-00 00:00:00" ) echo JHTML::_('date', $dataItem->modified_on, JText::_('DATE_FORMAT_LC4')); ?>
                  </td>
                  <td class="fieldDiv fieldValue">
                     <?php echo $dataItem->modified_by; ?>
                  </td>
               <?php endif; ?>
               <?php if ($this->params->get('show_project_id', 0)) : ?>
                  <td style="margin: 0 auto;" class="fieldDiv fieldValue">
                     <?php echo $dataItem->project_id; ?>
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