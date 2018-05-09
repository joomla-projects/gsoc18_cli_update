<?php
/*
 *
 * @Version       $Id: default.php 2299 2016-06-01 15:14:37Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.11
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-06-01 16:14:37 +0100 (Wed, 01 Jun 2016) $
 *
 */
defined('_JEXEC') or die('Restricted access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$ordering   = ($listOrder == 'a.lft');
$saveOrder  = ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc');
if ($saveOrder) {
   $saveOrderingUrl = 'index.php?option=com_issuetracker&task=itprojectslist.saveOrderAjax&tmpl=component';
   JHtml::_('sortablelist.sortable', 'projectList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
$sortFields = $this->getSortFields();
$show_assigned = $this->state->params->get('show_assigned_to_headings');
$archived   = $this->state->get('filter.state') == 2 ? true : false;
$trashed    = $this->state->get('filter.state') == -2 ? true : false;
?>
<script type="text/javascript">
Joomla.orderTable = function() {
   table = document.getElementById("sortTable");
   direction = document.getElementById("directionTable");
   order = table.options[table.selectedIndex].value;
   if (order != '<?php echo $listOrder; ?>') {
      dirn = 'asc';
   } else {
      dirn = direction.options[direction.selectedIndex].value;
   }
   Joomla.tableOrdering(order, dirn, '');
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itprojectslist'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
   <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
   </div>
   <div id="j-main-container" class="span10">
<?php else : ?>
   <div id="j-main-container">
<?php endif;?>

      <div id="filter-bar" class="btn-toolbar">
         <div class="filter-search btn-group pull-left">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
            <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
         </div>
         <div class="btn-group pull-right hidden-phone">
             <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
             <?php echo $this->pagination->getLimitBox(); ?>
         </div>
         <div class="btn-group pull-right hidden-phone">
            <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
            <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
               <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
               <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
               <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');  ?></option>
            </select>
         </div>
         <div class="btn-group pull-right">
            <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
            <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
               <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
               <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
            </select>
         </div>
      </div>
      <div class="clearfix"> </div>

   <table class="table table-striped" id="projectList">
      <thead>
         <tr>
            <?php if (isset($this->items[0]->ordering)): ?>
               <th width="1%" class="nowrap center hidden-phone">
                  <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
               </th>
            <?php endif; ?>

            <th style="width:1%" class="hidden-phone">
               <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  JText::_('COM_ISSUETRACKER_PROJECT_NAME'), 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  JText::_('COM_ISSUETRACKER_PROJECT_DESCRIPTION'), 'a.description', $listDirn, $listOrder); ?>
            </th>

            <?php if ($show_assigned == 1 ) : ?>
               <th class='left'>
                  <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_ASSIGNED_PERSON' ), 'a.person_name', $listDirn, $listOrder); ?>
               </th>
            <?php endif; ?>

            <th class='left'>
               <?php echo JHtml::_('grid.sort', JText::_('COM_ISSUETRACKER_START_DATE'), 'a.start_date', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
              <?php echo JHtml::_('grid.sort', JText::_('COM_ISSUETRACKER_TARGET_END_DATE'), 'a.target_end_date', $listDirn, $listOrder); ?>
            </th>

            <th class='left'>
               <?php echo JHtml::_('grid.sort', JText::_('COM_ISSUETRACKER_ACTUAL_END_DATE'), 'a.actual_end_date', $listDirn, $listOrder); ?>
            </th>

            <?php if (isset($this->items[0]->state)) { ?>
               <th style="width:5%">
                  <?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>

            <?php if (isset($this->items[0]->id)) { ?>
               <th style="width:1%" class="nowrap">
                  <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>
         </tr>
      </thead>
      <tfoot>
         <tr>
            <td colspan="10">
               <?php echo $this->pagination->getListFooter(); ?>
            </td>
         </tr>
      </tfoot>
      <tbody>
      <?php foreach ($this->items as $i => $item) :
         $orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
         $canCreate  = $user->authorise('core.create',      'com_issuetracker');
         $canEdit    = $user->authorise('core.edit',        'com_issuetracker');
         $canCheckin = $user->authorise('core.manage',      'com_issuetracker');
         $canChange  = $user->authorise('core.edit.state',  'com_issuetracker');

         // Get the parents of item for sorting
         if ($item->level > 1) {
            $parentsStr = "";
            $_currentParentId = $item->parent_id;
            $parentsStr = " " . $_currentParentId;
            for ($i2 = 0; $i2 < $item->level; $i2++) {
               foreach ($this->ordering as $k => $v) {
                  $v = implode("-", $v);
                  $v = "-" . $v . "-";
                  if (strpos($v, "-" . $_currentParentId . "-") !== false) {
                     $parentsStr .= " " . $k;
                     $_currentParentId = $k;
                     break;
                  }
               }
            }
         } else {
            $parentsStr = "";
         }
         ?>
         <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id; ?>" item-id="<?php echo $item->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
            <td class="order nowrap center hidden-phone">
               <?php
               $iconClass = '';
               if (!$canChange) {
                  $iconClass = ' inactive';
               } elseif (!$saveOrder) {
                  $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
               }
               ?>
               <span class="sortable-handler<?php echo $iconClass ?>">
                  <i class="icon-menu"></i>
               </span>
               <?php if ($canChange && $saveOrder) : ?>
                  <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" />
               <?php endif; ?>
            </td>

            <td class="center  hidden-phone">
               <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>

            <td>
               <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                  <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'itprojectslist.', $canCheckin); ?>
               <?php endif; ?>
               <?php if ($canEdit) : ?>
                  <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=itprojects.edit&id='.(int) $item->id); ?>">
                  <?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>
                  <?php echo $this->escape($item->title); ?></a>
               <?php else : ?>
                  <?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>
                  <?php echo $this->escape($item->title); ?>
               <?php endif; ?>
            </td>
            <td>
               <?php echo $item->description; ?>
            </td>

            <?php if ($show_assigned == 1 ) : ?>
               <td>
                  <?php echo $item->person_name; ?>
               </td>
            <?php endif; ?>

            <td>
               <?php echo JHTML::_('date', $item->start_date, JText::_('DATE_FORMAT_LC1')); ?>
            </td>

            <td>
              <?php if ( !empty($item->target_end_date) && $item->target_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $item->target_end_date, JText::_('DATE_FORMAT_LC1')); ?>
            </td>

            <td>
               <?php if ( !empty($item->actual_end_date) && $item->actual_end_date != "0000-00-00 00:00:00" ) echo JHTML::_('date', $item->actual_end_date, JText::_('DATE_FORMAT_LC1')); ?>
            </td>

            <?php if (isset($this->items[0]->state)) { ?>
               <td class="center">
                  <div class="btn-group">
                     <?php echo JHtml::_('jgrid.published', $item->state, $i, 'itprojectslist.', $canChange, 'cb'); ?>
                     <?php
                        $action = $archived ? 'unarchive' : 'archive';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'itprojectslist');
                        $action = $trashed ? 'untrash' : 'trash';
                        JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'itprojectslist');
                        echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
                     ?>
                  </div>
               </td>
            <?php } ?>

            <?php if (isset($this->items[0]->id)) { ?>
               <td class="center hidden-phone">
                  <span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt); ?>">
                     <?php echo (int) $item->id; ?></span>
               </td>
            <?php } ?>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>
   </div>

   <div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>