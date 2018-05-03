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
defined('_JEXEC') or die('Restricted access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$ordering   = ($listOrder == 'a.lft');
$saveOrder  = ($listOrder == 'a.lft' && $listDirn == 'asc');
$show_assigned = $this->state->params->get('show_assigned_to_headings');

?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=itprojectslist'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
   <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
   </div>
   <div id="j-main-container" class="span10">
<?php else : ?>
   <div id="j-main-container">
<?php endif;?>   <fieldset id="filter-bar">
      <div class="filter-search fltlft">
         <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
         <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
         <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
         <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
      </div>
      <div class="filter-select fltrt">
         <select name="filter_level" class="inputbox" onchange="this.form.submit()">
            <option value=""><?php echo JText::_('JOPTION_SELECT_MAX_LEVELS');?></option>
            <?php echo JHtml::_('select.options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'));?>
         </select>

         <select name="filter_state" class="inputbox" onchange="this.form.submit()">
            <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
            <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
         </select>

      </div>
   </fieldset>
   <div class="clr"> </div>

   <table class="adminlist">
      <thead>
         <tr>
            <th style="width:1%">
               <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  JText::_('COM_ISSUETRACKER_PROJECT_NAME'), 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  JText::_('COM_ISSUETRACKER_PROJECT_DESCRIPTION'), 'a.description', $listDirn, $listOrder); ?>
            </th>

            <?php if ($show_assigned == 1 ) : ?>
                <th class='left'>
                   <?php echo JHTML::_('grid.sort', JText::_( 'COM_ISSUETRACKER_ASSIGNED_PERSON' ), 'a.assignee', $listDirn, $listOrder); ?>
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

            <th style="width:10%">
               <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
               <?php if ($canOrder && $saveOrder) :?>
                  <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'itprojectslist.saveorder'); ?>
               <?php endif; ?>
            </th>

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
      <?php
         $originalOrders = array();
         foreach ($this->items as $i => $item) :
         $orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
         $canCreate  = $user->authorise('core.create',      'com_issuetracker');
         $canEdit    = $user->authorise('core.edit',        'com_issuetracker');
         $canCheckin = $user->authorise('core.manage',      'com_issuetracker');
         $canChange  = $user->authorise('core.edit.state',  'com_issuetracker');
         ?>
         <tr class="row<?php echo $i % 2; ?>">
            <td class="center">
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
                  <?php echo JHtml::_('jgrid.published', $item->state, $i, 'itprojectslist.', $canChange, 'cb'); ?>
               </td>
            <?php } ?>

            <td class="order">
               <?php if ($canChange) : ?>
                  <?php if ($saveOrder) :?>
                     <span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'itprojectslist.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                     <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'itprojectslist.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                  <?php endif; ?>
                  <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                      <input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
                      <?php $originalOrders[] = $orderkey + 1; ?>
               <?php else : ?>
                  <?php echo $orderkey + 1; ?>
               <?php endif; ?>
            </td>

            <?php if (isset($this->items[0]->id)) { ?>
               <td class="center">
                  <?php echo (int) $item->id; ?>
               </td>
            <?php } ?>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>

   <div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>
