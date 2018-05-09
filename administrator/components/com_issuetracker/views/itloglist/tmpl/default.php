<?php
/*
 *
 * @Version       $Id: default.php 2167 2016-01-01 16:41:39Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2016 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2016-01-01 16:41:39 +0000 (Fri, 01 Jan 2016) $
 *
 */

defined('_JEXEC') or die('Restricted access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
   require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

// JHtml::_('behavior.tooltip');
// JHTML::_('script','system/multiselect.js',false,true);
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId  = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.id';
$sortFields = $this->getSortFields();
require_once dirname(__FILE__).'/coloriser.php';

?>
<script type="text/javascript">
Joomla.orderTable = function()
{
   var table = document.getElementById("sortTable");
   var direction = document.getElementById("directionTable");
   var order = table.options[table.selectedIndex].value;
   var dirn = '';
   if (order != '<?php echo $listOrder; ?>')
   {
      dirn = 'asc';
   }
   else
   {
      dirn = direction.options[direction.selectedIndex].value;
   }
   Joomla.tableOrdering(order, dirn, '');
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itloglist'); ?>" method="post" name="adminForm" id="adminForm">
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

      <table class="ittable table-striped">
         <thead>
            <tr>
               <th style="width:1%" class="hidden-phone">
                  <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
               </th>

               <th class='left' style="width:10%">
                  <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_LOG_PRIORITY_NAME', 'a.priority', $listDirn, $listOrder); ?>
               </th>
               <th class='left' style="width:15%">
                  <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_DATE', 'a.date', $listDirn, $listOrder); ?>
               </th>
               <th class='left'>
                  <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_LOG_MESSAGE', 'a.message', $listDirn, $listOrder); ?>
               </th>
               <!-- th class='left' style="width:10%">
                  <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_CATEGORY', 'a.category', $listDirn, $listOrder); ?>
               </th -->

               <?php if (isset($this->items[0]->ordering)) { ?>
                  <th style="width:10%">
                     <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                     <?php if ($canOrder && $saveOrder) :?>
                        <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'itloglist.saveorder'); ?>
                     <?php endif; ?>
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
            $ordering   = ($listOrder == 'a.id');
            $canCreate  = $user->authorise('core.create',      'com_issuetracker');
            $canEdit    = $user->authorise('core.edit',        'com_issuetracker');
            $canCheckin = $user->authorise('core.manage',      'com_issuetracker');
            $canChange  = $user->authorise('core.edit.state',  'com_issuetracker');
         ?>
         <!-- tr class="row<?php echo $i % 2; ?>" -->
            <tr>
               <td class="center">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
               </td>
               <!-- Following call outputs the td codes directly -->
               <?php echo IssueTrackerLogColoriser::colortext($item->etype); ?>
               <td>
                  <?php echo IssueTrackerHelperDate::dateWithOffSet($item->date); ?>
               </td>
               <td>
                  <?php echo $item->message; ?>
               </td>
               <!-- td>
                  <?php echo $item->category; ?>
               </td -->

               <?php if (isset($this->items[0]->ordering)) { ?>
                  <td class="order">
                     <?php if ($canChange) : ?>
                        <?php if ($saveOrder) :?>
                           <?php if ($listDirn == 'asc') : ?>
                              <span><?php echo $this->pagination->orderUpIcon($i, true, 'itloglist.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                              <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itloglist.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                           <?php elseif ($listDirn == 'desc') : ?>
                              <span><?php echo $this->pagination->orderUpIcon($i, true, 'itloglist.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                              <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'itloglist.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                           <?php endif; ?>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                        <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                     <?php else : ?>
                        <?php echo $item->ordering; ?>
                     <?php endif; ?>
                  </td>
               <?php } ?>
               <?php if (isset($this->items[0]->id)) { ?>
                  <td class="center">
                     <?php echo (int) $item->id; ?>
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
