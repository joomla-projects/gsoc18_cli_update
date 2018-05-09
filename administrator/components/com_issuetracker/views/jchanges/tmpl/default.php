<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$textlen = $this->state->params->get('textlen', 50);

// Import CSS
// $document = JFactory::getDocument();
// $document->addStyleSheet('components/com_issuetracker/assets/css/issuetracker.css');

$user = JFactory::getUser();
$userId  = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.ordering';
if ($saveOrder)
{
   $saveOrderingUrl = 'index.php?option=com_issuetracker&task=jchanges.saveOrderAjax&tmpl=component';
   JHtml::_('sortablelist.sortable', 'jchangeList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
   Joomla.orderTable = function() {
      var table = document.getElementById("sortTable");
      var direction = document.getElementById("directionTable");
      var order = table.options[table.selectedIndex].value;
      var dirn = '';
      if (order != '<?php echo $listOrder; ?>') {
         dirn = 'asc';
      } else {
         dirn = direction.options[direction.selectedIndex].value;
      }
      Joomla.tableOrdering(order, dirn, '');
   }
</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar)) {
    $this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=jchanges'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty($this->sidebar)): ?>
   <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
   </div>
   <div id="j-main-container" class="span10">
<?php else : ?>
   <div id="j-main-container">
<?php endif;?>

      <div id="filter-bar" class="btn-toolbar">
         <div class="filter-search btn-group pull-left">
            <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER');?></label>
            <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
         </div>
         <div class="btn-group pull-left">
            <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
            <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
         </div>
         <div class="btn-group pull-right hidden-phone">
            <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
            <?php echo $this->pagination->getLimitBox(); ?>
         </div>
         <div class="btn-group pull-right hidden-phone">
            <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
            <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
               <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
               <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
               <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
            </select>
         </div>
         <div class="btn-group pull-right">
            <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
            <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
               <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
               <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
            </select>
         </div>
      </div>
      <div class="clearfix"> </div>
      <table class="ittable table-striped" style="table-layout: fixed; word-wrap:break-word;" id="jchangeList">
         <thead>
            <tr>
                <?php if (isset($this->items[0]->ordering)): ?>
               <th style="width:1%" class="nowrap center hidden-phone">
                  <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
               </th>
                <?php endif; ?>
               <th style="width:1%" class="hidden-phone">
                  <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
               </th>
                <?php if (isset($this->items[0]->state)): ?>
               <th style="width:4%" class="nowrap center">
                  <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
               </th>
                <?php endif; ?>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_TABLE_NAME', 'a.table_name', $listDirn, $listOrder); ?>
            </th>
            <!-- th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_COMPONENT', 'a.component', $listDirn, $listOrder); ?>
            </th -->
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ROW_KEY', 'a.row_key', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ROW_KEY_LINK', 'a.row_key_link', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_COLUMN_NAME', 'a.column_name', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_COLUMN_TYPE', 'a.column_type', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ACTION', 'a.action', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_OLD_VALUE', 'a.old_value', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_NEW_VALUE', 'a.new_value', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_CHANGED_BY', 'a.change_by', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_CHANGE_DATE', 'a.change_date', $listDirn, $listOrder); ?>
            </th>


                <?php if (isset($this->items[0]->id)): ?>
               <th style="width:3%" class="nowrap center hidden-phone">
                  <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
               </th>
                <?php endif; ?>
            </tr>
         </thead>
         <tfoot>
                <?php
                if(isset($this->items[0])){
                    $colspan = count(get_object_vars($this->items[0]));
                }
                else{
                    $colspan = 10;
                }
            ?>
         <tr>
            <td colspan="<?php echo $colspan ?>">
               <?php echo $this->pagination->getListFooter(); ?>
            </td>
         </tr>
         </tfoot>
         <tbody>
         <?php foreach ($this->items as $i => $item) :
            $ordering   = ($listOrder == 'a.ordering');
                $canCreate = $user->authorise('core.create',      'com_issuetracker');
                $canEdit   = $user->authorise('core.edit',        'com_issuetracker');
                $canCheckin   = $user->authorise('core.manage',   'com_issuetracker');
                $canChange = $user->authorise('core.edit.state',  'com_issuetracker');
            ?>
            <tr class="row<?php echo $i % 2; ?>">

                <?php if (isset($this->items[0]->ordering)): ?>
               <td class="order nowrap center hidden-phone">
               <?php if ($canChange) :
                  $disableClassName = '';
                  $disabledLabel   = '';
                  if (!$saveOrder) :
                     $disabledLabel    = JText::_('JORDERINGDISABLED');
                     $disableClassName = 'inactive tip-top';
                  endif; ?>
                  <span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
                     <i class="icon-menu"></i>
                  </span>
                  <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
               <?php else : ?>
                  <span class="sortable-handler inactive" >
                     <i class="icon-menu"></i>
                  </span>
               <?php endif; ?>
               </td>
                <?php endif; ?>
               <td class="center hidden-phone">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
               </td>
                <?php if (isset($this->items[0]->state)): ?>
               <td class="center">
                  <?php echo JHtml::_('jgrid.published', $item->state, $i, 'jchanges.', $canChange, 'cb'); ?>
               </td>
                <?php endif; ?>

            <td>
            <?php if (isset($item->checked_out) && $item->checked_out) : ?>
               <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'jchanges.', $canCheckin); ?>
            <?php endif; ?>
            <?php if ($canEdit) : ?>
               <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=jchange.edit&id='.(int) $item->id); ?>">
               <?php echo $this->escape($item->table_name); ?></a>
            <?php else : ?>
               <?php echo $this->escape($item->table_name); ?>
            <?php endif; ?>
            </td>
            <!-- td>
               <?php echo $item->component; ?>
            </td -->
            <td>
               <?php echo $item->row_key; ?>
            </td>
            <td>
               <?php echo $item->row_key_link; ?>
            </td>
            <td>
               <?php echo $item->column_name; ?>
            </td>
            <td>
               <?php echo $item->column_type; ?>
            </td>
            <td>
               <?php echo $item->action; ?>
            </td>
            <td>
               <?php if (strlen($item->old_value) > $textlen ) { echo substr($item->old_value, 0, $textlen).'---->'; } else { echo $item->old_value; } ?>
            </td>
            <td>
               <?php if (strlen($item->new_value) > $textlen ) { echo substr($item->new_value, 0, $textlen).'---->'; } else { echo $item->new_value; } ?>
            </td>
            <td>
               <?php echo $item->change_by; ?>
            </td>
            <td>
               <?php echo IssueTrackerHelperDate::getDate($item->change_date); ?>
            </td>

            <?php if (isset($this->items[0]->id)): ?>
               <td class="center hidden-phone">
                  <?php echo (int) $item->id; ?>
               </td>
            <?php endif; ?>
            </tr>
            <?php endforeach; ?>
         </tbody>
      </table>

      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>